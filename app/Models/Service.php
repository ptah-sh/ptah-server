<?php

namespace App\Models;

use App\Models\DeploymentData\Process;
use App\Models\NodeTasks\DeleteService\DeleteServiceMeta;
use App\Traits\HasOwningTeam;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Gate;

class Service extends Model
{
    use HasFactory;
    use HasOwningTeam;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'swarm_id',
        'team_id',
    ];

    protected static function booted()
    {
        self::saved(function (Service $service) {
            if (! $service->docker_name) {
                $service->docker_name = $service->makeResourceName($service->name);
                $service->saveQuietly();
            }
        });

        self::deleting(function (Service $service) {
            $taskGroup = $service->swarm->taskGroups()->create([
                'type' => NodeTaskGroupType::DeleteService,
                'team_id' => auth()->user()->current_team_id,
                'invoker_id' => auth()->id(),
            ]);

            $deleteProcessesTasks = collect($service->latestDeployment->data->processes)->map(function (Process $process) use ($service) {
                return [
                    'type' => NodeTaskType::DeleteService,
                    'meta' => new DeleteServiceMeta($service->id, $process->name, $service->name),
                    'payload' => [
                        'ServiceName' => $process->dockerName,
                    ],
                ];
            })->toArray();

            // TODO: apply caddy config after the services deletion
            //   https://github.com/ptah-sh/ptah-server/issues/117
            $taskGroup->tasks()->createMany($deleteProcessesTasks);
        });
    }

    public function swarm(): BelongsTo
    {
        return $this->belongsTo(Swarm::class);
    }

    public function deployments(): HasMany
    {
        return $this->hasMany(Deployment::class)->orderByDesc('id');
    }

    public function latestDeployment(): HasOne
    {
        return $this->hasOne(Deployment::class)->latest();
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(NodeTask::class, 'meta__service_id', 'id');
    }

    public function makeResourceName($name): string
    {
        return dockerize_name('svc_'.$this->id.'_'.$name);
    }

    public function deploy(DeploymentData $deploymentData): Deployment
    {
        Gate::authorize('deploy', $this);

        $this->placement_node_id = $deploymentData->placementNodeId;

        $taskGroup = NodeTaskGroup::create([
            'swarm_id' => $this->swarm_id,
            'team_id' => $this->team_id,
            'invoker_id' => auth()->id(),
            'type' => $this->deployments()->exists() ? NodeTaskGroupType::UpdateService : NodeTaskGroupType::CreateService,
        ]);

        $deployment = $this->deployments()->create([
            'team_id' => $this->team_id,
            'data' => $deploymentData,
        ]);

        $taskGroup->tasks()->createMany($deployment->asNodeTasks());

        return $deployment;
    }
}
