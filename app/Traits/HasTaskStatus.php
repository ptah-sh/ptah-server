<?php

namespace App\Traits;

use App\Casts\TaskPayloadCast;
use App\Models\NodeTask\TaskStatus;
use Illuminate\Database\Eloquent\Builder;

trait HasTaskStatus
{
    protected static function bootHasTaskStatus(): void
    {
        static::addGlobalScope('taskStatusCast', function (Builder $builder) {
            return $builder->withCasts([
                'status' => TaskStatus::class
            ])->orderBy('id');
        });
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where($query->qualifyColumn('status'), TaskStatus::Pending);
    }

    public function scopeRunning(Builder $query): Builder
    {
        return $query->where($query->qualifyColumn('status'), TaskStatus::Running);
    }

    public function scopeFailed(Builder $builder): Builder
    {
        return $builder->where($builder->qualifyColumn('status'), TaskStatus::Failed);
    }

    public function scopeUnsuccessful(Builder $builder): Builder
    {
        return $builder->whereIn($builder->qualifyColumn('status'), [TaskStatus::Failed, TaskStatus::Canceled]);
    }

    public function getIsPendingAttribute() : bool
    {
        return $this->status === TaskStatus::Pending;
    }

    public function getIsRunningAttribute() : bool
    {
        return $this->status === TaskStatus::Running;
    }

    public function getIsCompletedAttribute() : bool
    {
        return $this->status === TaskStatus::Completed;
    }

    public function scopeOfType(Builder $query, string $typeClass): Builder
    {
        return $query->where('type', TaskPayloadCast::TYPE_BY_PAYLOAD[$typeClass]);
    }

    public function scopeInProgress(Builder $query): Builder
    {
        return $query->whereIn($query->qualifyColumn('status'), [TaskStatus::Pending, TaskStatus::Running]);
    }
}