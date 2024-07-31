<?php

namespace App\Models\NodeTasks\ApplyCaddyConfig;

use App\Models\NodeTasks\AbstractTaskMeta;

class ApplyCaddyConfigMeta extends AbstractTaskMeta
{
    public function __construct(
        public int $deploymentId
    ) {
        //
    }

    public function formattedHtml(): string
    {
        return 'Apply Caddy Config';
    }
}
