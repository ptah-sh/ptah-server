<?php

namespace App\Models\NodeTasks\CreateService;

use App\Models\NodeTasks\AbstractTaskMeta;

class CreateServiceMeta extends AbstractTaskMeta
{
    public function __construct(
        public int $deploymentId,
        public int $serviceId,
        public string $serviceName
    )
    {
        //
    }

    public function formattedHtml(): string
    {
        return "Create Docker Service <code>$this->serviceName</code>";
    }
}
