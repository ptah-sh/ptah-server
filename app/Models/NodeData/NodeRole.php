<?php

namespace App\Models\NodeData;

enum NodeRole: string
{
    case Manager = 'manager';
    case Worker = 'worker';
}
