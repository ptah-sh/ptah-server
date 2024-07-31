<?php

namespace App\Models\NodeTasks\DownloadAgentUpgrade;

use App\Models\NodeTasks\AbstractTaskMeta;

class DownloadAgentUpgradeMeta extends AbstractTaskMeta
{
    public function __construct(
        public string $targetVersion,
        public string $downloadUrl
    ) {
        //
    }

    public function formattedHtml(): string
    {
        return "Download agent upgrade to <code>{$this->targetVersion}</code>";
    }
}
