<?php

namespace App\Models\NodeTasks\InitSwarm;

use App\Models\NodeTasks\AbstractTaskResult;
use App\Models\NodeTasks\DockerId;

class InitSwarmResult extends AbstractTaskResult
{
    public function __construct(
        public DockerId $docker
    ) {}

    public function formattedHtml(): string
    {
        return 'Swarm ID: <code>'.$this->docker->id.'</code>';
    }
}
