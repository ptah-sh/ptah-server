<?php

namespace App\Models\NodeTasks\UpdateService;

use App\Models\NodeTasks\AbstractTaskMeta;

class UpdateServiceMeta extends AbstractTaskMeta
{
    public function __construct(
        public int $deploymentId,
        public int $serviceId,
        public string $serviceName,
        public string $dockerName
    ) {
        //
    }

    public function formattedHtml(): string
    {
        return "Update Docker Service <code>{$this->dockerName}</code> of <code>$this->serviceName</code>";
    }
}
