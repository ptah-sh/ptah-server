<?php

namespace App\Models\NodeTasks\DownloadS3File;

use App\Models\NodeTasks\AbstractTaskMeta;

class DownloadS3FileMeta extends AbstractTaskMeta
{
    public function __construct(
        public int $serviceId,
        public ?int $backupId,
        public string $destPath,
    ) {
        //
    }

    public function formattedHtml(): string
    {
        return "Download file from S3 Storage: {$this->destPath}";
    }
}
