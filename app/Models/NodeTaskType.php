<?php

namespace App\Models;

use App\Events\NodeTasks\BuildImageWithDockerfile\BuildImageWithDockerfileCompleted;
use App\Events\NodeTasks\BuildImageWithDockerfile\BuildImageWithDockerfileFailed;
use App\Events\NodeTasks\BuildImageWithNixpacks\BuildImageWithNixpacksCompleted;
use App\Events\NodeTasks\BuildImageWithNixpacks\BuildImageWithNixpacksFailed;
use App\Events\NodeTasks\CheckRegistryAuth\CheckRegistryAuthCompleted;
use App\Events\NodeTasks\CheckRegistryAuth\CheckRegistryAuthFailed;
use App\Events\NodeTasks\CheckS3Storage\CheckS3StorageCompleted;
use App\Events\NodeTasks\CheckS3Storage\CheckS3StorageFailed;
use App\Events\NodeTasks\ConfirmAgentUpgrade\ConfirmAgentUpgradeCompleted;
use App\Events\NodeTasks\ConfirmAgentUpgrade\ConfirmAgentUpgradeFailed;
use App\Events\NodeTasks\CreateConfig\CreateConfigCompleted;
use App\Events\NodeTasks\CreateConfig\CreateConfigFailed;
use App\Events\NodeTasks\CreateNetwork\CreateNetworkCompleted;
use App\Events\NodeTasks\CreateNetwork\CreateNetworkFailed;
use App\Events\NodeTasks\CreateRegistryAuth\CreateRegistryAuthCompleted;
use App\Events\NodeTasks\CreateRegistryAuth\CreateRegistryAuthFailed;
use App\Events\NodeTasks\CreateS3Storage\CreateS3StorageCompleted;
use App\Events\NodeTasks\CreateS3Storage\CreateS3StorageFailed;
use App\Events\NodeTasks\CreateSecret\CreateSecretCompleted;
use App\Events\NodeTasks\CreateSecret\CreateSecretFailed;
use App\Events\NodeTasks\CreateService\CreateServiceCompleted;
use App\Events\NodeTasks\CreateService\CreateServiceFailed;
use App\Events\NodeTasks\DeleteService\DeleteServiceCompleted;
use App\Events\NodeTasks\DeleteService\DeleteServiceFailed;
use App\Events\NodeTasks\DownloadAgentUpgrade\DownloadAgentUpgradeCompleted;
use App\Events\NodeTasks\DownloadAgentUpgrade\DownloadAgentUpgradeFailed;
use App\Events\NodeTasks\DownloadS3File\DownloadS3FileCompleted;
use App\Events\NodeTasks\DownloadS3File\DownloadS3FileFailed;
use App\Events\NodeTasks\InitSwarm\InitSwarmCompleted;
use App\Events\NodeTasks\InitSwarm\InitSwarmFailed;
use App\Events\NodeTasks\JoinSwarm\JoinSwarmCompleted;
use App\Events\NodeTasks\JoinSwarm\JoinSwarmFailed;
use App\Events\NodeTasks\LaunchService\LaunchServiceCompleted;
use App\Events\NodeTasks\LaunchService\LaunchServiceFailed;
use App\Events\NodeTasks\PruneDockerRegistry\PruneDockerRegistryCompleted;
use App\Events\NodeTasks\PruneDockerRegistry\PruneDockerRegistryFailed;
use App\Events\NodeTasks\PullDockerImage\PullDockerImageCompleted;
use App\Events\NodeTasks\PullDockerImage\PullDockerImageFailed;
use App\Events\NodeTasks\PullGitRepo\PullGitRepoCompleted;
use App\Events\NodeTasks\PullGitRepo\PullGitRepoFailed;
use App\Events\NodeTasks\RebuildCaddyConfig\ApplyCaddyConfigCompleted;
use App\Events\NodeTasks\RebuildCaddyConfig\ApplyCaddyConfigFailed;
use App\Events\NodeTasks\RemoveS3File\RemoveS3FileCompleted;
use App\Events\NodeTasks\RemoveS3File\RemoveS3FileFailed;
use App\Events\NodeTasks\ServiceExec\ServiceExecCompleted;
use App\Events\NodeTasks\ServiceExec\ServiceExecFailed;
use App\Events\NodeTasks\UpdateAgentSymlink\UpdateAgentSymlinkCompleted;
use App\Events\NodeTasks\UpdateAgentSymlink\UpdateAgentSymlinkFailed;
use App\Events\NodeTasks\UpdateDirdConfig\UpdateDirdConfigCompleted;
use App\Events\NodeTasks\UpdateDirdConfig\UpdateDirdConfigFailed;
use App\Events\NodeTasks\UpdateNode\UpdateCurrentNodeCompleted;
use App\Events\NodeTasks\UpdateNode\UpdateCurrentNodeFailed;
use App\Events\NodeTasks\UpdateService\UpdateServiceCompleted;
use App\Events\NodeTasks\UpdateService\UpdateServiceFailed;
use App\Events\NodeTasks\UploadS3File\UploadS3FileCompleted;
use App\Events\NodeTasks\UploadS3File\UploadS3FileFailed;
use App\Models\NodeTasks\ApplyCaddyConfig\ApplyCaddyConfigMeta;
use App\Models\NodeTasks\ApplyCaddyConfig\ApplyCaddyConfigResult;
use App\Models\NodeTasks\BuildImageWithDockerfile\BuildImageWithDockerfileMeta;
use App\Models\NodeTasks\BuildImageWithDockerfile\BuildImageWithDockerfileResult;
use App\Models\NodeTasks\BuildImageWithNixpacks\BuildImageWithNixpacksMeta;
use App\Models\NodeTasks\BuildImageWithNixpacks\BuildImageWithNixpacksResult;
use App\Models\NodeTasks\CheckRegistryAuth\CheckRegistryAuthMeta;
use App\Models\NodeTasks\CheckRegistryAuth\CheckRegistryAuthResult;
use App\Models\NodeTasks\CheckS3Storage\CheckS3StorageMeta;
use App\Models\NodeTasks\CheckS3Storage\CheckS3StorageResult;
use App\Models\NodeTasks\ConfirmAgentUpgrade\ConfirmAgentUpgradeMeta;
use App\Models\NodeTasks\ConfirmAgentUpgrade\ConfirmAgentUpgradeResult;
use App\Models\NodeTasks\CreateConfig\CreateConfigMeta;
use App\Models\NodeTasks\CreateConfig\CreateConfigResult;
use App\Models\NodeTasks\CreateNetwork\CreateNetworkMeta;
use App\Models\NodeTasks\CreateNetwork\CreateNetworkResult;
use App\Models\NodeTasks\CreateRegistryAuth\CreateRegistryAuthMeta;
use App\Models\NodeTasks\CreateRegistryAuth\CreateRegistryAuthResult;
use App\Models\NodeTasks\CreateS3Storage\CreateS3StorageMeta;
use App\Models\NodeTasks\CreateS3Storage\CreateS3StorageResult;
use App\Models\NodeTasks\CreateSecret\CreateSecretMeta;
use App\Models\NodeTasks\CreateSecret\CreateSecretResult;
use App\Models\NodeTasks\CreateService\CreateServiceMeta;
use App\Models\NodeTasks\CreateService\CreateServiceResult;
use App\Models\NodeTasks\DeleteService\DeleteServiceMeta;
use App\Models\NodeTasks\DeleteService\DeleteServiceResult;
use App\Models\NodeTasks\DownloadAgentUpgrade\DownloadAgentUpgradeMeta;
use App\Models\NodeTasks\DownloadAgentUpgrade\DownloadAgentUpgradeResult;
use App\Models\NodeTasks\DownloadS3File\DownloadS3FileMeta;
use App\Models\NodeTasks\DownloadS3File\DownloadS3FileResult;
use App\Models\NodeTasks\InitSwarm\InitSwarmMeta;
use App\Models\NodeTasks\InitSwarm\InitSwarmResult;
use App\Models\NodeTasks\JoinSwarm\JoinSwarmMeta;
use App\Models\NodeTasks\JoinSwarm\JoinSwarmResult;
use App\Models\NodeTasks\LaunchService\LaunchServiceMeta;
use App\Models\NodeTasks\LaunchService\LaunchServiceResult;
use App\Models\NodeTasks\PruneDockerRegistry\PruneDockerRegistryMeta;
use App\Models\NodeTasks\PruneDockerRegistry\PruneDockerRegistryResult;
use App\Models\NodeTasks\PullDockerImage\PullDockerImageMeta;
use App\Models\NodeTasks\PullDockerImage\PullDockerImageResult;
use App\Models\NodeTasks\PullGitRepo\PullGitRepoMeta;
use App\Models\NodeTasks\PullGitRepo\PullGitRepoResult;
use App\Models\NodeTasks\RemoveS3File\RemoveS3FileMeta;
use App\Models\NodeTasks\RemoveS3File\RemoveS3FileResult;
use App\Models\NodeTasks\ServiceExec\ServiceExecMeta;
use App\Models\NodeTasks\ServiceExec\ServiceExecResult;
use App\Models\NodeTasks\UpdateAgentSymlink\UpdateAgentSymlinkMeta;
use App\Models\NodeTasks\UpdateAgentSymlink\UpdateAgentSymlinkResult;
use App\Models\NodeTasks\UpdateCurrentNode\UpdateCurrentNodeMeta;
use App\Models\NodeTasks\UpdateCurrentNode\UpdateCurrentNodeResult;
use App\Models\NodeTasks\UpdateDirdConfig\UpdateDirdConfigMeta;
use App\Models\NodeTasks\UpdateDirdConfig\UpdateDirdConfigResult;
use App\Models\NodeTasks\UpdateService\UpdateServiceMeta;
use App\Models\NodeTasks\UpdateService\UpdateServiceResult;
use App\Models\NodeTasks\UploadS3File\UploadS3FileMeta;
use App\Models\NodeTasks\UploadS3File\UploadS3FileResult;

// Mb use dynamic class names? $class = "{$this->name}Payload"; ??
enum NodeTaskType: int
{
    case CreateNetwork = 0;
    case InitSwarm = 1;
    case CreateConfig = 2;
    case CreateSecret = 3;
    case CreateService = 4;
    case ApplyCaddyConfig = 5;
    case UpdateService = 6;
    case UpdateCurrentNode = 7;
    case DeleteService = 8;
    case DownloadAgentUpgrade = 9;
    case UpdateAgentSymlink = 10;
    case ConfirmAgentUpgrade = 11;
    case CreateRegistryAuth = 12;
    case CheckRegistryAuth = 13;
    case PullDockerImage = 14;
    case CreateS3Storage = 15;
    case CheckS3Storage = 16;
    case ServiceExec = 17;
    case UploadS3File = 18;
    case JoinSwarm = 19;
    case UpdateDirdConfig = 20;
    case LaunchService = 21;
    case DownloadS3File = 22;
    case RemoveS3File = 23;
    case PullGitRepo = 24;
    case BuildImageWithDockerfile = 25;
    case BuildImageWithNixpacks = 26;
    case PruneDockerRegistry = 27;

    public function meta(): string
    {
        return match ($this) {
            self::CreateNetwork => CreateNetworkMeta::class,
            self::InitSwarm => InitSwarmMeta::class,
            self::CreateConfig => CreateConfigMeta::class,
            self::CreateSecret => CreateSecretMeta::class,
            self::CreateService => CreateServiceMeta::class,
            self::ApplyCaddyConfig => ApplyCaddyConfigMeta::class,
            self::UpdateService => UpdateServiceMeta::class,
            self::UpdateCurrentNode => UpdateCurrentNodeMeta::class,
            self::DeleteService => DeleteServiceMeta::class,
            self::DownloadAgentUpgrade => DownloadAgentUpgradeMeta::class,
            self::UpdateAgentSymlink => UpdateAgentSymlinkMeta::class,
            self::ConfirmAgentUpgrade => ConfirmAgentUpgradeMeta::class,
            self::CreateRegistryAuth => CreateRegistryAuthMeta::class,
            self::CheckRegistryAuth => CheckRegistryAuthMeta::class,
            self::PullDockerImage => PullDockerImageMeta::class,
            self::CreateS3Storage => CreateS3StorageMeta::class,
            self::CheckS3Storage => CheckS3StorageMeta::class,
            self::ServiceExec => ServiceExecMeta::class,
            self::UploadS3File => UploadS3FileMeta::class,
            self::JoinSwarm => JoinSwarmMeta::class,
            self::UpdateDirdConfig => UpdateDirdConfigMeta::class,
            self::LaunchService => LaunchServiceMeta::class,
            self::DownloadS3File => DownloadS3FileMeta::class,
            self::RemoveS3File => RemoveS3FileMeta::class,
            self::PullGitRepo => PullGitRepoMeta::class,
            self::BuildImageWithDockerfile => BuildImageWithDockerfileMeta::class,
            self::BuildImageWithNixpacks => BuildImageWithNixpacksMeta::class,
            self::PruneDockerRegistry => PruneDockerRegistryMeta::class,
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
            self::UpdateService => UpdateServiceResult::class,
            self::UpdateCurrentNode => UpdateCurrentNodeResult::class,
            self::DeleteService => DeleteServiceResult::class,
            self::DownloadAgentUpgrade => DownloadAgentUpgradeResult::class,
            self::UpdateAgentSymlink => UpdateAgentSymlinkResult::class,
            self::ConfirmAgentUpgrade => ConfirmAgentUpgradeResult::class,
            self::CreateRegistryAuth => CreateRegistryAuthResult::class,
            self::CheckRegistryAuth => CheckRegistryAuthResult::class,
            self::PullDockerImage => PullDockerImageResult::class,
            self::CreateS3Storage => CreateS3StorageResult::class,
            self::CheckS3Storage => CheckS3StorageResult::class,
            self::ServiceExec => ServiceExecResult::class,
            self::UploadS3File => UploadS3FileResult::class,
            self::JoinSwarm => JoinSwarmResult::class,
            self::UpdateDirdConfig => UpdateDirdConfigResult::class,
            self::LaunchService => LaunchServiceResult::class,
            self::DownloadS3File => DownloadS3FileResult::class,
            self::RemoveS3File => RemoveS3FileResult::class,
            self::PullGitRepo => PullGitRepoResult::class,
            self::BuildImageWithDockerfile => BuildImageWithDockerfileResult::class,
            self::BuildImageWithNixpacks => BuildImageWithNixpacksResult::class,
            self::PruneDockerRegistry => PruneDockerRegistryResult::class,
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
            self::ApplyCaddyConfig => ApplyCaddyConfigCompleted::class,
            self::UpdateService => UpdateServiceCompleted::class,
            self::UpdateCurrentNode => UpdateCurrentNodeCompleted::class,
            self::DeleteService => DeleteServiceCompleted::class,
            self::DownloadAgentUpgrade => DownloadAgentUpgradeCompleted::class,
            self::UpdateAgentSymlink => UpdateAgentSymlinkCompleted::class,
            self::ConfirmAgentUpgrade => ConfirmAgentUpgradeCompleted::class,
            self::CreateRegistryAuth => CreateRegistryAuthCompleted::class,
            self::CheckRegistryAuth => CheckRegistryAuthCompleted::class,
            self::PullDockerImage => PullDockerImageCompleted::class,
            self::CreateS3Storage => CreateS3StorageCompleted::class,
            self::CheckS3Storage => CheckS3StorageCompleted::class,
            self::ServiceExec => ServiceExecCompleted::class,
            self::UploadS3File => UploadS3FileCompleted::class,
            self::JoinSwarm => JoinSwarmCompleted::class,
            self::UpdateDirdConfig => UpdateDirdConfigCompleted::class,
            self::LaunchService => LaunchServiceCompleted::class,
            self::DownloadS3File => DownloadS3FileCompleted::class,
            self::RemoveS3File => RemoveS3FileCompleted::class,
            self::PullGitRepo => PullGitRepoCompleted::class,
            self::BuildImageWithDockerfile => BuildImageWithDockerfileCompleted::class,
            self::BuildImageWithNixpacks => BuildImageWithNixpacksCompleted::class,
            self::PruneDockerRegistry => PruneDockerRegistryCompleted::class,
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
            self::ApplyCaddyConfig => ApplyCaddyConfigFailed::class,
            self::UpdateService => UpdateServiceFailed::class,
            self::UpdateCurrentNode => UpdateCurrentNodeFailed::class,
            self::DeleteService => DeleteServiceFailed::class,
            self::DownloadAgentUpgrade => DownloadAgentUpgradeFailed::class,
            self::UpdateAgentSymlink => UpdateAgentSymlinkFailed::class,
            self::ConfirmAgentUpgrade => ConfirmAgentUpgradeFailed::class,
            self::CreateRegistryAuth => CreateRegistryAuthFailed::class,
            self::CheckRegistryAuth => CheckRegistryAuthFailed::class,
            self::PullDockerImage => PullDockerImageFailed::class,
            self::CreateS3Storage => CreateS3StorageFailed::class,
            self::CheckS3Storage => CheckS3StorageFailed::class,
            self::ServiceExec => ServiceExecFailed::class,
            self::UploadS3File => UploadS3FileFailed::class,
            self::JoinSwarm => JoinSwarmFailed::class,
            self::UpdateDirdConfig => UpdateDirdConfigFailed::class,
            self::LaunchService => LaunchServiceFailed::class,
            self::DownloadS3File => DownloadS3FileFailed::class,
            self::RemoveS3File => RemoveS3FileFailed::class,
            self::PullGitRepo => PullGitRepoFailed::class,
            self::BuildImageWithDockerfile => BuildImageWithDockerfileFailed::class,
            self::BuildImageWithNixpacks => BuildImageWithNixpacksFailed::class,
            self::PruneDockerRegistry => PruneDockerRegistryFailed::class,
        };
    }
}
