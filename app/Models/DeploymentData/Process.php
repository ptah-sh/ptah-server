<?php

namespace App\Models\DeploymentData;

use App\Models\Deployment;
use App\Models\Node;
use App\Models\NodeTasks\CreateConfig\CreateConfigMeta;
use App\Models\NodeTasks\CreateSecret\CreateSecretMeta;
use App\Models\NodeTasks\DeleteService\DeleteServiceMeta;
use App\Models\NodeTasks\LaunchService\LaunchServiceMeta;
use App\Models\NodeTasks\PullDockerImage\PullDockerImageMeta;
use App\Models\NodeTaskType;
use App\Rules\RequiredIfArrayHas;
use App\Util\ResourceId;
use Exception;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\Validation\Enum;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\RequiredWith;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;

class Process extends Data
{
    public function __construct(
        public string $name,
        #[Exists(Node::class, 'id')]
        #[RequiredWith('volumes')]
        public ?int $placementNodeId,
        public ?string $dockerName,
        public ?string $dockerRegistryId,
        public string $dockerImage,
        public ReleaseCommand $releaseCommand,
        public ?string $command,
        public Healthcheck $healthcheck,
        #[DataCollectionOf(ProcessBackup::class)]
        /* @var ProcessBackup[] */
        public array $backups,
        #[DataCollectionOf(Worker::class)]
        /* @var Worker[] */
        public array $workers,
        #[Enum(LaunchMode::class)]
        public string $launchMode,
        #[DataCollectionOf(EnvVar::class)]
        /* @var EnvVar[] */
        public array $envVars,
        #[DataCollectionOf(SecretVar::class)]
        /* @var SecretVar[] */
        public array $secretVars,
        #[DataCollectionOf(ConfigFile::class)]
        /* @var ConfigFile[] */
        public array $configFiles,
        #[DataCollectionOf(SecretFile::class)]
        /* @var SecretFile[] */
        public array $secretFiles,
        #[DataCollectionOf(Volume::class)]
        /* @var Volume[] */
        public array $volumes,
        public ?BackupVolume $backupVolume,
        public int $replicas,
        #[DataCollectionOf(NodePort::class)]
        /* @var NodePort[] */
        public array $ports,
        #[DataCollectionOf(Caddy::class)]
        /* @var Caddy[] */
        public array $caddy,
        #[Rule(new RequiredIfArrayHas('caddy.*.targetProtocol', 'fastcgi'))]
        public ?FastCgi $fastCgi,
        #[DataCollectionOf(RedirectRule::class)]
        /* @var RedirectRule[] */
        public array $redirectRules,
        #[DataCollectionOf(RewriteRule::class)]
        /* @var RewriteRule[] */
        public array $rewriteRules
    ) {}

    public function findVolume(string $id): ?Volume
    {
        return collect($this->volumes)->first(fn (Volume $volume) => $volume->id === $id);
    }

    public function findProcessBackup(string $id): ?ProcessBackup
    {
        return collect($this->backups)->first(fn (ProcessBackup $backup) => $backup->id === $id);
    }

    public function findConfigFile(string $path): ?ConfigFile
    {
        return collect($this->configFiles)->first(fn (ConfigFile $file) => $file->path === $path);
    }

    public function findSecretFile(string $path): ?SecretFile
    {
        return collect($this->secretFiles)->first(fn (SecretFile $file) => $file->path === $path);
    }

    public function findSecretVar(string $name): ?SecretVar
    {
        return collect($this->secretVars)->first(fn (SecretVar $var) => $var->name === $name);
    }

    /**
     * @throws Exception
     */
    public function asNodeTasks(Deployment $deployment): array
    {
        if (empty($this->dockerName)) {
            $this->dockerName = dockerize_name($deployment->service->docker_name.'_'.$this->name);
        }

        $labels = $deployment->resourceLabels();
        $previous = $deployment->previousDeployment()?->findProcess($this->dockerName);

        $tasks = [];

        $previousWorkers = $previous?->workers ?? [];
        foreach ($previousWorkers as $worker) {
            if ($this->findWorker($worker->dockerName) === null) {
                $tasks[] = [
                    'type' => NodeTaskType::DeleteService,
                    'meta' => new DeleteServiceMeta($deployment->service_id, $worker->dockerName, $deployment->service->name),
                    'payload' => [
                        'ServiceName' => $worker->dockerName,
                    ],
                ];
            }
        }

        foreach ($this->workers as $worker) {
            if (! $worker->dockerName) {
                // TODO: add validation - allow only unique worker commands
                $worker->dockerName = $this->makeResourceName('wkr_'.$worker->name);
            }
        }

        foreach ($this->configFiles as $configFile) {
            $previousConfig = $previous?->findConfigFile($configFile->path);
            if ($previousConfig && $configFile->sameAs($previousConfig)) {
                $configFile->dockerName = $previousConfig->dockerName;

                continue;
            }

            $configFile->dockerName = $this->makeResourceName('dpl_'.$deployment->id.'_cfg_'.$configFile->path);

            $tasks[] = [
                'type' => NodeTaskType::CreateConfig,
                'meta' => CreateConfigMeta::from([
                    'deploymentId' => $deployment->id,
                    'processName' => $this->dockerName,
                    'path' => $configFile->path,
                ]),
                'payload' => [
                    'SwarmConfigSpec' => [
                        'Name' => $configFile->dockerName,
                        'Data' => $configFile->base64(),
                        'Labels' => dockerize_labels([
                            ...$labels,
                            'kind' => 'config',
                        ]),
                    ],
                ],
            ];
        }

        foreach ($this->secretFiles as $secretFile) {
            $previousSecret = $previous?->findSecretFile($secretFile->path);
            if ($secretFile->sameAs($previousSecret)) {
                $secretFile->content = $previousSecret->content;

                continue;
            }

            $secretFile->dockerName = $this->makeResourceName('dpl_'.$deployment->id.'_secret_'.$secretFile->path);

            $tasks[] = [
                'type' => NodeTaskType::CreateSecret,
                'meta' => CreateSecretMeta::from([
                    'deploymentId' => $deployment->id,
                    'processName' => $this->dockerName,
                    'path' => $secretFile->path,
                ]),
                'payload' => [
                    'SwarmSecretSpec' => [
                        'Name' => $secretFile->dockerName,
                        'Data' => $secretFile->base64(),
                        'Labels' => dockerize_labels([
                            ...$labels,
                            'kind' => 'secret',
                        ]),
                    ],
                ],
            ];
        }

        foreach ($this->volumes as $volume) {
            if (! $volume->dockerName) {
                $volume->dockerName = $this->makeResourceName($volume->name);
            }
        }

        $internalDomain = "{$this->name}.{$deployment->data->internalDomain}";

        $command = null;
        $args = null;

        if ($this->command) {
            // FIXME: use smarter CLI split - need to handle values with spaces, surrounded by the double quotes
            $splitCmd = explode(' ', $this->command);

            $command = [$splitCmd[0]];
            $args = array_slice($splitCmd, 1);
        }

        $dockerRegistry = $this->dockerRegistryId
            ? $deployment->service->swarm->data->findRegistry($this->dockerRegistryId)
            : null;

        if ($this->dockerRegistryId && is_null($dockerRegistry)) {
            throw new Exception("Docker registry '{$this->dockerRegistryId}' not found");
        }

        $authConfigName = $dockerRegistry
            ? $dockerRegistry->dockerName
            : '';

        $tasks[] = [
            'type' => NodeTaskType::PullDockerImage,
            'meta' => PullDockerImageMeta::from([
                'deploymentId' => $deployment->id,
                'processName' => $this->dockerName,
                'serviceId' => $deployment->service_id,
                'serviceName' => $deployment->service->name,
                'dockerImage' => $this->dockerImage,
            ]),
            'payload' => [
                'AuthConfigName' => $authConfigName,
                'Image' => $this->dockerImage,
                'PullOptions' => (object) [],
            ],
        ];

        $serviceTaskMeta = [
            'deploymentId' => $deployment->id,
            'dockerName' => $this->dockerName,
            'serviceId' => $deployment->service_id,
            'serviceName' => $deployment->service->name,
        ];

        $volumes = $this->volumes;

        $mounts = collect($volumes)
            ->map(fn (Volume $volume) => [
                'Type' => 'volume',
                'Source' => $volume->dockerName,
                'Target' => $volume->path,
                'VolumeOptions' => [
                    'Labels' => dockerize_labels([
                        'id' => $volume->id,
                        ...$labels,
                    ]),
                ],
            ])
            ->toArray();

        // TODO: if (has volumes with backups enabled OR has a Backup Script defined)
        if (count($this->volumes)) {
            if ($this->backupVolume == null) {
                $this->backupVolume = BackupVolume::validateAndCreate([
                    'id' => ResourceId::make('volume'),
                    'name' => 'backups',
                    'dockerName' => $this->makeResourceName('/ptah/backups'),
                    'path' => '/ptah/backups',
                ]);
            }

            $mounts[] = [
                'Type' => 'volume',
                'Source' => $this->backupVolume->dockerName,
                'Target' => $this->backupVolume->path,
                'VolumeOptions' => [
                    'Labels' => dockerize_labels([
                        'id' => $this->backupVolume->id,
                        ...$labels,
                    ]),
                ],
            ];
        }

        $envVars = $this->envVars;
        $envVars[] = EnvVar::validateAndCreate([
            'name' => 'PTAH_HOSTNAME',
            'value' => $internalDomain,
        ]);

        $serviceSecretVars = $this->getSecretVars($previous);

        $tasks[] = [
            'type' => NodeTaskType::LaunchService,
            'meta' => LaunchServiceMeta::from($serviceTaskMeta),
            'payload' => [
                'AuthConfigName' => $authConfigName,
                'ReleaseCommand' => $this->getReleaseCommandPayload($deployment, $labels),
                'SecretVars' => $serviceSecretVars,
                'SwarmServiceSpec' => [
                    'Name' => $this->dockerName,
                    'Labels' => $labels,
                    'TaskTemplate' => [
                        'ContainerSpec' => [
                            'Image' => $this->dockerImage,
                            'Labels' => $labels,
                            'Command' => $command,
                            'Args' => $args,
                            'Hostname' => "dpl-{$deployment->id}.{$internalDomain}",
                            'Env' => collect($envVars)->map(fn (EnvVar $var) => "{$var->name}={$var->value}")->toArray(),
                            'Mounts' => $mounts,
                            'Hosts' => [
                                $internalDomain,
                            ],
                            'Secrets' => collect($this->secretFiles)->map(fn (SecretFile $secretFile) => [
                                'File' => [
                                    'Name' => $secretFile->path,
                                    // TODO: figure out better permissions settings (if any)
                                    'UID' => '0',
                                    'GID' => '0',
                                    'Mode' => 0777,
                                ],
                                'SecretName' => $secretFile->dockerName,
                            ])->values()->toArray(),
                            'Configs' => collect($this->configFiles)->map(fn (ConfigFile $configFile) => [
                                'File' => [
                                    'Name' => $configFile->path,
                                    // TODO: figure out better permissions settings (if any)
                                    'UID' => '0',
                                    'GID' => '0',
                                    'Mode' => 0777,
                                ],
                                'ConfigName' => $configFile->dockerName,
                            ])->values()->toArray(),
                            'Placement' => $this->placementNodeId ? [
                                'Constraints' => [
                                    "node.labels.sh.ptah.node.id=={$this->placementNodeId}",
                                ],
                            ] : [],
                            'HealthCheck' => $this->healthcheck->command ? [
                                'Test' => ['CMD-SHELL', $this->healthcheck->command],
                                'Interval' => $this->healthcheck->interval * 1000000000, // Convert to nanoseconds
                                'Timeout' => $this->healthcheck->timeout * 1000000000, // Convert to nanoseconds
                                'Retries' => $this->healthcheck->retries,
                                'StartPeriod' => $this->healthcheck->startPeriod * 1000000000, // Convert to nanoseconds
                                'StartInterval' => $this->healthcheck->startInterval * 1000000000, // Convert to nanoseconds
                            ] : null,
                        ],
                        'Networks' => [
                            [
                                'Target' => $deployment->data->networkName,
                                'Aliases' => [$internalDomain],
                            ],
                        ],
                    ],
                    'Mode' => [
                        'Replicated' => [
                            'Replicas' => $this->replicas,
                        ],
                    ],
                    'EndpointSpec' => [
                        'Ports' => collect($this->ports)->map(fn (NodePort $port) => [
                            'Protocol' => 'tcp',
                            'TargetPort' => $port->targetPort,
                            'PublishedPort' => $port->publishedPort,
                            'PublishMode' => 'ingress',
                        ])->toArray(),
                    ],
                ],
            ],
        ];

        foreach ($this->workers as $worker) {
            $workerTaskMeta = [
                ...$serviceTaskMeta,
                'dockerName' => $worker->dockerName,
            ];

            $tasks[] = [
                'type' => NodeTaskType::LaunchService,
                'meta' => LaunchServiceMeta::from($workerTaskMeta),
                'payload' => [
                    'AuthConfigName' => $authConfigName,
                    'ReleaseCommand' => (object) [],
                    'SecretVars' => $serviceSecretVars,
                    'SwarmServiceSpec' => [
                        'Name' => $worker->dockerName,
                        'Labels' => $labels,
                        'TaskTemplate' => [
                            'ContainerSpec' => [
                                'Image' => $this->dockerImage,
                                'Labels' => $labels,
                                'Command' => ['sh', '-c'],
                                'Args' => [
                                    $worker->command,
                                ],
                                'Hostname' => "dpl-{$deployment->id}.{$worker->name}.{$internalDomain}",
                                'Env' => collect($this->envVars)->map(fn (EnvVar $var) => "{$var->name}={$var->value}")->toArray(),
                                'Mounts' => [],
                                'HealthCheck' => [
                                    'Test' => ['NONE'],
                                ],
                                'Hosts' => [
                                    "{$worker->name}.{$internalDomain}",
                                ],
                                'Secrets' => collect($this->secretFiles)->map(fn (SecretFile $secretFile) => [
                                    'File' => [
                                        'Name' => $secretFile->path,
                                        // TODO: figure out better permissions settings (if any)
                                        'UID' => '0',
                                        'GID' => '0',
                                        'Mode' => 0777,
                                    ],
                                    'SecretName' => $secretFile->dockerName,
                                ])->values()->toArray(),
                                'Configs' => collect($this->configFiles)->map(fn (ConfigFile $configFile) => [
                                    'File' => [
                                        'Name' => $configFile->path,
                                        // TODO: figure out better permissions settings (if any)
                                        'UID' => '0',
                                        'GID' => '0',
                                        'Mode' => 0777,
                                    ],
                                    'ConfigName' => $configFile->dockerName,
                                ])->values()->toArray(),
                                'Placement' => [],
                            ],
                            'Networks' => [
                                [
                                    'Target' => $deployment->data->networkName,
                                    'Aliases' => [
                                        "{$worker->name}.{$internalDomain}",
                                    ],
                                ],
                            ],
                        ],
                        'Mode' => [
                            'Replicated' => [
                                'Replicas' => $worker->replicas,
                            ],
                        ],
                        'UpdateConfig' => [
                            'Parallelism' => 1,
                        ],
                        'EndpointSpec' => [
                            'Ports' => [],
                        ],
                    ],
                ],
            ];
        }

        return $tasks;
    }

    protected function getSecretVars(?Process $previous): object
    {
        return (object) collect($this->secretVars)
            ->reduce(function ($carry, SecretVar $var) use ($previous) {
                $prevVar = $previous?->findSecretVar($var->name);

                $carry[$var->name] = ($var->sameAs($prevVar) ? $prevVar->value : $var->value) ?? '';

                return $carry;
            }, []);
    }

    public function makeResourceName(string $name): string
    {
        return dockerize_name($this->dockerName.'_'.$name);
    }

    private function getReleaseCommandPayload(Deployment $deployment, array $labels): array
    {
        if (! $this->releaseCommand->command) {
            return [
                'ConfigName' => '',
                'ConfigLabels' => (object) [],
                'Command' => '',
            ];
        }

        // Always create a new config, as the command may be the same, but the image/entrypoint may be different.
        $this->releaseCommand->dockerName = $deployment->makeResourceName('release_command');

        return [
            'ConfigName' => $this->releaseCommand->dockerName,
            'ConfigLabels' => dockerize_labels([
                ...$labels,
                'kind' => 'release-command',
            ]),
            'Command' => $this->releaseCommand->command,
        ];
    }

    private function findWorker(?string $dockerName): ?Worker
    {
        if (! $dockerName) {
            return null;
        }

        return collect($this->workers)->first(fn (Worker $worker) => $worker->dockerName === $dockerName);
    }
}
