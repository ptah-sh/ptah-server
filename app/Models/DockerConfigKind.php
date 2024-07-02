<?php

namespace App\Models;

enum DockerConfigKind: string
{
    case RegistryCredentials = 'registry-credentials';
}
