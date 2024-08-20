<?php

namespace App\Models;

use App\Traits\HasOwningTeam;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Swarm extends Model
{
    use HasFactory;
    use HasOwningTeam;

    protected $fillable = [
        'data',
        'team_id',
    ];

    protected $casts = [
        'data' => SwarmData::class,
    ];

    public function networks(): HasMany
    {
        return $this->hasMany(Network::class);
    }

    public function nodes(): HasMany
    {
        return $this->hasMany(Node::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function taskGroups(): HasMany
    {
        return $this->hasMany(NodeTaskGroup::class, 'swarm_id');
    }
}
