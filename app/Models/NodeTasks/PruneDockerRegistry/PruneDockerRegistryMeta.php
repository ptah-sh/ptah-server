<?php

namespace App\Models\NodeTasks\PruneDockerRegistry;

use App\Models\NodeTasks\AbstractTaskMeta;

class PruneDockerRegistryMeta extends AbstractTaskMeta
{
    public function __construct(
    ) {
        //
    }

    public function formattedHtml(): string
    {
        return 'Prune Docker Registry';
    }
}
