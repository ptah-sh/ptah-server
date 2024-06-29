<?php

namespace App\Models;

use App\Models\DeploymentData\Caddy;
use App\Models\DeploymentData\ConfigFile;
use App\Models\DeploymentData\EnvVar;
use App\Models\DeploymentData\FastCgi;
use App\Models\DeploymentData\NodePort;
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
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;

class DeploymentData extends Data
{
    public function __construct(
//        #[Exists(DockerRegistry::class)]
        public ?int   $dockerRegistryId,
        public string $dockerImage,
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
//        #[Exists(Network::class, 'networkName')]
        public string    $networkName,
        public string $internalDomain,
        #[DataCollectionOf(NodePort::class)]
        /* @var NodePort[] */
        public array  $ports,
        public int    $replicas,
        #[Exists(Node::class)]
        public ?int   $placementNodeId,
        #[DataCollectionOf(Caddy::class)]
        /* @var Caddy[] */
        public array  $caddy,
        #[Rule(new RequiredIfArrayHas('caddy.*.targetProtocol', 'fastcgi'))]
        public ?FastCgi $fastCgi
    )
    {
    }

    public static function make(array $attributes): static
    {
        $defaults = [
            'dockerRegistryId' => null,
            'dockerImage' => '',
            'envVars' => [],
            'secretVars' => SecretVars::from([
                'vars' => [],
            ]),
            'configFiles' => [],
            'secretFiles' => [],
            'volumes' => [],
            'networkName' => '',
            'internalDomain' => '',
            'ports' => [],
            'replicas' => 1,
            'placementNodeId' => null,
            'caddy' => [],
            'fastCgi' => null,
        ];

        return self::from([
            ...$defaults,
            ...$attributes
        ]);
    }

    public function findConfigFile(string $path): ?ConfigFile
    {
        return collect($this->configFiles)->first(fn(ConfigFile $file) => $file->path === $path);
    }

    public function findSecretFile(string $path): ?ConfigFile
    {
        return collect($this->secretFiles)->first(fn(ConfigFile $file) => $file->path === $path);
    }

    public function copyWith(array $attributes): DeploymentData
    {
        return DeploymentData::make(Arrays::niceMerge($this->toArray(), $attributes));
    }
}