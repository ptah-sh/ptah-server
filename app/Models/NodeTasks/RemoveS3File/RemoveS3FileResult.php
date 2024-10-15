<?php

namespace App\Models\NodeTasks\RemoveS3File;

use App\Models\NodeTasks\AbstractTaskResult;

class RemoveS3FileResult extends AbstractTaskResult
{
    public function __construct(
    ) {
        //
    }

    public function formattedHtml(): string
    {
        return 'RemoveS3File - Task Result';
    }
}
