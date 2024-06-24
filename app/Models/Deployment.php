<?php

namespace App\Models;

use App\Models\DeploymentData\Caddy;
use App\Models\DeploymentData\EnvVar;
use App\Models\DeploymentData\NodePort;
use App\Models\DeploymentData\Volume;
use App\Models\NodeTasks\CreateConfig\CreateConfigMeta;
use App\Models\NodeTasks\CreateSecret\CreateSecretMeta;
use App\Models\NodeTasks\CreateService\CreateServiceMeta;
use App\Models\NodeTasks\ApplyCaddyConfig\ApplyCaddyConfigMeta;
use App\Traits\HasOwningTeam;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use InvalidArgumentException;

class Deployment extends Model
{
    use HasFactory;
    use HasOwningTeam;

    protected $fillable = [
        'task_group_id',
        'data',
    ];

    protected $casts = [
        'data' => DeploymentData::class,
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function taskGroup(): BelongsTo
    {
        return $this->belongsTo(NodeTaskGroup::class);
    }

    public function makeResourceName($name): string
    {
        return $this->service->makeResourceName("dpl_". $this->id . "_" . $name);
    }

    public function scopeLatestDeployments(EloquentBuilder $query): EloquentBuilder
    {
        return $query->whereIn('id', function (QueryBuilder $query) {
            $query
                ->selectRaw('max("latest_deployments_query"."id")')
                ->from('deployments', 'latest_deployments_query')
                ->groupBy('latest_deployments_query.service_id');
        });
    }

    public function asNodeTasks(): array
    {
        /* @var DeploymentData $data */
        $data = $this->data;

        $labels = dockerize_labels([
            'service.id' => $this->service_id,
            'deployment.id' => $this->id,
        ]);

        $configTasks = [];
        foreach ($data->configFiles as $configFile) {
            $configTasks[] = [
                'type' => NodeTaskType::CreateConfig,
                'meta' => CreateConfigMeta::from([
                    'deploymentId' => $this->id,
                    'path' => $configFile->path,
                    'hash' => md5($configFile->content),
                ]),
                'payload' => [
                    'SwarmConfigSpec' => [
                        'Name' => $this->makeResourceName('cfg_' . $configFile->path),
                        'Data' => base64_encode($configFile->content),
                        'Labels' => dockerize_labels([
                            ...$labels,
                            "content.hash" => md5($configFile->content),
                        ]),
                    ],
                ],
            ];
        }

        $secretFiles = $data->secretFiles;
        $secretTasks = [];
        foreach ($secretFiles as $secretFile) {
            $secretTasks[] = [
                'type' => NodeTaskType::CreateSecret,
                'meta' => CreateSecretMeta::from([
                    'deploymentId' => $this->id,
                    'path' => $secretFile->path,
                    'hash' => md5($secretFile->content),
                ]),
                'payload' => [
                    'SwarmSecretSpec' => [
                        'Name' => $this->makeResourceName($secretFile->path),
                        'Data' => base64_encode($secretFile->content),
                        'Labels' => dockerize_labels([
                            ...$labels,
                            "content.hash" => md5($secretFile->content),
                        ]),
                    ]
                ]
            ];
        }

        $secretVarsTasks = [];
        $secretVarsConfigName = $this->makeResourceName('secrets');
        if (count($data->secretVars)) {
            $secretEnvVars = collect($data->secretVars)->map(fn (EnvVar $var) => "{$var->name}={$var->value}")->toJson();
            $secretEnvVarsHash = md5($secretEnvVars);
            $secretVarsTasks[] = [
                'type' => NodeTaskType::CreateConfig,
                'meta' => CreateConfigMeta::from([
                    'deploymentId' => $this->id,
                    'path' => 'secret env vars',
                    'hash' => $secretEnvVarsHash,
                ]),
                'payload' => [
                    'SwarmConfigSpec' => [
                        'Name' => $secretVarsConfigName,
                        'Data' => base64_encode($secretEnvVars),
                        'Labels' => dockerize_labels([
                            ...$labels,
                            "content.hash" => $secretEnvVarsHash,
                        ]),
                    ],
                ],
            ];
        }

        $services = [];

        $services[] = [
            'type' => NodeTaskType::CreateService,
            'meta' => CreateServiceMeta::from([
                'deploymentId' => $this->id,
                'serviceId' => $this->service_id,
                'serviceName' => $this->service->name,
            ]),
            'payload' => [
//                    'AuthConfigName' => "",
                'SecretVarsConfigName' => count($secretVarsTasks) ? $secretVarsConfigName : "",
                'SwarmServiceSpec' => [
                    'Name' => $this->service->makeResourceName('service'),
                    'Labels' => $labels,
                    'TaskTemplate' => [
                        'ContainerSpec' => [
                            'Image' => $data->dockerImage,
                            'Labels' => $labels,
                            'Hostname' => "dpl-{$this->id}.{$data->internalDomain}",
                            'Env' => collect($data->envVars)->map(fn(EnvVar $var) => "{$var->name}={$var->value}")->toArray(),
                            'Mounts' => collect($data->volumes)->map(fn(Volume $volume) => [
                                'Type' => 'volume',
                                'Source' => $volume->name,
                                'Target' => $volume->path,
                                'VolumeOptions' => [
                                    'Labels' => $labels,
                                ]
                            ])->toArray(),
                            'Hosts' => [
                                $data->internalDomain,
                            ],
                            'Secrets' => collect($secretTasks)->map(fn($task) => [
                                'File' => [
                                    'Name' => $task['meta']->path,
                                    // TODO: figure out better permissions settings (if any)
                                    'UID' => "0",
                                    "GID" => "0",
                                    "Mode" => 0777
                                ],
                                'SecretName' => $task['payload']['SwarmSecretSpec']['Name'],
                            ]),
                            'Configs' => collect($configTasks)->map(fn($task) => [
                                'File' => [
                                    'Name' => $task['meta']->path,
                                    // TODO: figure out better permissions settings (if any)
                                    'UID' => "0",
                                    "GID" => "0",
                                    "Mode" => 0777
                                ],
                                'ConfigName' => $task['payload']['SwarmConfigSpec']['Name'],
                            ]),
                            'Placement' => $data->placementNodeId ? [
                                'Constraints' => [
                                    "node.id=={$this->getNodeDockerId($data->placementNodeId)}",
                                ]
                            ] : [],
                        ],
                        'Networks' => [
                            [
                                'Target' => $data->networkName,
                                'Aliases' => [$data->internalDomain],
                            ]
                        ],
                    ],
                    'Mode' => [
                        'Replicated' => [
                            'Replicas' => $data->replicas,
                        ],
                    ],
                    'EndpointSpec' => [
                        'Ports' => collect($data->ports)->map(fn(NodePort $port) => [
                            'Protocol' => 'tcp',
                            'TargetPort' => $port->targetPort,
                            'PublishedPort' => $port->publishedPort,
                            'PublishMode' => 'ingress',
                        ])->toArray()
                    ]
                ]
            ],
        ];

        $caddyHandlers = [];
        $this->latestDeployments()->each(function ($deployment) use (&$caddyHandlers) {
            $caddyHandlers[] = collect($deployment->data->caddy)->map(fn(Caddy $caddy) => [
                'apps' =>[
                    'http' => [
                        'servers' => [
                            "listen_{$caddy->publishedPort}" => [
                                'listen' => [
                                    "0.0.0.0:{$caddy->publishedPort}",
                                ],
                                'routes' => [
                                    [
                                        'match' => [
                                            [
                                                'host' => [$caddy->domain],
                                                'path' => [$caddy->path],
                                            ],
                                        ],
                                        'handle' => [
                                            [
                                                'handler' => 'reverse_proxy',
                                                'headers' => [
                                                    'request' => [
                                                        'set' => $this->getForwardedHeaders($caddy),
                                                    ],
                                                    'response' => [
                                                        'set' => [
                                                            'X-Powered-By' => ['https://ptah.sh'],
                                                        ],
                                                    ],
                                                ],
                                                'transport' => $this->getTransportOptions($caddy),
                                                'upstreams' => [
                                                    [
                                                        'dial' => "{$deployment->data->internalDomain}:{$caddy->targetPort}",
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ],
                                ]
                            ]
                        ]
                    ]
                ]
            ])->toArray();
        });

        // Flatten array
        $caddyHandlers = array_merge(...$caddyHandlers);

        $caddy = [];
        foreach ($caddyHandlers as $handler) {
            $caddy = array_merge_recursive($caddy, $handler);
        }

        foreach ($caddy['apps']['http']['servers'] as $name => $value) {
            $caddy['apps']['http']['servers'][$name]['listen'] = array_unique($value['listen']);
            $caddy['apps']['http']['servers'][$name]['routes'] = $this->sortRoutes($value['routes']);
        }

        $caddyTask = [];
        $caddyTask[] = [
            'type' => NodeTaskType::ApplyCaddyConfig,
            'meta' => ApplyCaddyConfigMeta::from([
                'deploymentId' => $this->id,
            ]),
            'payload' => [
                'caddy' => $caddy,
            ]
        ];

        return [
            ...$configTasks,
            ...$secretTasks,
            ...$secretVarsTasks,
            ...$services,
            ...$caddyTask,
        ];
    }

    protected function getTransportOptions(Caddy $caddy): array
    {
        if ($caddy->targetProtocol === 'http') {
            return [
                'protocol' => 'http',
            ];
        }

        if ($caddy->targetProtocol === 'fastcgi') {
            return [
                'protocol' => 'fastcgi',
                'root' => $caddy->fastCgi->root,
                'env' => collect($caddy->fastcgiVars)->reduce(fn ($carry, EnvVar $var) => [...$carry, $var->name => $var->value], []),
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
        $path = $route['match'][0]['path'][0];

        $pathSegments = count(explode('/', $path)) * -1;
        $wildcardSegments = count(explode('*', $path));

        return [
            'segments' => $pathSegments,
            'wildcards' => $wildcardSegments,
        ];
    }

    // TODO: update node labels and select by the labels instead of the id
    protected function getNodeDockerId($nodeId)
    {
        return Node::find($nodeId)->docker_id;
    }
}
