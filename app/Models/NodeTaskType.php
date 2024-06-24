<?php

namespace App\Models;

use App\Events\NodeTasks\CreateConfig\CreateConfigCompleted;
use App\Events\NodeTasks\CreateConfig\CreateConfigFailed;
use App\Events\NodeTasks\CreateNetwork\CreateNetworkCompleted;
use App\Events\NodeTasks\CreateNetwork\CreateNetworkFailed;
use App\Events\NodeTasks\CreateSecret\CreateSecretCompleted;
use App\Events\NodeTasks\CreateSecret\CreateSecretFailed;
use App\Events\NodeTasks\CreateService\CreateServiceCompleted;
use App\Events\NodeTasks\CreateService\CreateServiceFailed;
use App\Events\NodeTasks\InitSwarm\InitSwarmCompleted;
use App\Events\NodeTasks\InitSwarm\InitSwarmFailed;
use App\Events\NodeTasks\RebuildCaddyConfig\RebuildCaddyConfigCompleted;
use App\Events\NodeTasks\RebuildCaddyConfig\RebuildCaddyConfigFailed;
use App\Models\NodeTasks\CreateConfig\CreateConfigMeta;
use App\Models\NodeTasks\CreateConfig\CreateConfigResult;
use App\Models\NodeTasks\CreateNetwork\CreateNetworkMeta;
use App\Models\NodeTasks\CreateNetwork\CreateNetworkResult;
use App\Models\NodeTasks\CreateSecret\CreateSecretMeta;
use App\Models\NodeTasks\CreateSecret\CreateSecretResult;
use App\Models\NodeTasks\CreateService\CreateServiceMeta;
use App\Models\NodeTasks\CreateService\CreateServiceResult;
use App\Models\NodeTasks\InitSwarm\InitSwarmMeta;
use App\Models\NodeTasks\InitSwarm\InitSwarmResult;
use App\Models\NodeTasks\ApplyCaddyConfig\ApplyCaddyConfigMeta;
use App\Models\NodeTasks\ApplyCaddyConfig\ApplyCaddyConfigResult;

// Mb use dynamic class names? $class = "{$this->name}Payload"; ??
enum NodeTaskType: int
{
    case CreateNetwork = 0;
    case InitSwarm = 1;
    case CreateConfig = 2;
    case CreateSecret = 3;
    case CreateService = 4;
    case ApplyCaddyConfig = 5;

    public function meta(): string
    {
        return match ($this) {
            self::CreateNetwork => CreateNetworkMeta::class,
            self::InitSwarm => InitSwarmMeta::class,
            self::CreateConfig => CreateConfigMeta::class,
            self::CreateSecret => CreateSecretMeta::class,
            self::CreateService => CreateServiceMeta::class,
            self::ApplyCaddyConfig => ApplyCaddyConfigMeta::class,
        };
    }

    public function result(): string
    {
        return match ($this) {
            self::CreateNetwork => CreateNetworkResult::class,
            self::InitSwarm => InitSwarmResult::class,
            self::CreateConfig => CreateConfigResult::class,
            self::CreateSecret => CreateSecretResult::class,
            self::CreateService => CreateServiceResult::class,
            self::ApplyCaddyConfig => ApplyCaddyConfigResult::class,
        };
    }

    public function completed(): string
    {
        return match ($this) {
            self::CreateNetwork => CreateNetworkCompleted::class,
            self::InitSwarm => InitSwarmCompleted::class,
            self::CreateConfig => CreateConfigCompleted::class,
            self::CreateSecret => CreateSecretCompleted::class,
            self::CreateService => CreateServiceCompleted::class,
            self::ApplyCaddyConfig => RebuildCaddyConfigCompleted::class,
        };
    }

    public function failed(): string
    {
        return match ($this) {
            self::CreateNetwork => CreateNetworkFailed::class,
            self::InitSwarm => InitSwarmFailed::class,
            self::CreateConfig => CreateConfigFailed::class,
            self::CreateSecret => CreateSecretFailed::class,
            self::CreateService => CreateServiceFailed::class,
            self::ApplyCaddyConfig => RebuildCaddyConfigFailed::class,
        };
    }
}
