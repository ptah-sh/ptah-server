<?php

namespace App\Models\NodeTasks;

enum TaskStatus: string
{
    case Pending = 'pending';
    case Running = 'running';
    case Completed = 'completed';
    case Failed = 'failed';
    case Canceled = 'canceled';

    public function isEnded(): bool
    {
        return $this->value === TaskStatus::Completed->value
            || $this->value === TaskStatus::Failed->value
            || $this->value === TaskStatus::Canceled->value;
    }
}
