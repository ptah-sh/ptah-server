<?php

namespace App\Actions\Nodes;

use App\Models\Deployment;
use App\Models\DeploymentData\Caddy;
use App\Models\DeploymentData\EnvVar;
use App\Models\DeploymentData\Process;
use App\Models\NodeTaskGroup;
use App\Models\NodeTasks\ApplyCaddyConfig\ApplyCaddyConfigMeta;
use App\Models\NodeTaskType;
use App\Models\Team;
use InvalidArgumentException;
use Lorisleiva\Actions\Action;

class RebuildCaddy extends Action
{
    // TODO: make the deployment optional? Remove it altogether?
    public function handle(Team $team, NodeTaskGroup $taskGroup, Deployment $deployment)
    {
        $caddyHandlers = [];

        Deployment::latestDeployments($team)->each(function ($deployment) use (&$caddyHandlers) {
            foreach ($deployment->data->processes as $process) {
                $caddyHandlers[] = collect($process->caddy)->map(function (Caddy $caddy) use ($deployment, $process) {
                    $routes = [];

                    $handlers = [];

                    $pathRegexps = [];
                    foreach ($process->rewriteRules as $rewriteRule) {
                        $pathRegexps[] = [
                            'find' => $rewriteRule->pathFrom,
                            'replace' => $rewriteRule->pathTo,
                        ];
                    }

                    if (! empty($pathRegexps)) {
                        $handlers[] = [
                            'handler' => 'rewrite',
                            'path_regexp' => $pathRegexps,
                        ];
                    }

                    $handlers[] = [
                        'handler' => 'reverse_proxy',
                        'headers' => [
                            'request' => [
                                'set' => $this->getForwardedHeaders($caddy),
                            ],
                            'response' => [
                                'set' => [
                                    'X-Powered-By' => ['https://ptah.sh'],
                                    'X-Ptah-Rule-Id' => [$caddy->id],
                                ],
                            ],
                        ],
                        'transport' => $this->getTransportOptions($caddy, $process),
                        'upstreams' => [
                            [
                                'dial' => "{$process->name}.{$deployment->data->internalDomain}:{$caddy->targetPort}",
                            ],
                        ],
                    ];

                    $routes[] = [
                        'match' => [
                            [
                                'host' => [$caddy->domain],
                                'path' => [$caddy->path],
                            ],
                        ],
                        'handle' => $handlers,
                    ];

                    foreach ($process->redirectRules as $redirectRule) {
                        $regexpName = dockerize_name($redirectRule->id);

                        $pathTo = preg_replace("/\\$(\d+)/", "{http.regexp.$regexpName.$1}", $redirectRule->pathTo);

                        $routes[] = [
                            'match' => [
                                [
                                    'host' => [$redirectRule->domainFrom],
                                    'path_regexp' => [
                                        'name' => $regexpName,
                                        'pattern' => $redirectRule->pathFrom,
                                    ],
                                ],
                            ],
                            'handle' => [
                                [
                                    'handler' => 'static_response',
                                    'status_code' => (string) $redirectRule->statusCode,
                                    'headers' => [
                                        'X-Powered-By' => ['https://ptah.sh'],
                                        'X-Ptah-Rule-Id' => [$redirectRule->id],
                                        'Location' => ["{http.request.scheme}://{$redirectRule->domainTo}{$pathTo}"],
                                    ],
                                ],
                            ],
                        ];
                    }

                    return [
                        'apps' => [
                            'http' => [
                                'servers' => [
                                    "listen_{$caddy->publishedPort}" => [
                                        'listen' => [
                                            "0.0.0.0:{$caddy->publishedPort}",
                                        ],
                                        'routes' => $routes,
                                    ],
                                ],
                            ],
                        ],
                    ];
                })->toArray();
            }
        });

        $caddyHandlers = array_merge(...$caddyHandlers);

        $caddy = [
            'apps' => [
                'http' => [
                    'servers' => (object) [],
                ],
            ],
        ];

        foreach ($caddyHandlers as $handler) {
            $caddy = array_merge_recursive($caddy, $handler);
        }

        foreach ($caddy['apps']['http']['servers'] as $name => $value) {
            $caddy['apps']['http']['servers'][$name]['listen'] = array_unique($value['listen']);
            $caddy['apps']['http']['servers'][$name]['routes'] = $this->sortRoutes($value['routes']);

            $caddy['apps']['http']['servers'][$name]['routes'][] = [
                'match' => [
                    [
                        'host' => ['*'],
                        'path' => ['/*'],
                    ],
                ],
                'handle' => [
                    [
                        'handler' => 'static_response',
                        'status_code' => '404',
                        'headers' => [
                            'X-Powered-By' => ['https://ptah.sh'],
                            'Content-Type' => ['text/html; charset=utf-8'],
                        ],
                        'body' => file_get_contents(resource_path('support/caddy/404.html')),
                    ],
                ],
            ];
        }

        $taskGroup->tasks()->create([
            'type' => NodeTaskType::ApplyCaddyConfig,
            'meta' => ApplyCaddyConfigMeta::from([
                'deploymentId' => $deployment->id,
            ]),
            'payload' => [
                'caddy' => $caddy,
            ],
        ]);
    }

    protected function getTransportOptions(Caddy $caddy, Process $process): array
    {
        if ($caddy->targetProtocol === 'http') {
            return [
                'protocol' => 'http',
            ];
        }

        if ($caddy->targetProtocol === 'fastcgi') {
            return [
                'protocol' => 'fastcgi',
                'root' => $process->fastCgi->root,
                'env' => (object) collect($process->fastCgi->env)->reduce(fn ($carry, EnvVar $var) => [...$carry, $var->name => $var->value], []),
            ];
        }

        throw new InvalidArgumentException("Unsupported Caddy target protocol: {$caddy->targetProtocol}");
    }

    protected function getForwardedHeaders(Caddy $caddy): array
    {
        if ($caddy->publishedPort === 80) {
            return [
                'X-Forwarded-Proto' => ['http'],
                'X-Forwarded-Schema' => ['http'],
                'X-Forwarded-Host' => [$caddy->domain],
                'X-Forwarded-Port' => ['80'],
            ];
        }

        return [
            'X-Forwarded-Proto' => ['https'],
            'X-Forwarded-Schema' => ['https'],
            'X-Forwarded-Host' => [$caddy->domain],
            'X-Forwarded-Port' => ['443'],
        ];
    }

    protected function sortRoutes($routes): array
    {
        return collect($routes)
            ->sort(function (array $a, array $b) {
                $weightsA = $this->getRouteWeights($a);
                $weightsB = $this->getRouteWeights($b);

                $segmentsResult = $weightsA['segments'] <=> $weightsB['segments'];
                if ($segmentsResult === 0) {
                    return $weightsA['wildcards'] <=> $weightsB['wildcards'];
                }

                return $segmentsResult;
            })
            ->values()
            ->toArray();
    }

    protected function getRouteWeights($route): array
    {
        // Let's prioritize regexp routes to be first to execute
        if (isset($route['match'][0]['path_regexp'])) {
            return [
                'segments' => -100,
                'wildcards' => 100,
            ];
        }

        $path = $route['match'][0]['path'][0];

        $pathSegments = count(explode('/', $path)) * -1;
        $wildcardSegments = count(explode('*', $path));

        return [
            'segments' => $pathSegments,
            'wildcards' => $wildcardSegments,
        ];
    }
}
