<?php

namespace App\Listeners;

use App\Events\Tasks\InitSwarmCompleted;
use App\Models\Swarm;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

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
