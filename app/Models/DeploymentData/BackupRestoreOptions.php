<?php

namespace App\Models\DeploymentData;

use Spatie\LaravelData\Data;

class BackupRestoreOptions extends Data
{
    public function __construct(
        public ?Volume $restoreVolume,
    ) {}
}
