<?php

namespace App\Listeners;

use App\Events\NodeTasks\CreateNetwork\CreateNetworkCompleted;
use App\Models\Network;

class UpdateNetworkDockerId
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
    public function handle(CreateNetworkCompleted $event): void
    {
        // TODO: ...or just Network::update([docker_id]) ???
        $network = Network::findOrFail($event->task->meta->networkId);

        $network->docker_id = $event->task->result->docker->id;
        $network->save();
    }
}
