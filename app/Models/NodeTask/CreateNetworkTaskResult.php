<?php

namespace App\Models\NodeTask;

use Spatie\LaravelData\Data;

class CreateNetworkTaskResult extends AbstractTaskResult
{
    public function __construct(
        public DockerId $docker
    ) {

    }

    public function formattedHtml(): string
    {
        return 'Network ID: <code>' . $this->docker->id . '</code>';
    }
}