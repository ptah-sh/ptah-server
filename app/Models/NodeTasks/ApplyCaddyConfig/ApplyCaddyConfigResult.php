<?php

namespace App\Models\NodeTasks\ApplyCaddyConfig;

use App\Models\NodeTasks\AbstractTaskResult;

class ApplyCaddyConfigResult extends AbstractTaskResult
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
