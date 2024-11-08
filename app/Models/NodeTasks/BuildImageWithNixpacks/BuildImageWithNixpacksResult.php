<?php

namespace App\Models\NodeTasks\BuildImageWithNixpacks;

use App\Models\NodeTasks\AbstractTaskResult;

class BuildImageWithNixpacksResult extends AbstractTaskResult
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
