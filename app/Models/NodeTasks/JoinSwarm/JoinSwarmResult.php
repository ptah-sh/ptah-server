<?php

namespace App\Models\NodeTasks\JoinSwarm;

use App\Models\NodeTasks\AbstractTaskResult;

class JoinSwarmResult extends AbstractTaskResult
{
    public function __construct(
    ) {
        //
    }

    public function formattedHtml(): string
    {
        return 'Success.';
    }
}
