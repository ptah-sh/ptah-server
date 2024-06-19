<?php

namespace App\Models\NodeData;

enum IpVersion: string
{
    case IPv4 = 'inet';
    case IPv6 = 'inet6';
}