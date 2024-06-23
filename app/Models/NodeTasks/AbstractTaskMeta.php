<?php

namespace App\Models\NodeTasks;

use Spatie\LaravelData\Data;

abstract class AbstractTaskMeta extends Data {
    abstract public function formattedHtml();

    public function toPayload(): array
    {
        return $this->toArray();
    }
}
