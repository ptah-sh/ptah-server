<?php

namespace App\Models\NodeTasks\UpdateService;

use App\Models\NodeTasks\AbstractTaskResult;

class UpdateServiceResult extends AbstractTaskResult
{
    public function __construct(
    ) {
        //
    }

    public function formattedHtml(): string
    {
        return 'Update Succeeded.';
    }
}
