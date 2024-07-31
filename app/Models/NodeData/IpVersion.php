<?php

namespace App\Models\NodeData;

enum IpVersion: string
{
    case IPv4 = 'ipv4';
    case IPv6 = 'ipv6';
}
