<?php

namespace App\Models;

use App\Events\NodeTaskGroups\BackupCreate\BackupCreateCompleted;
use App\Events\NodeTaskGroups\BackupCreate\BackupCreateFailed;

enum NodeTaskGroupType: int
{
    case InitSwarm = 0;
    case CreateService = 1;
    case UpdateService = 2;
    case DeleteService = 3;
    case SelfUpgrade = 4;
    case UpdateDockerRegistries = 5;
    case UpdateS3Storages = 6;
    case CreateBackup = 7;
    case JoinSwarm = 8;
    case UpdateDirdConfig = 9;
    case LaunchService = 10;
    case ExecuteScheduledWorker = 11;
    case BackupCreate = 12;

    public function completed(): ?string
    {
        return match ($this) {
            NodeTaskGroupType::BackupCreate => BackupCreateCompleted::class,
            default => null,
        };
    }

    public function failed(): ?string
    {
        return match ($this) {
            NodeTaskGroupType::BackupCreate => BackupCreateFailed::class,
            default => null,
        };
    }
}
