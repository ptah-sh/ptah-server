<?php

namespace App\Models\NodeTasks\BuildImageWithDockerfile;

use App\Models\NodeTasks\AbstractTaskResult;

class BuildImageWithDockerfileResult extends AbstractTaskResult
{
    public function __construct(
    ) {
        //
    }

    public function formattedHtml(): string
    {
        return 'Done.';
    }
}
