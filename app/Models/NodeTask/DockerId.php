<?php

namespace App\Models\NodeTask;

use Spatie\LaravelData\Data;

class DockerId extends Data {

    public function __construct(
        public string $id
    ) {

    }
}