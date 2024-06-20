<?php

namespace App\Models\NodeTask;

use Spatie\LaravelData\Data;

abstract class AbstractTaskPayload extends Data {
    abstract public function formattedHtml();
}
