<?php

namespace App\Models;

use App\Models\NodeData\DockerData;
use App\Models\NodeData\HostData;
use App\Models\NodeData\NodeRole;
use Spatie\LaravelData\Data;

class NodeData extends Data
{
    public function __construct(
        public string $version,
        public DockerData $docker,
        public HostData $host,
        public NodeRole $role,
        public string $address,
    ) {}
}
