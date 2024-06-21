<?php

namespace App\Casts;

use App\Models\NodeTask;
use App\Models\NodeTask\CreateNetworkTaskPayload;
use App\Models\NodeTask\InitSwarmTaskPayload;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class TaskPayloadCast implements CastsAttributes
{
    public const TYPE_BY_PAYLOAD = [
        CreateNetworkTaskPayload::class => 0,
        InitSwarmTaskPayload::class => 1
    ];

    public const PAYLOAD_BY_TYPE = [
        0 => CreateNetworkTaskPayload::class,
        1 => InitSwarmTaskPayload::class
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

//        if (!isset($attributes['type'])) {
//            return null;
//        }
//dd($model->type, $attributes);
        return self::PAYLOAD_BY_TYPE[$attributes['type']]::from($value);
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

        if (is_string($value)) {
            return $value;
        }

        return $value->toJson();
    }
}
