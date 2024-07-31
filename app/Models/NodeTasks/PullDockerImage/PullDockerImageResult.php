<?php

namespace App\Models\NodeTasks\PullDockerImage;

use App\Models\NodeTasks\AbstractTaskResult;

class PullDockerImageResult extends AbstractTaskResult
{
    public function __construct(
        /* @var string[] */
        public array $output
    ) {
        //
    }

    public function formattedHtml(): string
    {
        return implode("<br>\n", $this->output);
    }
}
