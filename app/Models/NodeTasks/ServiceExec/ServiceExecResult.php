<?php

namespace App\Models\NodeTasks\ServiceExec;

use App\Models\NodeTasks\AbstractTaskResult;

class ServiceExecResult extends AbstractTaskResult
{
    public function __construct(
        public array $output
    ) {
        //
    }

    public function formattedHtml(): string
    {
        return implode('<br>', $this->output);
    }
}
