<?php

namespace App\Models\NodeTasks\BuildImageWithDockerfile;

use App\Models\NodeTasks\AbstractTaskMeta;

class BuildImageWithDockerfileMeta extends AbstractTaskMeta
{
    public function __construct(
        public string $dockerfilePath,
        public string $dockerImage,
    ) {
        //
    }

    public function formattedHtml(): string
    {
        return "Building docker image <code>{$this->dockerImage}</code> from <code>{$this->dockerfilePath}</code>";
    }
}
