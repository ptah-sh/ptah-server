<?php

namespace App\Models;

use App\Models\DeploymentData\Caddy;
use App\Models\DeploymentData\ConfigFile;
use App\Models\DeploymentData\EnvVar;
use App\Models\DeploymentData\NodePort;
use App\Models\DeploymentData\Volume;
use App\Models\NodeTasks\CreateConfig\CreateConfigMeta;
use App\Models\NodeTasks\CreateSecret\CreateSecretMeta;
use App\Models\NodeTasks\CreateService\CreateServiceMeta;
use App\Models\NodeTasks\ApplyCaddyConfig\ApplyCaddyConfigMeta;
use App\Traits\HasOwningTeam;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
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

    public function taskGroups(): HasManyThrough
    {
        return $this->hasManyThrough(NodeTaskGroup::class, NodeTask::class,  'meta__deployment_id', 'id', 'id', 'task_group_id')->orderByDesc('id');
    }

    public function latestTaskGroup(): HasOneThrough
    {
        return $this->hasOneThrough(NodeTaskGroup::class, NodeTask::class,  'meta__deployment_id', 'id', 'id', 'task_group_id')->latest();
    }

    public function previousDeployment(): ?Deployment
    {
        return $this->service->deployments()->where('id', '<', $this->id)->latest('id')->get()->first();
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
            $previousConfig = $this->previousDeployment()?->data->findConfigFile($configFile->path);
            if ($previousConfig && $configFile->sameAs($previousConfig)) {
                $configFile->dockerName = $previousConfig->dockerName;

                continue;
            }

            $configFile->dockerName = $this->makeResourceName('cfg_' . $configFile->path);

            $configTasks[] = [
                'type' => NodeTaskType::CreateConfig,
                'meta' => CreateConfigMeta::from([
                    'deploymentId' => $this->id,
                    'path' => $configFile->path,
                    'hash' => $configFile->hash(),
                ]),
                'payload' => [
                    'SwarmConfigSpec' => [
                        'Name' => $configFile->dockerName,
                        'Data' => $configFile->base64(),
                        'Labels' => dockerize_labels([
                            ...$labels,
                            'kind' => 'config',
                            "content.hash" => $configFile->hash(),
                        ]),
                    ],
                ],
            ];
        }

        $secretTasks = [];
        foreach ($this->data->secretFiles as $secretFile) {
            $previousSecret = $this->previousDeployment()?->data->findSecretFile($secretFile->path);
            if ($previousSecret && ($secretFile->content === null || $secretFile->sameAs($previousSecret))) {
                $secretFile->dockerName = $previousSecret->dockerName;

                continue;
            }

            $secretFile->dockerName = $this->makeResourceName($secretFile->path);

            $secretTasks[] = [
                'type' => NodeTaskType::CreateSecret,
                'meta' => CreateSecretMeta::from([
                    'deploymentId' => $this->id,
                    'path' => $secretFile->path,
                    'hash' => $secretFile->hash(),
                ]),
                'payload' => [
                    'SwarmSecretSpec' => [
                        'Name' => $secretFile->dockerName,
                        'Data' => $secretFile->base64(),
                        'Labels' => dockerize_labels([
                            ...$labels,
                            "content.hash" => $secretFile->hash(),
                        ]),
                    ]
                ]
            ];
        }

        $services = [];

        $services[] = [
            'type' => $this->previousDeployment() ? NodeTaskType::UpdateService : NodeTaskType::CreateService,
            'meta' => CreateServiceMeta::from([
                'deploymentId' => $this->id,
                'serviceId' => $this->service_id,
                'serviceName' => $this->service->name,
            ]),
            'payload' => [
//                    'AuthConfigName' => "",
                'SecretVars' => (object) $this->getSecretVars($labels),
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
                            'Secrets' => collect($this->data->secretFiles)->map(fn(ConfigFile $secretFile) => [
                                'File' => [
                                    'Name' => $secretFile->path,
                                    // TODO: figure out better permissions settings (if any)
                                    'UID' => "0",
                                    "GID" => "0",
                                    "Mode" => 0777
                                ],
                                'SecretName' => $secretFile->dockerName,
                            ])->values()->toArray(),
                            'Configs' => collect($this->data->configFiles)->map(fn(ConfigFile $configFile) => [
                                'File' => [
                                    'Name' => $configFile->path,
                                    // TODO: figure out better permissions settings (if any)
                                    'UID' => "0",
                                    "GID" => "0",
                                    "Mode" => 0777
                                ],
                                'ConfigName' => $configFile->dockerName,
                            ])->values()->toArray(),
                            'Placement' => $data->placementNodeId ? [
                                'Constraints' => [
                                    "node.labels.sh.ptah.node.id=={$data->placementNodeId}",
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

        $caddy = [
            'apps' => [
                'http' => [
                    'servers' => [],
                ]
            ]
        ];

        foreach ($caddyHandlers as $handler) {
            $caddy = array_merge_recursive($caddy, $handler);
        }

        foreach ($caddy['apps']['http']['servers'] as $name => $value) {
            $caddy['apps']['http']['servers'][$name]['listen'] = array_unique($value['listen']);
            $caddy['apps']['http']['servers'][$name]['routes'] = $this->sortRoutes($value['routes']);
        }

        $caddyTask = [];

        if (!empty($caddyHandlers)) {
            $caddyTask[] = [
                'type' => NodeTaskType::ApplyCaddyConfig,
                'meta' => ApplyCaddyConfigMeta::from([
                    'deploymentId' => $this->id,
                ]),
                'payload' => [
                    'caddy' => $caddy,
                ]
            ];
        }

        $this->saveQuietly();

        return [
            ...$configTasks,
            ...$secretTasks,
            ...$services,
            ...$caddyTask,
        ];
    }

    protected function getSecretVars($labels): array|object
    {
        if (empty($this->data->secretVars->vars)) {
            return (object) [];
        }

        $this->data->secretVars->dockerName = $this->makeResourceName('secret_vars');

        $data = [
            'ConfigName' => $this->data->secretVars->dockerName,
            'ConfigLabels' => dockerize_labels([
                ...$labels,
                'kind' => 'secret-env-vars',
            ]),
            'Values' => (object) collect($this->data->secretVars->vars)
                ->reject(fn(EnvVar $var) => $var->value === null)
                ->reduce(fn($carry, EnvVar $var) => [...$carry, $var->name => $var->value], []),
        ];

        if (!empty($this->previousDeployment()?->data->secretVars->dockerName)) {
            $data['Preserve'] = collect($this->data->secretVars->vars)
                ->filter(fn (EnvVar $var) => $var->value === null)
                ->map(fn (EnvVar $var) => $var->name)
                ->toArray();
            $data['PreserveFromConfig'] = $this->previousDeployment()->data->secretVars->dockerName;
        }

        return $data;
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
}
