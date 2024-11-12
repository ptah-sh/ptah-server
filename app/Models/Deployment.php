<?php

namespace App\Models;

use App\Models\DeploymentData\Process;
use App\Models\NodeTasks\DeleteService\DeleteServiceMeta;
use App\Traits\HasOwningTeam;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Query\Builder as QueryBuilder;

class Deployment extends Model
{
    use HasFactory;
    use HasOwningTeam;

    protected $fillable = [
        'service_id',
        'team_id',
        'task_group_id',
        'configured_by_id',
        'review_app_id',
        'data',
    ];

    protected $casts = [
        'data' => DeploymentData::class,
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function taskGroups(): BelongsToMany
    {
        return $this->belongsToMany(NodeTaskGroup::class)->orderByDesc('id');
    }

    public function latestTaskGroup(): HasOneThrough
    {
        return $this->hasOneThrough(NodeTaskGroup::class, DeploymentNodeTaskGroup::class, 'deployment_id', 'id', 'id', 'node_task_group_id')->orderByDesc('id');
    }

    public function reviewApp(): BelongsTo
    {
        return $this->belongsTo(ReviewApp::class)->withTrashed();
    }

    public function previousDeployment(): ?Deployment
    {
        return $this->service->deployments()->where('id', '<', $this->id)->whereNull('review_app_id')->latest('id')->get()->first();
    }

    public function makeResourceName($name): string
    {
        return $this->service->makeResourceName('dpl_'.$this->id.'_'.$name);
    }

    public function scopeLatestDeployments(EloquentBuilder $query, Team $team): EloquentBuilder
    {
        return $query->whereIn('id', function (QueryBuilder $query) use ($team) {
            $query
                ->selectRaw('max("latest_deployments_query"."id")')
                ->from('deployments', 'latest_deployments_query')
                ->join('services', 'services.id', '=', 'latest_deployments_query.service_id')
                ->leftJoin('review_apps', 'review_apps.id', '=', 'latest_deployments_query.review_app_id')
                ->whereNull('services.deleted_at')
                ->whereNull('review_apps.deleted_at')
                ->where('deployments.team_id', $team->id)
                ->groupBy('latest_deployments_query.service_id', 'latest_deployments_query.review_app_id');
        });
    }

    public function resourceLabels(): array
    {
        return dockerize_labels([
            'service.id' => $this->service_id,
            'service.slug' => $this->service->slug,
            'deployment.id' => $this->id,
        ]);
    }

    public function asNodeTasks(): array
    {
        /* @var DeploymentData $data */
        $data = $this->data;

        $tasks = [];

        foreach ($data->processes as $process) {
            $tasks = [
                ...$tasks,
                ...$process->asNodeTasks($this),
            ];
        }

        if (! $this->review_app_id) {
            $previousProcesses = $this->previousDeployment()?->data->processes ?? [];

            foreach ($previousProcesses as $process) {
                if ($this->findProcess($process->dockerName) === null) {
                    $tasks[] = [
                        'type' => NodeTaskType::DeleteService,
                        'meta' => new DeleteServiceMeta($this->service_id, $process->dockerName, $this->service->name),
                        'payload' => [
                            'ServiceName' => $process->dockerName,
                        ],
                    ];
                }
            }
        }

        // Question: Why is this needed? :)
        // Answer: process asNodeTasks() method makes modifications to the process object. "asNodeTasks" is not the best name.
        $this->saveQuietly();

        return $tasks;
    }

    public function findProcess(string $dockerName): ?Process
    {
        return $this->data->findProcess($dockerName);
    }
}
