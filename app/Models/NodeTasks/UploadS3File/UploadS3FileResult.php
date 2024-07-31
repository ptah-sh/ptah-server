<?php

namespace App\Models\NodeTasks\UploadS3File;

use App\Models\NodeTasks\AbstractTaskResult;

class UploadS3FileResult extends AbstractTaskResult
{
    public function __construct(
    ) {
        //
    }

    public function formattedHtml(): string
    {
        return 'UploadS3File - Task Result';
    }
}
