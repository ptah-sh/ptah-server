<?php

namespace App\Models\DeploymentData;

use App\Models\Deployment;
use App\Models\Node;
use App\Models\NodeTasks\CreateConfig\CreateConfigMeta;
use App\Models\NodeTasks\CreateSecret\CreateSecretMeta;
use App\Models\NodeTasks\DeleteService\DeleteServiceMeta;
use App\Models\NodeTasks\PullDockerImage\PullDockerImageMeta;
use App\Models\NodeTaskType;
use App\Rules\RequiredIfArrayHas;
use App\Rules\UniqueInArray;
use App\Util\ResourceId;
use Exception;
use Spatie\LaravelData\Attributes\DataCollectionOf;
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
        #[DataCollectionOf(Worker::class)]
        #[Rule(new UniqueInArray('name'))]
        /* @var Worker[] */
        public array $workers,
        #[DataCollectionOf(EnvVar::class)]
        #[Rule(new UniqueInArray('name'))]
        /* @var EnvVar[] */
        public array $envVars,
        #[DataCollectionOf(SecretVar::class)]
        #[Rule(new UniqueInArray('name'))]
        /* @var SecretVar[] */
        public array $secretVars,
        #[DataCollectionOf(ConfigFile::class)]
        #[Rule(new UniqueInArray('path'))]
        /* @var ConfigFile[] */
        public array $configFiles,
        #[DataCollectionOf(SecretFile::class)]
        #[Rule(new UniqueInArray('path'))]
        /* @var SecretFile[] */
        public array $secretFiles,
        #[DataCollectionOf(Volume::class)]
        #[Rule(new UniqueInArray('name'))]
        /* @var Volume[] */
        public array $volumes,
        public ?BackupVolume $backupVolume,
        #[DataCollectionOf(NodePort::class)]
        // TODO: unique across all services of the swarm cluster
        #[Rule(new UniqueInArray('targetPort'))]
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

        foreach ($this->secretVars as $secretVar) {
            $previousSecret = $previous?->findSecretVar($secretVar->name);
            if ($secretVar->sameAs($previousSecret)) {
                $secretVar->value = $previousSecret->value;
            }
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

        if ($this->backupVolume == null) {
            $this->backupVolume = BackupVolume::validateAndCreate([
                'id' => ResourceId::make('volume'),
                'name' => 'backups',
                'dockerName' => $this->makeResourceName('/ptah/backups'),
                'path' => '/ptah/backups',
            ]);
        }

        $tasks = [
            ...$tasks,
            ...$this->getPullImageTasks($deployment),
        ];

        foreach ($this->workers as $worker) {
            $tasks = [
                ...$tasks,
                ...$worker->asNodeTasks($deployment, $this),
            ];
        }

        return $tasks;
    }

    public function resourceLabels(Deployment $deployment): array
    {
        return dockerize_labels([
            ...$deployment->resourceLabels(),
            'process.name' => $this->name,
        ]);
    }

    public function makeResourceName(string $name): string
    {
        return dockerize_name($this->dockerName.'_'.$name);
    }

    public function getInternalDomain(Deployment $deployment): string
    {
        return "{$this->name}.{$deployment->data->internalDomain}";
    }

    public function getMounts(Deployment $deployment): array
    {
        $labels = $this->resourceLabels($deployment);

        $mounts = collect($this->volumes)
            ->map(fn (Volume $volume) => [
                'Type' => 'volume',
                'Source' => $volume->dockerName,
                'Target' => $volume->path,
                'VolumeOptions' => [
                    'Labels' => dockerize_labels([
                        ...$labels,
                        'volume.id' => $volume->id,
                        'volume.path' => $volume->path,
                    ]),
                ],
            ])
            ->toArray();

        $mounts[] = [
            'Type' => 'volume',
            'Source' => $this->backupVolume->dockerName,
            'Target' => $this->backupVolume->path,
            'VolumeOptions' => [
                'Labels' => dockerize_labels([
                    ...$labels,
                    'volume.id' => $this->backupVolume->id,
                    'volume.path' => $this->backupVolume->path,
                ]),
            ],
        ];

        return $mounts;
    }

    private function findWorker(?string $dockerName): ?Worker
    {
        if (! $dockerName) {
            return null;
        }

        return collect($this->workers)->first(fn (Worker $worker) => $worker->dockerName === $dockerName);
    }

    private function getPullImageTasks(Deployment $deployment): array
    {
        $pulledImages = [];

        $tasks = [];

        foreach ($this->workers as $worker) {
            if (in_array($worker->dockerImage, $pulledImages)) {
                continue;
            }

            $pulledImages[] = $worker->dockerImage;

            $dockerRegistry = $worker->dockerRegistryId
                ? $deployment->service->swarm->data->findRegistry($worker->dockerRegistryId)
                : null;

            if ($worker->dockerRegistryId && is_null($dockerRegistry)) {
                throw new Exception("Docker registry '{$worker->dockerRegistryId}' not found");
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
                    'dockerImage' => $worker->dockerImage,
                ]),
                'payload' => [
                    'AuthConfigName' => $authConfigName,
                    'Image' => $worker->dockerImage,
                    'PullOptions' => (object) [],
                ],
            ];
        }

        return $tasks;
    }

    public static function make(array $attributes): static
    {
        $workerDefaults = Worker::make([]);

        $defaults = [
            'name' => 'service',
            'networkName' => '',
            'internalDomain' => '',
            'workers' => [$workerDefaults],
            'launchMode' => LaunchMode::Daemon->value,
            'envVars' => [],
            'secretVars' => [],
            'configFiles' => [],
            'secretFiles' => [],
            'volumes' => [],
            'ports' => [],
            'replicas' => 1,
            'caddy' => [],
            'fastCgi' => null,
            'redirectRules' => [],
            'rewriteRules' => [],
        ];

        return self::from([
            ...$defaults,
            ...$attributes,
        ]);
    }
}
