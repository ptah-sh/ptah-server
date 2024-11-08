<?php

namespace App\Models\NodeTasks\PruneDockerRegistry;

use App\Models\NodeTasks\AbstractTaskResult;

class PruneDockerRegistryResult extends AbstractTaskResult
{
    public function __construct(
    ) {
        //
    }

    public function formattedHtml(): string
    {
        return 'PruneDockerRegistry - Task Result';
    }
}
