<?php

namespace App\Models\NodeTasks\DownloadS3File;

use App\Models\NodeTasks\AbstractTaskResult;

class DownloadS3FileResult extends AbstractTaskResult
{
    public function __construct(
    ) {
        //
    }

    public function formattedHtml(): string
    {
        return 'DownloadS3File - Task Result';
    }
}
