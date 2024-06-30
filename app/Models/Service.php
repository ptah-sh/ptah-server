<?php

namespace App\Models;

use App\Models\NodeTasks\ApplyCaddyConfig\ApplyCaddyConfigMeta;
use App\Models\NodeTasks\DeleteService\DeleteServiceMeta;
use App\Traits\HasOwningTeam;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use InvalidArgumentException;

class Service extends Model
{
    use SoftDeletes;
    use HasFactory;
    use HasOwningTeam;

    protected $fillable = [
        'name',
        'swarm_id',
    ];

    protected static function booted()
    {
        self::saved(function (Service $service) {
            $service->docker_name = $service->makeResourceName($service->name);
            $service->saveQuietly();
        });

        self::deleting(function (Service $service) {
            $taskGroup = $service->swarm->taskGroups()->create([
                'type' => NodeTaskGroupType::DeleteService,
                'invoker_id' => auth()->id(),
            ]);

            $deleteProcessesTasks = collect($service->latestDeployment->data->processes)->map(function ($process) use ($service) {
                return [
                    'type' => NodeTaskType::DeleteService,
                    'meta' => new DeleteServiceMeta($service->id, $process->name, $service->name),
                    'payload' => [
                        'ServiceName' => $service->docker_name,
                    ],
                ];
            })->toArray();

            // TODO: apply caddy config after the services deletion
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

    public function makeResourceName($name): string
    {
        return dockerize_name("svc_" . $this->id . '_'. $name);
    }

    public function deploy(DeploymentData $deploymentData): Deployment
    {
        Gate::authorize('deploy', $this);

        $taskGroup = NodeTaskGroup::create([
            'swarm_id' => $this->swarm_id,
            'invoker_id' => auth()->id(),
            'type' => $this->deployments()->exists() ? NodeTaskGroupType::UpdateService : NodeTaskGroupType::CreateService,
        ]);

        $deployment = $this->deployments()->create([
            'data' => $deploymentData,
        ]);

        $taskGroup->tasks()->createMany($deployment->asNodeTasks());

        return $deployment;
    }
}
