<?php

namespace App\Models\DeploymentData;

use Spatie\LaravelData\Data;

class ProcessBackup extends Data
{
    public function __construct(
        public string $id,
        public string $name,
        public string $command,
        public BackupSchedule $backupSchedule,
    ) {}
}
