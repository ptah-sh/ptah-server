<?php

namespace App\Models\NodeTasks\CreateSecret;

use App\Models\NodeTasks\AbstractTaskMeta;

class CreateSecretMeta extends AbstractTaskMeta
{
    public function __construct(
        public int $deploymentId,
        public string $processName,
        public string $path,
    ) {
        //
    }

    public function formattedHtml(): string
    {
        return "Create Docker Secret <code>$this->path</code> for process <code>{$this->processName}</code>";
    }
}
