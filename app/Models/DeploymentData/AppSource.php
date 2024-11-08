<?php

namespace App\Models\DeploymentData;

use App\Models\DeploymentData\AppSource\AppSourceType;
use App\Models\DeploymentData\AppSource\DockerSource;
use App\Models\DeploymentData\AppSource\GitWithDockerfileSource;
use App\Models\DeploymentData\AppSource\GitWithNixpacksSource;
use Spatie\LaravelData\Attributes\Validation\Enum;
use Spatie\LaravelData\Attributes\Validation\ProhibitedUnless;
use Spatie\LaravelData\Attributes\Validation\RequiredIf;
use Spatie\LaravelData\Data;

class AppSource extends Data
{
    public function __construct(
        #[Enum(AppSourceType::class)]
        public AppSourceType $type,
        #[RequiredIf('type', AppSourceType::DockerImage), ProhibitedUnless('type', AppSourceType::DockerImage)]
        public ?DockerSource $docker,
        #[RequiredIf('type', AppSourceType::GitWithDockerfile), ProhibitedUnless('type', AppSourceType::GitWithDockerfile)]
        public ?GitWithDockerfileSource $git,
        #[RequiredIf('type', AppSourceType::GitWithNixpacks), ProhibitedUnless('type', AppSourceType::GitWithNixpacks)]
        public ?GitWithNixpacksSource $nixpacks,
    ) {}
}
