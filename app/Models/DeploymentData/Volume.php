<?php

namespace App\Models\DeploymentData;

use Spatie\LaravelData\Data;

class Volume extends Data
{
    public function __construct(
        public string $id,
        public string $name,
        public ?string $dockerName,
        public string $path,
        public ?BackupSchedule $backupSchedule
    ) {}
}
