<?php

namespace App\Models;

use App\Models\DeploymentData\EnvVar;
use App\Models\DeploymentData\NodePort;
use App\Models\DeploymentData\Volume;
use App\Models\NodeTasks\CreateConfig\CreateConfigMeta;
use App\Models\NodeTasks\CreateSecret\CreateSecretMeta;
use App\Models\NodeTasks\CreateService\CreateServiceMeta;
use App\Traits\HasOwningTeam;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function makeResourceName($name): string
    {
        return $this->service->makeResourceName("dpl_". $this->id . "_" . $name);
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
                        'Labels' => [
                            ...$labels,
                            "content.hash" => md5($configFile->content),
                        ],
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
                        'Labels' => [
                            ...$labels,
                            "content.hash" => md5($secretFile->content),
                        ],
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
                        'Labels' => [
                            ...$labels,
                            "content.hash" => $secretEnvVarsHash,
                        ],
                    ],
                ],
            ];
        }

        return [
            ...$configTasks,
            ...$secretTasks,
            ...$secretVarsTasks,
            [
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
                                    'Target' => Network::find($data->networkId)->docker_id,
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
            ]
        ];
    }

    protected function getNodeDockerId($nodeId)
    {
        return Node::find($nodeId)->docker_id;
    }
}
