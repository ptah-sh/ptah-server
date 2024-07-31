<?php

namespace App\Policies;

use App\Models\NodeTaskGroup;
use App\Models\User;

class NodeTaskGroupPolicy
{
    public function retry(User $user, NodeTaskGroup $taskGroup): bool
    {
        return $user->belongsToTeam($taskGroup->node->team);
    }
}
