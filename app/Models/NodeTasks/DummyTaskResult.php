<?php

namespace App\Models\NodeTasks;

// TODO: remove this once https://github.com/ptah-sh/ptah-agent/issues/33 is done
class DummyTaskResult extends AbstractTaskResult
{
    public function formattedHtml(): string
    {
        return 'self-hosted';
    }
}
