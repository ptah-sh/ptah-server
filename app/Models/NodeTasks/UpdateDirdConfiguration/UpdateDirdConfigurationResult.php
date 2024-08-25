<?php

namespace App\Models\NodeTasks\UpdateDirdConfiguration;

use App\Models\NodeTasks\AbstractTaskResult;

class UpdateDirdConfigurationResult extends AbstractTaskResult
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
