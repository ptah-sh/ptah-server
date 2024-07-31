<?php

namespace ApiNodes\Models\AgentStartedEventData;

use App\Models\SwarmData\JoinTokens;
use App\Models\SwarmData\ManagerNode;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;

class SwarmData extends Data
{
    public function __construct(
        public JoinTokens $joinTokens,
        #[DataCollectionOf(ManagerNode::class)]
        /* @var ManagerNode[] */
        public array $managerNodes,
    ) {}
}
