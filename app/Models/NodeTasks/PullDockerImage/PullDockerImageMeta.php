<?php

namespace App\Models\NodeTasks\PullDockerImage;

use App\Models\NodeTasks\AbstractTaskMeta;

class PullDockerImageMeta extends AbstractTaskMeta
{
    public function __construct(
        public string $dockerImage
    ) {
        //
    }

    public function formattedHtml(): string
    {
        return "Pull Docker Image <code>{$this->dockerImage}</code>";
    }
}
