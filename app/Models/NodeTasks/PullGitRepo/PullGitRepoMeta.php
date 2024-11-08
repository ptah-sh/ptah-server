<?php

namespace App\Models\NodeTasks\PullGitRepo;

use App\Models\NodeTasks\AbstractTaskMeta;

class PullGitRepoMeta extends AbstractTaskMeta
{
    public function __construct(
        public int $deploymentId,
        public string $process,
        public string $worker,
        public int $serviceId,
        public string $repo,
        public string $ref,
    ) {
        //
    }

    public function formattedHtml(): string
    {
        return "Pull Git Repository <code>{$this->repo}#{$this->ref}</code>";
    }
}
