<?php

namespace App\Models\NodeTasks\RemoveS3File;

use App\Models\NodeTasks\AbstractTaskMeta;

class RemoveS3FileMeta extends AbstractTaskMeta
{
    public function __construct(
        public string $s3StorageId,
        public string $filePath,
        public ?int $backupId = null,
    ) {
        //
    }

    public function formattedHtml(): string
    {
        return "Remove S3 File {$this->filePath}";
    }
}
