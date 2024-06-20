<?php

namespace App\Models\NodeTask;

class InitSwarmTaskPayload extends AbstractTaskPayload
{
    public function __construct(
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