<?php

namespace App\Models\DeploymentData;

use Spatie\LaravelData\Attributes\Validation\RequiredWithout;
use Spatie\LaravelData\Data;

class SecretFile extends Data
{
    public function __construct(
        public string $path,
        #[RequiredWithout('dockerName')]
        public ?string $content,
        public ?string $dockerName,
    ) {}

    public function base64(): string
    {
        return base64_encode($this->content);
    }

    public function sameAs(?SecretFile $older): bool
    {
        if ($older === null) {
            return false;
        }

        return $this->path === $older->path && is_null($this->content);
    }
}
