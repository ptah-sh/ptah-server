<?php

namespace App\Models\NodeTasks\LaunchService;

use App\Models\NodeTasks\AbstractTaskResult;
use App\Models\NodeTasks\DockerId;

class LaunchServiceResult extends AbstractTaskResult
{
    public function __construct(
        public DockerId $docker,
        public string $action,
    ) {
        //
    }

    public function formattedHtml(): string
    {
        return "Service {$this->action} successfully. ID: <code>{$this->docker->id}</code>";
    }
}
