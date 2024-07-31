<?php

namespace App\Models\NodeTasks\CreateSecret;

use App\Models\NodeTasks\AbstractTaskResult;
use App\Models\NodeTasks\DockerId;

class CreateSecretResult extends AbstractTaskResult
{
    public function __construct(
        public DockerId $docker
    ) {
        //
    }

    public function formattedHtml(): string
    {
        return 'Secret ID: <code>'.$this->docker->id.'</code>';
    }
}
