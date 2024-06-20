<?php

namespace App\Models\NodeTask;

class CreateNetworkTaskPayload extends AbstractTaskPayload
{
    public function __construct(
        public string $name,
    ) {

    }

    public function formattedHtml(): string
    {
        return 'Create Docker Network <code>'.$this->name.'</code>';
    }
}