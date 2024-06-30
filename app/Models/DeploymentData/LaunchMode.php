<?php

namespace App\Models\DeploymentData;

enum LaunchMode: string
{
    case Daemon = 'daemon';
    case Scheduled = 'scheduled';
    case Backup = 'backup';
    case Lifecycle = 'lifecycle';
    case Manual = 'manual';
}
