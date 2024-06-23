<?php

namespace App\Models;

enum NodeTaskGroupType: int
{
    case InitSwarm = 0;
    case DeployService = 1;
}