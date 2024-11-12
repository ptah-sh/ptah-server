<?php

namespace App\Models;

use App\Traits\HasOwningTeam;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReviewApp extends Model
{
    use HasFactory;
    use HasOwningTeam;
    use SoftDeletes;

    protected $fillable = [
        'process',
        'worker',
        'ref',
        'ref_url',
        'visit_url',
        'team_id',
    ];

    public function deployments(): HasMany
    {
        return $this->hasMany(Deployment::class);
    }

    public function latestDeployment()
    {
        return $this->deployments()->one()->latestOfMany();
    }
}
