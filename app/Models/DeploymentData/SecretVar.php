<?php

namespace App\Models\DeploymentData;

use Spatie\LaravelData\Data;

class SecretVar extends Data
{
    public function __construct(
        public string $name,
        public ?string $value
    ) {}

    public function sameAs(?SecretVar $other): bool
    {
        if ($other === null) {
            return false;
        }

        return $this->name === $other->name && is_null($this->value);
    }
}
