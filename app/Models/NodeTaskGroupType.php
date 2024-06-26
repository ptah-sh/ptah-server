<?php

namespace App\Models;

enum NodeTaskGroupType: int
{
    case InitSwarm = 0;
    case CreateService = 1;
    case UpdateService = 2;
    case DeleteService = 3;
    case SelfUpgrade = 4;
}
