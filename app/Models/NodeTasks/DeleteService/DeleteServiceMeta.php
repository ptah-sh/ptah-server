<?php

namespace App\Models\NodeTasks\DeleteService;

use App\Models\NodeTasks\AbstractTaskMeta;

class DeleteServiceMeta extends AbstractTaskMeta
{
    public function __construct(
        public int $serviceId,
        public string $processName,
        public string $serviceName,
    )
    {
        //
    }

    public function formattedHtml(): string
    {
        return "Delete process <code>{$this->processName}</code> of <code>{$this->serviceName}</code>";
    }
}
