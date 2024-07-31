<?php

namespace App\Models\NodeTasks\ConfirmAgentUpgrade;

use App\Models\NodeTasks\AbstractTaskMeta;

class ConfirmAgentUpgradeMeta extends AbstractTaskMeta
{
    public function __construct(
        public string $targetVersion
    ) {
        //
    }

    public function formattedHtml(): string
    {
        return "Confirm agent upgrade to <code>{$this->targetVersion}</code>";
    }
}
