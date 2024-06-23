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
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\Validation\Exists;
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
        #[DataCollectionOf(EnvVar::class)]
        /* @var EnvVar[] */
        public array  $secretVars,
        #[DataCollectionOf(ConfigFile::class)]
        /* @var ConfigFile[] */
        public array  $configFiles,
        #[DataCollectionOf(ConfigFile::class)]
        /* @var ConfigFile[] */
        public array  $secretFiles,
        #[DataCollectionOf(Volume::class)]
        /* @var Volume[] */
        public array  $volumes,
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
        #[DataCollectionOf(EnvVar::class)]
        /* @var EnvVar[] */
        public ?array $fastcgiVars
    )
    {
    }
}