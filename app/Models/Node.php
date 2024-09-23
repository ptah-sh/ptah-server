<?php

namespace App\Models;

use App\Traits\HasOwningTeam;
use App\Util\AgentToken;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use RuntimeException;

class Node extends Model
{
    use HasFactory;
    use HasOwningTeam;

    protected $casts = [
        'data' => NodeData::class,
    ];

    protected $fillable = [
        'name',
        'team_id',
    ];

    protected $appends = [
        'online',
    ];

    protected static function booted(): void
    {
        self::creating(function (Node $node) {
            if ($node->team->quotas()->nodes->quotaReached()) {
                throw new RuntimeException('Invalid State - The team is at its node limit');
            }

            $node->agent_token = AgentToken::make();
        });
    }

    public function swarm(): BelongsTo
    {
        return $this->belongsTo(Swarm::class);
    }

    public function boundServices(): HasMany
    {
        return $this->hasMany(Service::class, 'placement_node_id', 'id');
    }

    public function taskGroups(): HasMany
    {
        return $this->hasMany(NodeTaskGroup::class);
    }

    public function tasks(): HasManyThrough
    {
        return $this->hasManyThrough(NodeTask::class, NodeTaskGroup::class, 'node_id', 'task_group_id', 'id', 'id');
    }

    public function scopeOnline(Builder $query): Builder
    {
        return $query->where('last_seen_at', '>', now()->subSeconds(35));
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

    public function upgradeAgent($targetVersion): void
    {
        $release = AgentRelease::where('tag_name', $targetVersion)->sole();

        $taskGroup = NodeTaskGroup::create([
            'swarm_id' => $this->swarm_id,
            'node_id' => $this->id,
            'type' => NodeTaskGroupType::SelfUpgrade,
            'invoker_id' => auth()->user()->id,
            'team_id' => auth()->user()->current_team_id,
        ]);

        $taskGroup->tasks()->createMany([
            [
                'type' => NodeTaskType::DownloadAgentUpgrade,
                'meta' => [
                    'targetVersion' => $targetVersion,
                    'downloadUrl' => $release->download_url,
                ],
                'payload' => [
                    'TargetVersion' => $targetVersion,
                    'DownloadUrl' => $release->download_url,
                ],
            ],
            [
                'type' => NodeTaskType::UpdateAgentSymlink,
                'meta' => [
                    'targetVersion' => $targetVersion,
                ],
                'payload' => [
                    'TargetVersion' => $targetVersion,
                ],
            ],
            [
                'type' => NodeTaskType::ConfirmAgentUpgrade,
                'meta' => [
                    'targetVersion' => $targetVersion,
                ],
                'payload' => [
                    'TargetVersion' => $targetVersion,
                ],
            ],
        ]);
    }
}
