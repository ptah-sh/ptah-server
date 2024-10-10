<?php

namespace App\Models\NodeTasks\UploadS3File;

use App\Models\NodeTasks\AbstractTaskResult;

class UploadS3FileResult extends AbstractTaskResult
{
    public function __construct(
        public array $output,
    ) {
        //
    }

    public function formattedHtml(): string
    {
        return implode('<br>', $this->output);
    }
}
