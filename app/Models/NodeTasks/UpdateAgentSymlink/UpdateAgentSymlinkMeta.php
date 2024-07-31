<?php

namespace App\Models\NodeTasks\UpdateAgentSymlink;

use App\Models\NodeTasks\AbstractTaskMeta;

class UpdateAgentSymlinkMeta extends AbstractTaskMeta
{
    public function __construct(
        public string $targetVersion
    ) {
        //
    }

    public function formattedHtml(): string
    {
        return "Update current agent symlink to <code>{$this->targetVersion}</code>";
    }
}
