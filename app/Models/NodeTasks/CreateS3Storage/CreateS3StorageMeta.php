<?php

namespace App\Models\NodeTasks\CreateS3Storage;

use App\Models\NodeTasks\AbstractTaskMeta;

class CreateS3StorageMeta extends AbstractTaskMeta
{
    public function __construct(
        public string $s3StorageId,
        public string $s3StorageName,
    ) {
        //
    }

    public function formattedHtml(): string
    {
        return "Create S3 Storage config for <code>{$this->s3StorageName}</code>";
    }
}
