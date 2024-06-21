<?php

namespace App\Models\NodeTask;

use Spatie\LaravelData\Data;

class InitSwarmTaskMeta extends Data
{
    public function __construct(public int $swarmId) {}
}