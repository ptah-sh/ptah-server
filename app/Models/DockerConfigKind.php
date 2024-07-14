<?php

namespace App\Models;

enum DockerConfigKind: string
{
    case RegistryCredentials = 'registry-credentials';
    case S3Storage = 's3-storage';
}
