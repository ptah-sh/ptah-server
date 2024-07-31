<?php

namespace App\Models\NodeTasks;

use Spatie\LaravelData\Data;

class DockerId extends Data
{
    public function __construct(
        public string $id,
    ) {}
}
