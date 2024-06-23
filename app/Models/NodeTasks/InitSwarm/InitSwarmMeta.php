<?php

namespace App\Models\NodeTasks\InitSwarm;

use App\Models\NodeTasks\AbstractTaskMeta;
use Spatie\LaravelData\Attributes\Validation\IPv4;

class InitSwarmMeta extends AbstractTaskMeta
{
    public function __construct(
        public int $swarmId,
        public string $name,
        public bool $forceNewCluster
    ) {

    }

    public function formattedHtml(): string
    {
        $msg = 'Initialize Docker Swarm cluster <code>'.$this->name.'</code>';
        if ($this->forceNewCluster) {
            $msg .= ' with <b>force cluster creation</b>';
        }

        return $msg;
    }
}
