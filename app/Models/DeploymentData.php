<?php

namespace App\Models;

use App\Models\DeploymentData\Caddy;
use App\Models\DeploymentData\ConfigFile;
use App\Models\DeploymentData\EnvVar;
use App\Models\DeploymentData\FastCgi;
use App\Models\DeploymentData\LaunchMode;
use App\Models\DeploymentData\NodePort;
use App\Models\DeploymentData\Process;
use App\Models\DeploymentData\SecretVars;
use App\Models\DeploymentData\Volume;
use App\Models\NodeTasks\CreateConfig\CreateConfigMeta;
use App\Models\NodeTasks\CreateSecret\CreateSecretMeta;
use App\Models\NodeTasks\CreateService\CreateServiceMeta;
use App\Rules\RequiredIfArrayHas;
use App\Util\Arrays;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\RequiredIf;
use Spatie\LaravelData\Attributes\Validation\RequiredUnless;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;

class DeploymentData extends Data
{
    public function __construct(
//        #[Exists(DockerRegistry::class)]
//        #[Exists(Network::class, 'networkName')]
        public string    $networkName,
        public string $internalDomain,
        #[Exists(Node::class)]
        public ?int   $placementNodeId,
        #[DataCollectionOf(Process::class)]
        /* @var Process[] */
        public array  $processes
    )
    {
    }

    public static function make(array $attributes): static
    {
        $processDefaults = [
            'name' => 'svc',
            'dockerRegistryId' => null,
            'dockerImage' => '',
            'command' => '',
            'launchMode' => LaunchMode::Daemon->value,
            'envVars' => [],
            'secretVars' => SecretVars::from([
                'vars' => [],
            ]),
            'configFiles' => [],
            'secretFiles' => [],
            'volumes' => [],
            'ports' => [],
            'replicas' => 1,
            'caddy' => [],
            'fastCgi' => null,
        ];

        $defaults = [
            'networkName' => '',
            'internalDomain' => '',
            'placementNodeId' => null,
            'processes' => empty($attributes['processes']) ? [$processDefaults] : $attributes['processes'],
        ];

        return self::from([
            ...$defaults,
            ...$attributes
        ]);
    }

    public function copyWith(array $attributes): DeploymentData
    {
        return DeploymentData::make(Arrays::niceMerge($this->toArray(), $attributes));
    }

    public function findProcess(string $dockerName): ?Process
    {
        return collect($this->processes)->first(fn(Process $process) => $process->dockerName === $dockerName);
    }
}
