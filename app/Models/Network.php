<?php

namespace App\Models;

use App\Traits\HasOwningTeam;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Network extends Model
{
    use HasFactory;
    use HasOwningTeam;

    protected $fillable = [
        'swarm_id',
        'name',
        'team_id',
    ];

    protected static function booted(): void
    {
        self::creating(function (Network $network) {
            $network->docker_name = dockerize_name($network->name);
        });
    }
}
