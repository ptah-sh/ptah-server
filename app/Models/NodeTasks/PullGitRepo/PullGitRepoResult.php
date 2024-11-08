<?php

namespace App\Models\NodeTasks\PullGitRepo;

use App\Models\NodeTasks\AbstractTaskResult;

class PullGitRepoResult extends AbstractTaskResult
{
    public function __construct(
        public array $output,
    ) {
        //
    }

    public function formattedHtml(): string
    {
        return implode('<br>', $this->output);
    }
}
