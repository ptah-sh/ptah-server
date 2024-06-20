<?php

namespace App\Models\NodeTask;

use Spatie\LaravelData\Data;

abstract class AbstractTaskResult extends Data
{
    abstract public function formattedHtml();
}