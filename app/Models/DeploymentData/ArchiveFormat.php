<?php

namespace App\Models\DeploymentData;

enum ArchiveFormat: string
{
    case TarGz = 'tar.gz';
    case Zip = 'zip';
}
