<?php

namespace App\Models;

use App\Traits\HasOwningTeam;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

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

    public function deployments(): HasMany
    {
        return $this->hasMany(Deployment::class);
    }

    public function makeResourceName($name): string
    {
        $name = "svc_" . $this->id . '_'. $name;

        $name = Str::snake($name);
        $name = Str::replaceMatches('/\W/', '_', $name);
        $name = Str::replaceMatches('/_+/', '_', $name);

        if (Str::length($name) > 63) {
            $name = Str::substr($name, 0, 57) . '_' . Str::random(5);
        }

        return $name;
    }
}
