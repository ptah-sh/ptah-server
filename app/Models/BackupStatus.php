<?php

namespace App\Models;

enum BackupStatus: string
{
    case InProgress = 'in_progress';
    case Succeeded = 'succeeded';
    case Failed = 'failed';
}
