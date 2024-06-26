<?php

namespace App\Models;

use App\Traits\HasOwningTeam;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;
use InvalidArgumentException;

class Service extends Model
{
    use HasFactory;
    use HasOwningTeam;

    protected $fillable = [
        'name',
        'swarm_id',
    ];

    protected static function booted()
    {
        self::creating(function ($model) {
            $model->docker_name = 'swarm_' . $model->swarm_id . '_' . $model->name;
        });
    }

    public function swarm(): BelongsTo
    {
        return $this->belongsTo(Swarm::class);
    }

    public function deployments(): HasMany
    {
        return $this->hasMany(Deployment::class);
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
        $taskGroup = NodeTaskGroup::create([
            'swarm_id' => $this->swarm_id,
            'invoker_id' => auth()->id(),
            'type' => $this->deployments()->exists() ? NodeTaskGroupType::UpdateService : NodeTaskGroupType::CreateService,
        ]);

        $deployment = $this->deployments()->create([
            'task_group_id' => $taskGroup->id,
            'data' => $deploymentData,
        ]);

        $taskGroup->tasks()->createMany($deployment->asNodeTasks());

        return $deployment;
    }
}
