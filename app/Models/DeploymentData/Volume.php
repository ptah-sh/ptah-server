<?php

namespace App\Models\DeploymentData;

use Spatie\LaravelData\Data;

class Volume extends Data
{
    public function __construct(
        public string $id,
        public string $name,
        public ?string $dockerName,
        public string $path,
    ) {}

    public function asMount(array $labels): array
    {
        return [
            'Type' => 'volume',
            'Source' => $this->dockerName,
            'Target' => $this->path,
            'VolumeOptions' => [
                'Labels' => dockerize_labels([
                    ...$labels,
                    'volume.id' => $this->id,
                    'volume.path' => $this->path,
                ]),
            ],
        ];
    }
}
