<?php

namespace App\Models\NodeData;

use Spatie\LaravelData\Attributes\Validation\Enum;
use Spatie\LaravelData\Attributes\Validation\IP;
use Spatie\LaravelData\Data;

class IpAddress extends Data
{
    public function __construct(
        #[Enum(IpVersion::class)]
        public string $version,
        #[IP]
        public string $ip
    ) {}
}
