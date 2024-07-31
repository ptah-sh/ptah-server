<?php

namespace App\Models\NodeTasks\DeleteService;

use App\Models\NodeTasks\AbstractTaskResult;

class DeleteServiceResult extends AbstractTaskResult
{
    public function __construct(
    ) {
        //
    }

    public function formattedHtml(): string
    {
        return 'Service deleted successfully.';
    }
}
