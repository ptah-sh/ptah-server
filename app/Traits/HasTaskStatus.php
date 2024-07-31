<?php

namespace App\Traits;

use App\Models\NodeTask;
use App\Models\NodeTaskGroup;
use App\Models\NodeTaskGroupType;
use App\Models\NodeTasks\TaskStatus;
use App\Models\NodeTaskType;
use Illuminate\Database\Eloquent\Builder;
use InvalidArgumentException;

trait HasTaskStatus
{
    protected static function bootHasTaskStatus(): void
    {
        static::addGlobalScope('taskStatusCast', function (Builder $builder) {
            return $builder->withCasts([
                'status' => TaskStatus::class,
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

    public function scopeCompleted(Builder $builder): Builder
    {
        return $builder->where($builder->qualifyColumn('status'), TaskStatus::Completed);
    }

    public function scopeUnsuccessful(Builder $builder): Builder
    {
        return $builder->whereIn($builder->qualifyColumn('status'), [TaskStatus::Failed, TaskStatus::Canceled]);
    }

    public function getIsPendingAttribute(): bool
    {
        return $this->status === TaskStatus::Pending;
    }

    public function getIsRunningAttribute(): bool
    {
        return $this->status === TaskStatus::Running;
    }

    public function getIsCompletedAttribute(): bool
    {
        return $this->status === TaskStatus::Completed;
    }

    public function scopeOfType(Builder $query, NodeTaskType|NodeTaskGroupType $typeClass): Builder
    {
        if ($this instanceof NodeTaskGroup && ! ($typeClass instanceof NodeTaskGroupType)) {
            throw new InvalidArgumentException('typeClass must be an instance of NodeTaskGroupType');
        }

        if ($this instanceof NodeTask && ! ($typeClass instanceof NodeTaskType)) {
            throw new InvalidArgumentException('typeClass must be an instance of NodeTaskType');
        }

        return $query->where('type', $typeClass->value);
    }

    public function scopeInProgress(Builder $query): Builder
    {
        return $query->whereIn($query->qualifyColumn('status'), [TaskStatus::Pending, TaskStatus::Running]);
    }
}
