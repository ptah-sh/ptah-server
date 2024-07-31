<?php

namespace App\Models\NodeTasks\UpdateCurrentNode;

use App\Models\NodeTasks\AbstractTaskMeta;

class UpdateCurrentNodeMeta extends AbstractTaskMeta
{
    public function __construct(
    ) {
        //
    }

    public function formattedHtml(): string
    {
        return "Update Agent's Swarm Node";
    }
}
