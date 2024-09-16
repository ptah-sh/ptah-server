<?php

namespace App\Models;

use App\Actions\Nodes\RebuildCaddy;
use App\Models\DeploymentData\Process;
use App\Models\NodeTasks\DeleteService\DeleteServiceMeta;
use App\Traits\HasOwningTeam;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

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

    // Add this line to make slug the routing key
    public function getRouteKeyName()
    {
        return 'slug';
    }

    protected static function booted()
    {
        static::creating(function (Service $service) {
            $service->slug = $service->generateUniqueSlug($service->team_id);
        });

        static::created(function (Service $service) {
            $service->slug = $service->generateUniqueSlug($service->id);
            $service->saveQuietly();
        });

        static::updating(function (Service $service) {
            if ($service->isDirty('name')) {
                $service->slug = $service->generateUniqueSlug($service->id);
            }
        });

        self::saved(function (Service $service) {
            if (! $service->docker_name) {
                $service->docker_name = $service->makeResourceName($service->name);
                $service->saveQuietly();
            }
        });

        self::deleting(function (Service $service) {
            $taskGroup = $service->swarm->taskGroups()->create([
                'type' => NodeTaskGroupType::DeleteService,
                'team_id' => auth()->user()->currentTeam->id,
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

            $taskGroup->tasks()->createMany($deleteProcessesTasks);

            RebuildCaddy::run($service->team, $taskGroup, $service->latestDeployment);
        });
    }

    protected function generateUniqueSlug($id)
    {
        $slug = Str::slug($this->name, '_');
        $vocabulary = config('ptah.services.slug.vocabulary');
        $adjectives = config('ptah.services.slug.adjectives');

        $entity = $vocabulary[array_rand($vocabulary)];
        $adjective = $adjectives[array_rand($adjectives)];

        $hexId = dechex($id);

        return $slug.'_'.$adjective.'_'.$entity.'_'.$hexId;
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
        return $this->hasOne(Deployment::class)->latest('id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(NodeTask::class, 'meta__service_id', 'id');
    }

    public function makeResourceName($name): string
    {
        return dockerize_name('svc_'.$this->id.'_'.$name);
    }
}
