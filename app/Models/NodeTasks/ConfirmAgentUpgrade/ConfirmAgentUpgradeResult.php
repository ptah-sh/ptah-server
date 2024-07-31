<?php

namespace App\Models\NodeTasks\ConfirmAgentUpgrade;

use App\Models\NodeTasks\AbstractTaskResult;

class ConfirmAgentUpgradeResult extends AbstractTaskResult
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
