<?php

namespace App\Models\DeploymentData;

use App\Models\Deployment;
use App\Models\NodeTasks\CreateConfig\CreateConfigMeta;
use App\Models\NodeTasks\CreateSecret\CreateSecretMeta;
use App\Models\NodeTasks\CreateService\CreateServiceMeta;
use App\Models\NodeTasks\PullDockerImage\PullDockerImageMeta;
use App\Models\NodeTaskType;
use App\Rules\RequiredIfArrayHas;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\Validation\Enum;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;

class Process extends Data
{
    public function __construct(
        public string $name,
        public ?string $dockerName,
        public ?string   $dockerRegistry,
        public string $dockerImage,
        public ?string $command,
        #[Enum(LaunchMode::class)]
        public string $launchMode,
        #[DataCollectionOf(EnvVar::class)]
                           /* @var EnvVar[] */
        public array  $envVars,
        public SecretVars  $secretVars,
        #[DataCollectionOf(ConfigFile::class)]
                           /* @var ConfigFile[] */
        public array  $configFiles,
        #[DataCollectionOf(ConfigFile::class)]
                           /* @var ConfigFile[] */
        public array  $secretFiles,
        #[DataCollectionOf(Volume::class)]
                           /* @var Volume[] */
        public array  $volumes,
        public int    $replicas,
        #[DataCollectionOf(NodePort::class)]
        /* @var NodePort[] */
        public array  $ports,
        #[DataCollectionOf(Caddy::class)]
                        /* @var Caddy[] */
        public array  $caddy,
        #[Rule(new RequiredIfArrayHas('caddy.*.targetProtocol', 'fastcgi'))]
        public ?FastCgi $fastCgi,
    )
    {

    }
    public function findConfigFile(string $path): ?ConfigFile
    {
        return collect($this->configFiles)->first(fn(ConfigFile $file) => $file->path === $path);
    }

    public function findSecretFile(string $path): ?ConfigFile
    {
        return collect($this->secretFiles)->first(fn(ConfigFile $file) => $file->path === $path);
    }

    public function asNodeTasks(Deployment $deployment): array
    {
        if (empty($this->dockerName)) {
            $this->dockerName = dockerize_name($deployment->service->docker_name . '_' . $this->name);
        }

        $labels = $deployment->resourceLabels();
        $previous = $deployment->previousDeployment()?->findProcess($this->dockerName);

        $tasks = [];

        foreach ($this->configFiles as $configFile) {
            $previousConfig = $previous?->findConfigFile($configFile->path);
            if ($previousConfig && $configFile->sameAs($previousConfig)) {
                $configFile->dockerName = $previousConfig->dockerName;

                continue;
            }

            $configFile->dockerName = $this->makeResourceName('dpl_'.$deployment->id .'_cfg_' . $configFile->path);

            $tasks[] = [
                'type' => NodeTaskType::CreateConfig,
                'meta' => CreateConfigMeta::from([
                    'deploymentId' => $deployment->id,
                    'processName' => $this->dockerName,
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

        foreach ($this->secretFiles as $secretFile) {
            $previousSecret = $previous?->findSecretFile($secretFile->path);
            if ($previousSecret && ($secretFile->content === null || $secretFile->sameAs($previousSecret))) {
                $secretFile->dockerName = $previousSecret->dockerName;

                continue;
            }

            $secretFile->dockerName = $this->makeResourceName('dpl_'.$deployment->id .'_cfg_' . $secretFile->path);

            $tasks[] = [
                'type' => NodeTaskType::CreateSecret,
                'meta' => CreateSecretMeta::from([
                    'deploymentId' => $deployment->id,
                    'processName' => $this->dockerName,
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

        foreach ($this->volumes as $volume) {
            if (!$volume->dockerName) {
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
                'AuthConfigName' => $this->dockerRegistry,
                'Image' => $this->dockerImage,
                'PullOptions' => (object) [],
            ],
        ];

        $tasks[] = [
            'type' => $previous ? NodeTaskType::UpdateService : NodeTaskType::CreateService,
            'meta' => CreateServiceMeta::from([
                'deploymentId' => $deployment->id,
                'processName' => $this->dockerName,
                'serviceId' => $deployment->service_id,
                'serviceName' => $deployment->service->name,
            ]),
            'payload' => [
                'AuthConfigName' => $this->dockerRegistry,
                'SecretVars' => (object) $this->getSecretVars($previous, $labels),
                'SwarmServiceSpec' => [
                    'Name' => $this->dockerName,
                    'Labels' => $labels,
                    'TaskTemplate' => [
                        'ContainerSpec' => [
                            // TODO: !!!!!!!!!!!!!!!!!!!!! add CMD from $this->command
                            'Image' => $this->dockerImage,
                            'Labels' => $labels,
                            'Command' => $command,
                            'Args' => $args,
                            'Hostname' => "dpl-{$deployment->id}.{$internalDomain}",
                            'Env' => collect($this->envVars)->map(fn(EnvVar $var) => "{$var->name}={$var->value}")->toArray(),
                            'Mounts' => collect($this->volumes)->map(fn(Volume $volume) => [
                                'Type' => 'volume',
                                'Source' => $volume->dockerName,
                                'Target' => $volume->path,
                                'VolumeOptions' => [
                                    'Labels' => $labels,
                                ]
                            ])->toArray(),
                            'Hosts' => [
                                $internalDomain,
                            ],
                            'Secrets' => collect($this->secretFiles)->map(fn(ConfigFile $secretFile) => [
                                'File' => [
                                    'Name' => $secretFile->path,
                                    // TODO: figure out better permissions settings (if any)
                                    'UID' => "0",
                                    "GID" => "0",
                                    "Mode" => 0777
                                ],
                                'SecretName' => $secretFile->dockerName,
                            ])->values()->toArray(),
                            'Configs' => collect($this->configFiles)->map(fn(ConfigFile $configFile) => [
                                'File' => [
                                    'Name' => $configFile->path,
                                    // TODO: figure out better permissions settings (if any)
                                    'UID' => "0",
                                    "GID" => "0",
                                    "Mode" => 0777
                                ],
                                'ConfigName' => $configFile->dockerName,
                            ])->values()->toArray(),
                            'Placement' => $deployment->data->placementNodeId ? [
                                'Constraints' => [
                                    "node.labels.sh.ptah.node.id=={$deployment->data->placementNodeId}",
                                ]
                            ] : [],
                        ],
                        'Networks' => [
                            [
                                'Target' => $deployment->data->networkName,
                                'Aliases' => [$internalDomain],
                            ]
                        ],
                    ],
                    'Mode' => [
                        'Replicated' => [
                            'Replicas' => $this->replicas,
                        ],
                    ],
                    'EndpointSpec' => [
                        'Ports' => collect($this->ports)->map(fn(NodePort $port) => [
                            'Protocol' => 'tcp',
                            'TargetPort' => $port->targetPort,
                            'PublishedPort' => $port->publishedPort,
                            'PublishMode' => 'ingress',
                        ])->toArray()
                    ]
                ]
            ],
        ];

        return $tasks;
    }

    protected function getSecretVars(?Process $previous, $labels): array|object
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

        if (!empty($previous?->secretVars->dockerName)) {
            $data['Preserve'] = collect($this->data->secretVars->vars)
                ->filter(fn (EnvVar $var) => $var->value === null)
                ->map(fn (EnvVar $var) => $var->name)
                ->toArray();

            $data['PreserveFromConfig'] = $previous->secretVars->dockerName;
        }

        return $data;
    }


    public function makeResourceName(string $name): string
    {
        return dockerize_name($this->dockerName . '_' . $name);
    }
}
