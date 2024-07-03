<?php

namespace App\Models\NodeTasks\UpdateService;

use App\Models\NodeTasks\AbstractTaskMeta;

class UpdateServiceMeta extends AbstractTaskMeta
{
    public function __construct(
        public int $deploymentId,
        public int $serviceId,
        public string $serviceName,
        public string $processName
    )
    {
        //
    }

    public function formattedHtml(): string
    {
        return "Update Docker process <code>{$this->processName}</code> in <code>$this->serviceName</code>";
    }
}
