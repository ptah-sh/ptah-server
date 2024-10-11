<?php

namespace App\Models\DeploymentData;

use Spatie\LaravelData\Data;

class BackupCreateOptions extends Data
{
    public function __construct(
        public string $s3StorageId,
        public ?ArchiveOptions $archive,
        public ?Volume $backupVolume,
    ) {}
}
