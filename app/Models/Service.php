<?php

namespace App\Models;

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
        return $this->deployments()->one()->ofMany(['id' => 'MAX'], function ($query) {
            $query->whereNull('review_app_id');
        });
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(NodeTask::class, 'meta__service_id', 'id');
    }

    public function backups(): HasMany
    {
        return $this->hasMany(Backup::class);
    }

    public function reviewApps(): HasMany
    {
        return $this->hasMany(ReviewApp::class);
    }

    public function makeResourceName($name): string
    {
        return dockerize_name('svc_'.$this->id.'_'.$name);
    }
}
