<?php

namespace App\Models\NodeTasks\CreateNetwork;

use App\Models\NodeTasks\AbstractTaskResult;
use App\Models\NodeTasks\DockerId;

class CreateNetworkResult extends AbstractTaskResult
{
    public function __construct(
        public DockerId $docker
    ) {}

    public function formattedHtml(): string
    {
        return 'Network ID: <code>'.$this->docker->id.'</code>';
    }
}
