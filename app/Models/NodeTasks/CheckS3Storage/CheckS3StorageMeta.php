<?php

namespace App\Models\NodeTasks\CheckS3Storage;

use App\Models\NodeTasks\AbstractTaskMeta;

class CheckS3StorageMeta extends AbstractTaskMeta
{
    public function __construct(
        public string $s3StorageId,
        public string $s3StorageName,
    ) {
        //
    }

    public function formattedHtml(): string
    {
        return "Check S3 Storage credentials for <code>{$this->s3StorageName}</code>";
    }
}
