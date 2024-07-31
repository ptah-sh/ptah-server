<?php

namespace App\Models\SwarmData;

use Spatie\LaravelData\Data;

class S3Storage extends Data
{
    public function __construct(
        public string $id,
        public string $name,
        public string $endpoint,
        public string $accessKey,
        public string $secretKey,
        public string $region,
        public string $bucket,
        public string $pathPrefix,
        public ?string $dockerName,
    ) {}

    public function sameAs(S3Storage $s3Storage): bool
    {
        return $this->endpoint === $s3Storage->endpoint
            && $this->region === $s3Storage->region
            && $this->bucket === $s3Storage->bucket
            && $this->pathPrefix === $s3Storage->pathPrefix
            && empty($this->accessKey)
            && empty($this->secretKey);
    }
}
