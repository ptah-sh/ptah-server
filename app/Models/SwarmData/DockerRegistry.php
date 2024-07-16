<?php

namespace App\Models\SwarmData;

use Spatie\LaravelData\Attributes\Validation\RequiredUnless;
use Spatie\LaravelData\Attributes\Validation\RequiredWith;
use Spatie\LaravelData\Attributes\Validation\RequiredWithout;
use Spatie\LaravelData\Attributes\Validation\Sometimes;
use Spatie\LaravelData\Data;
use Symfony\Contracts\Service\Attribute\Required;

class DockerRegistry extends Data
{
    public function __construct(
        public string $id,
        public string $name,
        public ?string $dockerName,
        public string $serverAddress,
        #[RequiredWithout('dockerName')]
        public ?string $username,
        #[RequiredWithout('dockerName')]
        public ?string $password
    )
    {

    }

    public function sameAs(?DockerRegistry $other): bool
    {
        if ($other === null) {
            return false;
        }

        return $this->serverAddress === $other->serverAddress
            && empty($this->username)
            && empty($this->password);
    }
}
