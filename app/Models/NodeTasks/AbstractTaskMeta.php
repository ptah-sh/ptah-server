<?php

namespace App\Models\NodeTasks;

use Spatie\LaravelData\Data;

abstract class AbstractTaskMeta extends Data
{
    abstract public function formattedHtml();

    // TODO: makes sense for each task define it's own payload. Examples:
    //   - storing Caddy config as a task makes it harder to rollback releases, as other services may have already contributed to the Caddy config.
    //   - dependent tasks. Sometimes one task may depend on the output of the other task.
    public function asPayload(): array
    {
        return [];
    }
}
