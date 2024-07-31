<?php

namespace App\Models\NodeTasks\CreateS3Storage;

use App\Models\NodeTasks\AbstractTaskResult;
use App\Models\NodeTasks\DockerId;

class CreateS3StorageResult extends AbstractTaskResult
{
    public function __construct(
        public DockerId $docker,
    ) {
        //
    }

    public function formattedHtml(): string
    {
        return "Docker ID: <code>{$this->docker->id}</code>";
    }
}
