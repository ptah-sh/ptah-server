<?php

namespace App\Models\NodeTasks\ServiceExec;

use App\Models\NodeTasks\AbstractTaskMeta;

class ServiceExecMeta extends AbstractTaskMeta
{
    public function __construct(
        public int $serviceId,
        public string $command,
    ) {
        //
    }

    public function formattedHtml(): string
    {
        return "Execute <code>$this->command</code> on the service <code>$this->serviceId</code>.";
    }
}
