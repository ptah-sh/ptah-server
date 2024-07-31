<?php

namespace ApiNodes\Models;

use ApiNodes\Models\AgentStartedEventData\SwarmData;
use App\Models\NodeData;
use Spatie\LaravelData\Data;

class AgentStartedEventData extends Data
{
    public function __construct(
        public NodeData $node,
        public ?SwarmData $swarm,
    ) {}
}
