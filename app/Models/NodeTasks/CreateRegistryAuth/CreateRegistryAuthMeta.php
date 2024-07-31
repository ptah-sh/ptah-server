<?php

namespace App\Models\NodeTasks\CreateRegistryAuth;

use App\Models\NodeTasks\AbstractTaskMeta;

class CreateRegistryAuthMeta extends AbstractTaskMeta
{
    public function __construct(
        public string $registryName,
    ) {
        //
    }

    public function formattedHtml(): string
    {
        return "Create Docker Config for registry <code>{$this->registryName}</code>";
    }
}
