<?php

namespace App\Models\NodeTasks\LaunchService;

use App\Models\NodeTasks\AbstractTaskMeta;

class LaunchServiceMeta extends AbstractTaskMeta
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
        return "Launch Docker Service <code>{$this->dockerName}</code> of <code>{$this->serviceName}</code>";
    }
}
