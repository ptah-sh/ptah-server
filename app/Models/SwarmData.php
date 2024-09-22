<?php

namespace App\Models;

use App\Models\SwarmData\DockerRegistry;
use App\Models\SwarmData\JoinTokens;
use App\Models\SwarmData\ManagerNode;
use App\Models\SwarmData\S3Storage;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;

class SwarmData extends Data
{
    public function __construct(
        public int $registriesRev,
        #[DataCollectionOf(DockerRegistry::class)]
        /* @var DockerRegistry[] */
        public array $registries,
        public int $s3StoragesRev,
        #[DataCollectionOf(S3Storage::class)]
        /* @var S3Storage[] */
        public array $s3Storages,
        public JoinTokens $joinTokens,
        #[DataCollectionOf(ManagerNode::class)]
        /* @var ManagerNode[] */
        public array $managerNodes,
        public string $encryptionKey = '',
    ) {}

    public function findRegistry(string $id): ?DockerRegistry
    {
        return collect($this->registries)
            ->filter(fn (DockerRegistry $registry) => $registry->id === $id)
            ->first();
    }

    public function findS3Storage(string $id): ?S3Storage
    {
        return collect($this->s3Storages)
            ->filter(fn (S3Storage $s3Storage) => $s3Storage->id === $id)
            ->first();
    }
}
