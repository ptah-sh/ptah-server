<?php

namespace App\Listeners;

use App\Events\NodeTasks\InitSwarm\InitSwarmCompleted;
use App\Models\Swarm;

class UpdateSwarmDockerId
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(InitSwarmCompleted $event): void
    {
        $swarm = Swarm::findOrFail($event->task->meta->swarmId);

        $swarm->docker_id = $event->task->result->docker->id;
        $swarm->save();
    }
}
