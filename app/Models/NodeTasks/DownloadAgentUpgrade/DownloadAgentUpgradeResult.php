<?php

namespace App\Models\NodeTasks\DownloadAgentUpgrade;

use App\Models\NodeTasks\AbstractTaskResult;

class DownloadAgentUpgradeResult extends AbstractTaskResult
{
    public function __construct(
        public int $fileSize,
        public int $downloadTime
    ) {
        //
    }

    public function formattedHtml(): string
    {
        return "Downloaded <code>{$this->fileSize}</code> bytes in <code>{$this->downloadTime}</code> seconds.";
    }
}
