<?php

namespace App\Models;

use App\Models\NodeTasks\TaskStatus;
use App\Traits\HasOwningTeam;
use App\Traits\HasTaskStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class NodeTaskGroup extends Model
{
    use HasFactory;
    use HasOwningTeam;
    use HasTaskStatus;

    protected $fillable = [
        'type',
        'swarm_id',
        'node_id',
        'invoker_id',
        'team_id',
    ];

    public function swarm(): BelongsTo
    {
        return $this->belongsTo(Swarm::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(NodeTask::class, 'task_group_id');
    }

    public function latestTask(): HasOne
    {
        return $this->hasOne(NodeTask::class, 'task_group_id')->whereNot('status', TaskStatus::Canceled)->latest('id');
    }

    public function allTasksEnded(): bool
    {
        return ! $this->tasks()->whereNull('ended_at')->exists();
    }

    public function node(): BelongsTo
    {
        return $this->belongsTo(Node::class);
    }

    public function invoker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invoker_id');
    }

    public function start(Node $node): void
    {
        $this->status = TaskStatus::Running;
        $this->node_id = $node->id;
        $this->started_at = now();
        $this->save();
    }

    public function retry(?Node $node): void
    {
        $nodeId = is_null($node) ? null : $node->id;

        $taskGroup = new NodeTaskGroup;
        $taskGroup->type = $this->type;
        $taskGroup->node_id = $nodeId;
        $taskGroup->forceFill(collect($this->attributes)->only([
            'swarm_id',
            'invoker_id',
            'team_id',
        ])->toArray());
        $taskGroup->save();

        $taskGroup->tasks()->saveMany($this->tasks->map(function (NodeTask $task) {
            $dataAttrs = $task->is_completed
                ? [
                    'status',
                    'started_at',
                    'ended_at',
                    'result',
                ]
                : [
                ];

            $attrs = collect($task->attributes);

            $attributes = $attrs
                ->only($dataAttrs)
                ->merge($attrs->only(['type', 'meta', 'payload']))
                ->toArray();

            return (new NodeTask($attributes))->forceFill($attributes);
        }));
    }
}
