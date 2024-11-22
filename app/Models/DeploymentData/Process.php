<?php

namespace App\Models\DeploymentData;

use App\Models\Deployment;
use App\Models\Node;
use App\Models\NodeTasks\CreateConfig\CreateConfigMeta;
use App\Models\NodeTasks\CreateSecret\CreateSecretMeta;
use App\Models\NodeTasks\DeleteService\DeleteServiceMeta;
use App\Models\NodeTaskType;
use App\Rules\RequiredIfArrayHas;
use App\Rules\UniqueInArray;
use App\Util\Arrays;
use Exception;
use Illuminate\Validation\ValidationException;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\Validation\Distinct;
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
        #[DataCollectionOf(NodePort::class)]
        // TODO: unique across all services of the swarm cluster
        #[Rule(new UniqueInArray('targetPort'))]
        /* @var NodePort[] */
        public array $ports,
        #[DataCollectionOf(Caddy::class), Distinct('id')]
        /* @var Caddy[] */
        public array $caddy,
        #[Rule(new RequiredIfArrayHas('caddy.*.targetProtocol', 'fastcgi'))]
        public ?FastCgi $fastCgi,
    ) {}

    public function findVolume(string $id): ?Volume
    {
        return collect($this->volumes)->first(fn (Volume $volume) => $volume->id === $id);
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

    public function findCaddyById(string $id): ?Caddy
    {
        return collect($this->caddy)->first(fn (Caddy $caddy) => $caddy->id === $id);
    }

    public function addCaddy(Caddy $caddy): void
    {
        if ($this->findCaddyById($caddy->id)) {
            throw ValidationException::withMessages([
                'caddy' => 'Caddy with id '.$caddy->id.' already exists',
            ]);
        }

        array_push($this->caddy, $caddy);
    }

    public function putCaddyById(string $id, Caddy $caddy): void
    {
        $this->removeCaddyById($id, safe: true);
        $this->addCaddy($caddy);
    }

    public function removeCaddyById(string $id, bool $safe = false): void
    {
        if (! $this->findCaddyById($id)) {
            if ($safe) {
                return;
            }

            throw ValidationException::withMessages([
                'caddy' => 'Caddy with id '.$id.' does not exist',
            ]);
        }

        $this->caddy = array_filter($this->caddy, fn ($caddy) => $caddy->id !== $id);
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

        if (! $deployment->review_app_id) {
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
        }

        foreach ($this->configFiles as $configFile) {
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

        $tasks = [
            ...$tasks,
            ...$this->getBuildOrPullTasks($deployment),
        ];

        foreach ($this->workers as $worker) {
            $tasks = [
                ...$tasks,
                ...$worker->asNodeTasks($deployment, $this, buildOrPull: false),
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
            ->map(fn (Volume $volume) => $volume->asMount($labels))
            ->toArray();

        return $mounts;
    }

    public function findWorker(?string $dockerName): ?Worker
    {
        if (! $dockerName) {
            return null;
        }

        return collect($this->workers)->first(fn (Worker $worker) => $worker->dockerName === $dockerName);
    }

    public function findWorkerByName(string $name): ?Worker
    {
        return collect($this->workers)->first(fn (Worker $worker) => $worker->name === $name);
    }

    private function getBuildOrPullTasks(Deployment $deployment): array
    {
        $sources = [];

        $tasks = [];

        foreach ($this->workers as $worker) {
            $encodedSource = json_encode($worker->source);
            if (in_array($encodedSource, $sources)) {
                continue;
            }

            $sources[] = $encodedSource;

            $tasks = [
                ...$tasks,
                ...$worker->getBuildOrPullImageTasks($deployment, $this),
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
        ];

        return self::from([
            ...$defaults,
            ...$attributes,
        ]);
    }

    public function copyWith(array $attributes): static
    {
        $result = $this->toArray();

        if (isset($attributes['envVars'])) {
            $attributes['envVars'] = Arrays::niceMergeByKey($result['envVars'], $attributes['envVars'], 'name');
        }

        $errors = [];

        if (isset($attributes['workers'])) {
            foreach ($attributes['workers'] as $idx => $worker) {
                if (! isset($worker['name'])) {
                    $errors["workers.{$idx}.name"] = 'Worker name is required';

                    continue;
                }

                if (! $this->findWorkerByName($worker['name'])) {
                    $errors["workers.{$idx}.name"] = 'Worker '.$worker['name'].' does not exist';
                }
            }

            $attributes['workers'] = Arrays::niceMergeByKey($result['workers'], $attributes['workers'], 'name');
        }

        if (! empty($errors)) {
            throw ValidationException::withMessages($errors);
        }

        return self::validateAndCreate([
            ...$result,
            ...$attributes,
        ]);
    }
}
