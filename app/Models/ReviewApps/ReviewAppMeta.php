<?php

namespace App\Models\ReviewApps;

use Spatie\LaravelData\Attributes\Validation\AlphaDash;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Attributes\Validation\Url;
use Spatie\LaravelData\Data;

class ReviewAppMeta extends Data
{
    public function __construct(
        #[AlphaDash, Rule('ascii')]
        public string $ref,
        #[Url]
        public string $refUrl,
    ) {}
}
