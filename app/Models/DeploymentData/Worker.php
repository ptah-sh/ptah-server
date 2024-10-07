<?php

namespace App\Models\DeploymentData;

use App\Models\Deployment;
use App\Models\NodeTasks\LaunchService\LaunchServiceMeta;
use App\Models\NodeTaskType;
use Spatie\LaravelData\Attributes\Validation\Enum;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Data;

class Worker extends Data
{
    public function __construct(
        public string $name,
        public ?string $dockerRegistryId,
        public string $dockerImage,
        public ?string $dockerName,
        public ?string $command,
        #[Min(1)]
        public int $replicas,
        #[Enum(LaunchMode::class)]
        public LaunchMode $launchMode,
        public ?string $schedule,
        public ReleaseCommand $releaseCommand,
        public Healthcheck $healthcheck,
    ) {
        $this->replicas = min($this->replicas, $this->launchMode->maxReplicas());
    }

    public function asNodeTasks(Deployment $deployment, Process $process): array
    {
        [$command, $args] = $this->getCommandAndArgs();

        $internalDomain = $this->getInternalDomain($deployment, $process);

        $hostname = $this->getHostname($deployment, $process);

        $tasks = [];

        if (! $this->dockerName) {
            $this->dockerName = $process->makeResourceName('wkr_'.$this->name);
        }

        $labels = [
            ...$process->resourceLabels($deployment),
            'worker.name' => $this->name,
        ];

        $tasks[] = [
            'type' => NodeTaskType::LaunchService,
            'meta' => LaunchServiceMeta::from([
                'deploymentId' => $deployment->id,
                'serviceId' => $deployment->service_id,
                'serviceName' => $deployment->service->name,
                'dockerName' => $this->dockerName,
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
                            'Mounts' => $process->getMounts($deployment),
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
                    'Mode' => [
                        'Replicated' => [
                            'Replicas' => $this->replicas,
                        ],
                    ],
                    'EndpointSpec' => [
                        'Ports' => $this->getPorts($process),
                    ],
                ],
            ],
        ];

        return $tasks;
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

    private function getCommandAndArgs(): array
    {
        if (! $this->command) {
            return [null, null];
        }

        // FIXME: use smarter CLI split - need to handle values with spaces, surrounded by the double quotes
        $splitCmd = explode(' ', $this->command);

        $command = [$splitCmd[0]];
        $args = array_slice($splitCmd, 1);

        return [$command, $args];
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

        $envVars[] = EnvVar::validateAndCreate([
            'name' => 'PTAH_WORKER_NAME',
            'value' => $this->name,
        ]);

        $envVars[] = EnvVar::validateAndCreate([
            'name' => 'PTAH_BACKUP_DIR',
            'value' => '/ptah/backups',
        ]);

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
