<?php

namespace App\Models\NodeTasks\BuildImageWithNixpacks;

use App\Models\NodeTasks\AbstractTaskMeta;

class BuildImageWithNixpacksMeta extends AbstractTaskMeta
{
    public function __construct(
        public string $nixpacksFilePath,
        public string $dockerImage,
    ) {
        //
    }

    public function formattedHtml(): string
    {
        return "Building docker image <code>{$this->dockerImage}</code> from <code>{$this->nixpacksFilePath}</code>";
    }
}
