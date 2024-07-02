<?php

namespace App\Models;

use App\Models\SwarmData\DockerRegistry;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;

class SwarmData extends Data
{
    public function __construct(
        public int $registriesRev,
        #[DataCollectionOf(DockerRegistry::class)]
        /* @var DockerRegistry[] */
        public array $registries
    )
    {

    }

    public function findRegistry(string $dockerName): ?DockerRegistry
    {
        return collect($this->registries)
            ->filter(fn (DockerRegistry $registry) => $registry->dockerName === $dockerName)
            ->first();
    }
}
