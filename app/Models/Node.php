<?php

namespace App\Models;

use App\Traits\HasOwningTeam;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Str;

class Node extends Model
{
    use HasFactory;
    use HasOwningTeam;

    protected $casts = [
        'data' => NodeData::class
    ];

    protected $fillable = [
        'name',
    ];

    protected $appends = [
        'online',
    ];

    protected static function booted()
    {
        self::creating(function (Node $node) {
            $node->agent_token = Str::random(42);
        });
    }

    public function taskGroups(): HasMany
    {
        return $this->hasMany(NodeTaskGroup::class);
    }

    public function tasks(): HasManyThrough
    {
        return $this->hasManyThrough(NodeTask::class, NodeTaskGroup::class, 'node_id', 'task_group_id', 'id', 'id');
    }

    public function getOnlineAttribute()
    {
        return $this->last_seen_at > now()->subSeconds(35);
    }

    public function actualTaskGroup($type): ?NodeTaskGroup
    {
        $base = $this->taskGroups()->ofType($type)->with(['tasks', 'invoker']);

        $inProgress = $base->clone()->inProgress()->first();
        if ($inProgress) {
            return $inProgress;
        }

        return $base->orderByDesc('id')->take(1)->get()[0] ?? null;
    }
}
