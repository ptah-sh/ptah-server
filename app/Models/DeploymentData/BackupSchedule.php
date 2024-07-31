<?php

namespace App\Models\DeploymentData;

use Spatie\LaravelData\Data;

class BackupSchedule extends Data
{
    public function __construct(
        public CronPreset $preset,
        public string $s3StorageId,
        // TODO: !!! validate CRON expr
        public string $expr,
    ) {}
}
