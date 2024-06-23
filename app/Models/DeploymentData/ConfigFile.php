<?php

namespace App\Models\DeploymentData;

use App\Models\NodeTasks\DockerId;
use App\Models\Service;
use Spatie\LaravelData\Data;

class ConfigFile extends Data
{
    public function __construct(
        public string $path,
        public string $content
    )
    {

    }
}
