<?php

namespace App\Models\NodeTasks\InitSwarm;

use App\Models\NodeTasks\AbstractTaskMeta;

class InitSwarmMeta extends AbstractTaskMeta
{
    public function __construct(
        public int $swarmId,
        public bool $forceNewCluster
    ) {}

    public function formattedHtml(): string
    {
        $msg = 'Initialize Docker Swarm cluster';
        if ($this->forceNewCluster) {
            $msg .= ' with <b>force cluster creation</b>';
        }

        return $msg;
    }
}
