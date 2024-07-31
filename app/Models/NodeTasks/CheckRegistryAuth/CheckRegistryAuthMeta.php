<?php

namespace App\Models\NodeTasks\CheckRegistryAuth;

use App\Models\NodeTasks\AbstractTaskMeta;

class CheckRegistryAuthMeta extends AbstractTaskMeta
{
    public function __construct(
        public string $registryName
    ) {
        //
    }

    public function formattedHtml(): string
    {
        return 'Check registry auth <code>'.$this->registryName.'</code>';
    }
}
