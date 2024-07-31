<?php

namespace App\Models\NodeData;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

class HostNetwork extends Data
{
    public function __construct(
        public string $if_name,
        #[DataCollectionOf(IpAddress::class)]
        #[Required]
        public array $ips
    ) {}
}
