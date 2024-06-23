<?php

namespace App\Models\NodeTasks;

use Illuminate\Database\Eloquent\Concerns\HasEvents;
use Spatie\LaravelData\Data;

abstract class AbstractTaskResult extends Data
{


    abstract public function formattedHtml();
}