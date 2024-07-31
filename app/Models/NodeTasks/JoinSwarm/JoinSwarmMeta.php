<?php

namespace App\Models\NodeTasks\JoinSwarm;

use App\Models\NodeData\NodeRole;
use App\Models\NodeTasks\AbstractTaskMeta;
use App\Models\Swarm;

class JoinSwarmMeta extends AbstractTaskMeta
{
    public function __construct(
        public readonly string $swarmId,
        public readonly NodeRole $role,
    ) {
        //
    }

    public function formattedHtml(): string
    {
        $swarm = Swarm::find($this->swarmId);

        return "Join Swarm <code>{$swarm->name}</code> as <code>{$this->role->value}</code>";
    }
}
