<?php

namespace App\Models\NodeData;

use IPLib\Address\IPv4;
use IPLib\Range;
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

    public function isPublic(): bool
    {
        return $this->version === IpVersion::IPv4 && IPv4::parseString($this->ip)->getRangeType() === Range\Type::T_PUBLIC;
    }
}
