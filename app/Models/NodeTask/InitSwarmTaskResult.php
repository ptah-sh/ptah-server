<?php

namespace App\Models\NodeTask;

use Spatie\LaravelData\Data;

class InitSwarmTaskResult extends AbstractTaskResult
{
    public function __construct(
        public DockerId $docker
    )
    {

    }

    public function formattedHtml(): string
    {
        return 'Swarm ID: <code>' . $this->docker->id . '</code>';
    }
}