<?php

namespace App\Models\NodeTasks;

class ErrorResult extends AbstractTaskResult
{
    public function __construct(
        public string $message
    ) {}

    public function formattedHtml(): string
    {
        return $this->message;
    }
}
