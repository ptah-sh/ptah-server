<?php

namespace App\Models\NodeTasks\UpdateDirdConfig;

use App\Models\NodeTasks\AbstractTaskMeta;

class UpdateDirdConfigMeta extends AbstractTaskMeta
{
    public function __construct(
        public array $nodeAddresses,
        public array $dockerServices,
        public array $nodePorts,
    ) {}

    public function formattedHtml(): string
    {
        return 'Update DIRD Config';
    }
}
