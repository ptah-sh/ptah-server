<?php

namespace App\Models\DeploymentData;

use Spatie\LaravelData\Data;

class BackupOptions extends Data
{
    public function __construct(
        public string $s3StorageId,
        public ?ArchiveOptions $archive,
        public ?Volume $backupVolume,
    ) {}
}
