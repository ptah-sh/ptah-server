<?php

namespace App\Console\Commands;

use App\Actions\HouseKeeping\PruneStaleDockerData;
use Illuminate\Console\Command;

class PruneStaleDockerDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:housekeeping:prune-stale-docker-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prune stale docker data';

    /**
     * Execute the console command.
     */
    public function handle(PruneStaleDockerData $action): void
    {
        $action->prune();
    }
}
