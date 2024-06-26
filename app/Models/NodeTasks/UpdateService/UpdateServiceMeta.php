<?php

namespace App\Models\NodeTasks\UpdateService;

use App\Models\NodeTasks\AbstractTaskMeta;

class UpdateServiceMeta extends AbstractTaskMeta
{
    public function __construct(
        public int $serviceId,
        public string $serviceName
    )
    {
        //
    }

    public function formattedHtml(): string
    {
        return "Update Docker Service <code>$this->serviceName</code>";
    }
}
