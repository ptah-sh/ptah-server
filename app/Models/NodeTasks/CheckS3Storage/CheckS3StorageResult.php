<?php

namespace App\Models\NodeTasks\CheckS3Storage;

use App\Models\NodeTasks\AbstractTaskResult;

class CheckS3StorageResult extends AbstractTaskResult
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
