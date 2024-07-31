<?php

namespace App\Models\NodeTasks\CreateNetwork;

use App\Models\NodeTasks\AbstractTaskMeta;

class CreateNetworkMeta extends AbstractTaskMeta
{
    public function __construct(
        public int $networkId,
        public string $name,
    ) {}

    public function formattedHtml(): string
    {
        return 'Create Docker Network <code>'.$this->name.'</code>';
    }
}
