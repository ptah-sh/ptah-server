<?php

namespace App\Models\DeploymentData;

use App\Models\NodeTasks\DockerId;
use App\Models\Service;
use Illuminate\Support\Str;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Data;

class ConfigFile extends Data
{
    public function __construct(
        public string $path,
        public ?string $content,
        public ?string $dockerName,
    )
    {
        $this->content = $content ?? '';
    }

    public function hash(): string
    {
        // TODO: use cache?
        return md5($this->content);
    }

    public function base64(): string
    {
        return base64_encode($this->content);
    }

    public function sameAs(?ConfigFile $older): bool
    {
        return $older !== null && $this->hash() === $older->hash();
    }
}
