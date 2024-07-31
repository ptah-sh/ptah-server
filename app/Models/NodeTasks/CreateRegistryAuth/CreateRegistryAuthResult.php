<?php

namespace App\Models\NodeTasks\CreateRegistryAuth;

use App\Models\NodeTasks\AbstractTaskResult;
use App\Models\NodeTasks\DockerId;

class CreateRegistryAuthResult extends AbstractTaskResult
{
    public function __construct(
        public DockerId $docker
    ) {
        //
    }

    public function formattedHtml(): string
    {
        return "Config ID: <code>{$this->docker->id}</code>";
    }
}
