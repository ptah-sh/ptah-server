<?php

namespace App\Models\DeploymentData\AppSource;

enum AppSourceType: string
{
    case DockerImage = 'docker_image';
    case GitWithDockerfile = 'git_with_dockerfile';
    case GitWithNixpacks = 'git_with_nixpacks';
}
