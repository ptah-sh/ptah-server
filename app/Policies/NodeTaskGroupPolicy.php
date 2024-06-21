<?php


namespace App\Policies;

use App\Models\Node;
use App\Models\NodeTaskGroup;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class NodeTaskGroupPolicy
{
    public function retry(User $user, NodeTaskGroup $taskGroup): bool
    {
        return $user->belongsToTeam($taskGroup->node->team);
    }
}