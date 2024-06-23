<?php

namespace App\Models\NodeTasks\LoadCaddyConfig;

use App\Models\NodeTasks\AbstractTaskResult;

class LoadCaddyConfigResult extends AbstractTaskResult
{
    public function __construct(
    )
    {
        //
    }

    public function formattedHtml(): string
    {
        return "LoadCaddyConfig - Task Result";
    }
}
