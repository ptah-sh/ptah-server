<?php

namespace App\Models\DeploymentData;

enum CronPreset: string
{
    case Daily = 'daily';
    case Custom = 'custom';
}
