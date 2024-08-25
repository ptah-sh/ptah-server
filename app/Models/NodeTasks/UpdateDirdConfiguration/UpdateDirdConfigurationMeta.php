<?php

namespace App\Models\NodeTasks\UpdateDirdConfiguration;

use App\Models\NodeTasks\AbstractTaskMeta;

class UpdateDirdConfigurationMeta extends AbstractTaskMeta
{
    public function __construct(
        public array $nodeAddresses,
        public array $dockerServices,
        public array $nodePorts,
    ) {
        //
    }

    public function formattedHtml(): string
    {
        return 'Update DIRD Configuration';
    }
}
