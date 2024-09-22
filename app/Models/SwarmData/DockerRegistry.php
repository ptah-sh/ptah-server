<?php

namespace App\Models\SwarmData;

use Spatie\LaravelData\Attributes\Validation\RequiredWithout;
use Spatie\LaravelData\Data;

class DockerRegistry extends Data
{
    public function __construct(
        public string $id,
        public string $name,
        public ?string $dockerName,
        public string $serverAddress,
        public string $username,
        #[RequiredWithout('dockerName')]
        public ?string $password
    ) {}

    public function sameAs(?DockerRegistry $other): bool
    {
        if ($other === null) {
            return false;
        }

        return $this->serverAddress === $other->serverAddress && $this->username === $other->username && is_null($this->password);
    }
}
