<?php

namespace App\Casts;

use App\Models\NodeTask;
use App\Models\NodeTask\CreateNetworkTaskResult;
use App\Models\NodeTask\ErrorResult;
use App\Models\NodeTask\InitSwarmTaskResult;
use InvalidArgumentException;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class TaskResultCast implements CastsAttributes
{

    public const TYPE_BY_RESULT = [
        CreateNetworkTaskResult::class => 0,
        InitSwarmTaskResult::class => 1
    ];

    public const RESULT_BY_TYPE = [
        0 => CreateNetworkTaskResult::class,
        1 => InitSwarmTaskResult::class
    ];

    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if (!($model instanceof NodeTask)) {
            throw new InvalidArgumentException('Model must be an instance of NodeTask');
        }

        if ($model->is_failed) {
            return ErrorResult::from($value);
        }

        if ($model->is_ended) {
            return self::RESULT_BY_TYPE[$model->type]::from($value);
        }

        return null;
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if (!($model instanceof NodeTask)) {
            throw new InvalidArgumentException('Model must be an instance of NodeTask');
        }

        return $value->toJson();
    }
}
