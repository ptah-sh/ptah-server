<?php

namespace App\Models\NodeTasks;

use Spatie\LaravelData\Data;

abstract class AbstractTaskResult extends Data
{
    abstract public function formattedHtml();
}
