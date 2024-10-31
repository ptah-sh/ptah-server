<?php

namespace App\Models\NodeData;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Data;

class HostData extends Data
{
    public function __construct(
        #[DataCollectionOf(HostNetwork::class)]
        #[Min(1)]
        public array $networks
    ) {}

    public function getPublicIpv4(): ?IpAddress
    {
        return collect($this->networks)
            ->flatMap(fn (HostNetwork $network) => $network->ips)
            ->first(fn (IpAddress $ip) => $ip->isPublic() && $ip->version === IpVersion::IPv4);
    }

    public function getPrivateIpv4(): ?IpAddress
    {
        return collect($this->networks)
            ->flatMap(fn (HostNetwork $network) => $network->ips)
            ->first(fn (IpAddress $ip) => ! $ip->isPublic() && $ip->version === IpVersion::IPv4);
    }
}
