<?php

namespace App\Models\NodeTasks\CreateConfig;

use App\Models\NodeTasks\AbstractTaskMeta;

class CreateConfigMeta extends AbstractTaskMeta
{
    public function __construct(
        public int    $deploymentId,
        public string $path,
        public string $hash
    )
    {
        //
    }

    public function formattedHtml():string
    {
        return "Create Docker Config for <code>$this->path</code>";
    }
}
