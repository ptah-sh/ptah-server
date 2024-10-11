<?php

namespace App\Models\DeploymentData;

use App\Models\Deployment;
use App\Models\NodeTasks\LaunchService\LaunchServiceMeta;
use App\Models\NodeTasks\PullDockerImage\PullDockerImageMeta;
use App\Models\NodeTaskType;
use App\Rules\Crontab;
use Exception;
use Illuminate\Support\Str;
use Spatie\LaravelData\Attributes\Validation\Enum;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\ProhibitedUnless;
use Spatie\LaravelData\Attributes\Validation\RequiredIf;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;

class Worker extends Data
{
    public function __construct(
        public string $name,
        public ?string $dockerRegistryId,
        public string $dockerImage,
        public ?string $dockerName,
        public ?string $command,
        #[Min(0)]
        public int $replicas,
        #[Enum(LaunchMode::class)]
        public LaunchMode $launchMode,
        #[RequiredIf('launchMode', CRONJOB_LAUNCH_MODES), ProhibitedUnless('launchMode', CRONJOB_LAUNCH_MODES), Rule(new Crontab)]
        public ?string $crontab,
        public ReleaseCommand $releaseCommand,
        public Healthcheck $healthcheck,
        #[RequiredIf('launchMode', LaunchMode::BackupCreate), ProhibitedUnless('launchMode', LaunchMode::BackupCreate)]
        public ?BackupCreateOptions $backupCreate,
        #[RequiredIf('launchMode', LaunchMode::BackupRestore), ProhibitedUnless('launchMode', LaunchMode::BackupRestore)]
        public ?BackupRestoreOptions $backupRestore,
    ) {
        $maxReplicas = $this->launchMode->maxReplicas();
        if ($this->replicas > $maxReplicas) {
            $this->replicas = $maxReplicas;
        }

        $maxInitialReplicas = $this->launchMode->maxInitialReplicas();
        if ($this->replicas > $maxInitialReplicas) {
            $this->replicas = $maxInitialReplicas;
        }
    }

    public function asNodeTasks(Deployment $deployment, Process $process, bool $pullImage = true, ?int $desiredReplicas = null): array
    {
        [$command, $args] = $this->getCommandAndArgs();

        $internalDomain = $this->getInternalDomain($deployment, $process);

        $hostname = $this->getHostname($deployment, $process);

        $tasks = [];

        if (! $this->dockerName) {
            $this->dockerName = $process->makeResourceName('wkr_'.$this->name);
        }

        if ($this->launchMode->value === LaunchMode::BackupCreate->value && ! $this->backupCreate->backupVolume) {
            $dockerName = dockerize_name($this->dockerName.'_vol_ptah_backup');

            $this->backupCreate->backupVolume = Volume::validateAndCreate([
                'id' => 'volume-'.Str::random(11),
                'name' => $dockerName,
                'dockerName' => $dockerName,
                'path' => '/ptah/backup/create',
            ]);
        }

        if ($this->launchMode->value === LaunchMode::BackupRestore->value && ! $this->backupRestore->restoreVolume) {
            $dockerName = dockerize_name($this->dockerName.'_vol_ptah_restore');

            $this->backupRestore->restoreVolume = Volume::validateAndCreate([
                'id' => 'volume-'.Str::random(11),
                'name' => $dockerName,
                'dockerName' => $dockerName,
                'path' => '/ptah/backup/restore',
            ]);
        }

        $labels = dockerize_labels([
            ...$process->resourceLabels($deployment),
            'kind' => 'worker',
            // Cookie is used to filter out stale tasks for the same Docker Service on the Docker Engine's side
            //   and avoid transferring loads of data between Ptah.sh Agent and Docker Engine.
            'cookie' => Str::random(32),
            'worker.name' => $this->name,
        ]);

        if ($pullImage) {
            $tasks[] = $this->getPullImageTask($deployment);
        }

        $tasks[] = [
            'type' => NodeTaskType::LaunchService,
            'meta' => LaunchServiceMeta::from([
                'deploymentId' => $deployment->id,
                'serviceId' => $deployment->service_id,
                'serviceName' => $deployment->service->name,
                'dockerName' => $this->dockerName,
                'processName' => $process->name,
                'workerName' => $this->name,
            ]),
            'payload' => [
                'ReleaseCommand' => $this->getReleaseCommandPayload($process, $labels),
                'SecretVars' => $this->getSecretVars($process),
                'SwarmServiceSpec' => [
                    'Name' => $this->dockerName,
                    'Labels' => $labels,
                    'TaskTemplate' => [
                        'ContainerSpec' => [
                            'Image' => $this->dockerImage,
                            'Labels' => $labels,
                            'Command' => $command,
                            'Args' => $args,
                            'Hostname' => $hostname,
                            'Env' => collect($this->getEnvVars($deployment, $process))->map(fn (EnvVar $var) => "{$var->name}={$var->value}")->toArray(),
                            'Mounts' => $this->getMounts($deployment, $process, $labels),
                            'Hosts' => [
                                $internalDomain,
                            ],
                            'Secrets' => collect($process->secretFiles)->map(fn (SecretFile $secretFile) => [
                                'File' => [
                                    'Name' => $secretFile->path,
                                    // TODO: figure out better permissions settings (if any)
                                    'UID' => '0',
                                    'GID' => '0',
                                    'Mode' => 0777,
                                ],
                                'SecretName' => $secretFile->dockerName,
                            ])->values()->toArray(),
                            'Configs' => collect($process->configFiles)->map(fn (ConfigFile $configFile) => [
                                'File' => [
                                    'Name' => $configFile->path,
                                    // TODO: figure out better permissions settings (if any)
                                    'UID' => '0',
                                    'GID' => '0',
                                    'Mode' => 0777,
                                ],
                                'ConfigName' => $configFile->dockerName,
                            ])->values()->toArray(),
                            'Placement' => $process->placementNodeId ? [
                                'Constraints' => [
                                    "node.labels.sh.ptah.node.id=={$process->placementNodeId}",
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
                        'RestartPolicy' => $this->getRestartPolicy(),
                        'Networks' => [
                            [
                                'Target' => $deployment->data->networkName,
                                'Aliases' => [
                                    $internalDomain,
                                    $hostname,
                                ],
                            ],
                        ],
                    ],
                    'Mode' => $this->getSchedulingMode($desiredReplicas),
                    'EndpointSpec' => [
                        'Ports' => $this->getPorts($process),
                    ],
                ],
            ],
        ];

        return $tasks;
    }

    public function getPullImageTask(Deployment $deployment): array
    {
        $dockerRegistry = $this->dockerRegistryId
            ? $deployment->service->swarm->data->findRegistry($this->dockerRegistryId)
            : null;

        if ($this->dockerRegistryId && is_null($dockerRegistry)) {
            throw new Exception("Docker registry '{$this->dockerRegistryId}' not found");
        }

        $authConfigName = $dockerRegistry
            ? $dockerRegistry->dockerName
            : '';

        return [
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
    }

    private function getInternalDomain(Deployment $deployment, Process $process): string
    {
        $base = $process->getInternalDomain($deployment);
        if ($this->name === 'main') {
            return $base;
        }

        return "{$this->name}.{$base}";
    }

    private function getHostname(Deployment $deployment, Process $process): string
    {
        return "dpl-{$deployment->id}.{$this->name}.{$process->getInternalDomain($deployment)}";
    }

    private function getMounts(Deployment $deployment, Process $process, array $labels): array
    {
        $mounts = $process->getMounts($deployment);

        if ($this->backupCreate) {
            $mounts[] = $this->backupCreate->backupVolume->asMount($labels);
        }

        if ($this->backupRestore) {
            $mounts[] = $this->backupRestore->restoreVolume->asMount($labels);
        }

        return $mounts;
    }

    private function getCommandAndArgs(): array
    {
        if (! $this->command) {
            return [null, null];
        }

        return [['sh'], ['-c', $this->command]];
    }

    private function getReleaseCommandPayload(Process $process, array $labels): array
    {
        if (! $this->releaseCommand->command) {
            return [
                'ConfigName' => '',
                'ConfigLabels' => (object) [],
                'Command' => '',
            ];
        }

        // Always create a new config, as the command may be the same, but the image/entrypoint may be different.
        $this->releaseCommand->dockerName = $process->makeResourceName('rel_cmd');

        return [
            'ConfigName' => $this->releaseCommand->dockerName,
            'ConfigLabels' => dockerize_labels([
                ...$labels,
                'kind' => 'release-command',
            ]),
            'Command' => $this->releaseCommand->command,
        ];
    }

    private function getEnvVars(Deployment $deployment, Process $process): array
    {
        $envVars = $process->envVars;

        $envVars[] = EnvVar::validateAndCreate([
            'name' => 'PTAH_INTERNAL_DOMAIN',
            'value' => $this->getInternalDomain($deployment, $process),
        ]);

        $envVars[] = EnvVar::validateAndCreate([
            'name' => 'PTAH_HOSTNAME',
            'value' => $this->getHostname($deployment, $process),
        ]);

        $backupVolume = $this->backupCreate?->backupVolume ?? $this->backupRestore?->restoreVolume;
        if ($backupVolume) {
            $envVars[] = EnvVar::validateAndCreate([
                'name' => 'PTAH_BACKUP_DIR',
                'value' => $backupVolume->path,
            ]);
        }

        $envVars[] = EnvVar::validateAndCreate([
            'name' => 'PTAH_DEPLOYMENT_ID',
            'value' => strval($deployment->id),
        ]);

        $envVars[] = EnvVar::validateAndCreate([
            'name' => 'PTAH_SERVICE_ID',
            'value' => $deployment->service->slug,
        ]);

        $envVars[] = EnvVar::validateAndCreate([
            'name' => 'PTAH_PROCESS_NAME',
            'value' => $process->name,
        ]);

        $envVars[] = EnvVar::validateAndCreate([
            'name' => 'PTAH_WORKER_NAME',
            'value' => $this->name,
        ]);

        return $envVars;
    }

    private function getSecretVars(Process $process): object
    {
        return (object) collect($process->secretVars)
            ->reduce(function ($carry, SecretVar $var) {
                $carry[$var->name] = $var->value;

                return $carry;
            }, []);
    }

    private function getPorts(Process $process): array
    {
        if ($this->name !== 'main') {
            return [];
        }

        return collect($process->ports)
            ->map(fn (NodePort $port) => [
                'Protocol' => 'tcp',
                'TargetPort' => $port->targetPort,
                'PublishedPort' => $port->publishedPort,
                'PublishMode' => 'ingress',
            ])
            ->toArray();
    }

    private function getSchedulingMode(?int $desiredReplicas): array
    {
        $desiredReplicas ??= $this->replicas;

        if ($this->launchMode->isDaemon()) {
            return [
                'Replicated' => [
                    'Replicas' => $desiredReplicas,
                ],
            ];
        }

        return [
            'ReplicatedJob' => (object) [
                'MaxConcurrent' => $desiredReplicas,
            ],
        ];
    }

    public function getRestartPolicy(): array
    {
        if ($this->launchMode->isDaemon()) {
            return [
                'Condition' => 'any',
            ];
        }

        return [
            'Condition' => 'none',
        ];
    }

    public static function make(array $attributes): static
    {
        $defaults = [
            'name' => 'main',
            'dockerRegistryId' => null,
            'dockerImage' => '',
            'launchMode' => LaunchMode::Daemon->value,
            'replicas' => 1,
            'command' => null,
            'releaseCommand' => ReleaseCommand::from([
                'command' => null,
            ]),
            'healthcheck' => Healthcheck::from([
                'command' => null,
            ]),
        ];

        return self::from([
            ...$defaults,
            ...$attributes,
        ]);
    }
}
