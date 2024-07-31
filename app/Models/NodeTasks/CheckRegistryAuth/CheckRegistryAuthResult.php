<?php

namespace App\Models\NodeTasks\CheckRegistryAuth;

use App\Models\NodeTasks\AbstractTaskResult;

class CheckRegistryAuthResult extends AbstractTaskResult
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
