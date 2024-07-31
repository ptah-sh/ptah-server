<?php

namespace App\Models\NodeTasks\UpdateAgentSymlink;

use App\Models\NodeTasks\AbstractTaskResult;

class UpdateAgentSymlinkResult extends AbstractTaskResult
{
    public function __construct(
    ) {
        //
    }

    public function formattedHtml(): string
    {
        return 'Success.';
    }
}
