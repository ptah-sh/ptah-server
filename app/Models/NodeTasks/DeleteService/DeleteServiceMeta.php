<?php

namespace App\Models\NodeTasks\DeleteService;

use App\Models\NodeTasks\AbstractTaskMeta;

class DeleteServiceMeta extends AbstractTaskMeta
{
    public function __construct(
        public int $serviceId,
        public string $dockerName,
        public string $serviceName,
    ) {
        //
    }

    public function formattedHtml(): string
    {
        return "Delete Docker Service <code>{$this->dockerName}</code> of <code>{$this->serviceName}</code>";
    }
}
