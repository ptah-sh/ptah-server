<?php

namespace App\Models\NodeData;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;

class HostData extends Data
{
    public function __construct(
        #[DataCollectionOf(HostNetwork::class)]
        public array $networks
    ) {}
}
