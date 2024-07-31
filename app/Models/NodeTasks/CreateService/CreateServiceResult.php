<?php

namespace App\Models\NodeTasks\CreateService;

use App\Models\NodeTasks\AbstractTaskResult;
use App\Models\NodeTasks\DockerId;

class CreateServiceResult extends AbstractTaskResult
{
    public function __construct(
        public DockerId $docker
    ) {
        //
    }

    public function formattedHtml(): string
    {
        return 'Service ID: <code>'.$this->docker->id.'</code>';
    }
}
