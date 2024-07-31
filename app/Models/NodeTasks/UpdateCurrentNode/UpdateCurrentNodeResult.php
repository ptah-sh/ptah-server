<?php

namespace App\Models\NodeTasks\UpdateCurrentNode;

use App\Models\NodeTasks\AbstractTaskResult;

class UpdateCurrentNodeResult extends AbstractTaskResult
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
