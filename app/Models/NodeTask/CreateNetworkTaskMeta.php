<?php

namespace App\Models\NodeTask;

use Spatie\LaravelData\Data;

class CreateNetworkTaskMeta extends Data
{
    public function __construct(public int $networkId) {}
}