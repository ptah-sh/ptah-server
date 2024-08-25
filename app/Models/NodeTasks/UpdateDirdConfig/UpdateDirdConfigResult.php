<?php

namespace App\Models\NodeTasks\UpdateDirdConfig;

use App\Models\NodeTasks\AbstractTaskResult;

class UpdateDirdConfigResult extends AbstractTaskResult
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
