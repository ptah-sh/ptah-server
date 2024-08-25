<?php

namespace App\Models;

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
}
