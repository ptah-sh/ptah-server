<?php

namespace App\Models;

use App\Models\NodeTask\AbstractTaskResult;
use App\Models\NodeTask\TaskStatus;
use App\Traits\HasOwningTeam;
use App\Traits\HasTaskStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NodeTaskGroup extends Model
{
    use HasFactory;
    use HasOwningTeam;
    use HasTaskStatus;

    protected $fillable = [
        'swarm_id',
        'node_id',
        'invoker_id',
    ];

    public function swarm(): BelongsTo
    {
        return $this->belongsTo(Swarm::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(NodeTask::class, 'task_group_id');
    }

    public function allTasksEnded() :bool
    {
        return ! $this->tasks()->whereNull('ended_at')->exists();
    }

    public function node(): BelongsTo
    {
        return $this->belongsTo(Node::class);
    }

    public function startTask(Node $node, NodeTask $task): void
    {
        $this->status = TaskStatus::Running;
        $this->node_id = $node->id;
        $this->started_at = now();
        $this->save();

        $task->start();
    }

    public function completeTask(NodeTask $task, AbstractTaskResult $result): void
    {


        $task->complete($result);
    }

    public function failTask(NodeTask $task, AbstractTaskResult $result): void
    {
        $task->fail($result);
    }
}
