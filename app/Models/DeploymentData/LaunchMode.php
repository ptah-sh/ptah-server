<?php

namespace App\Models\DeploymentData;

enum LaunchMode: string
{
    case Daemon = 'daemon';
    case Cronjob = 'cronjob';
    case BackupCreate = 'backup_create';
    case BackupRestore = 'backup_restore';
    case Manual = 'manual';

    public function maxReplicas(): int
    {
        return match ($this) {
            self::Daemon => PHP_INT_MAX,
            self::Cronjob => 1,
            self::BackupCreate => 1,
            self::BackupRestore => 1,
            self::Manual => 1,
        };
    }
}
