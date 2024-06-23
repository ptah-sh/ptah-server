<?php

namespace App\Models\NodeTasks\LoadCaddyConfig;

use App\Models\NodeTasks\AbstractTaskMeta;

class LoadCaddyConfigMeta extends AbstractTaskMeta
{
    public function __construct(
    )
    {
        //
    }

    public function formattedHtml(): string
    {
        return "LoadCaddyConfig - Task Payload";
    }
}
