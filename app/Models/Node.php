<?php

namespace App\Models;

use App\HasOwningTeam;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Node extends Model
{
    use HasFactory;
    use HasOwningTeam;

    protected $casts = [
        'data' => NodeData::class
    ];

    protected $fillable = [
        'name',
    ];

    protected $appends = [
        'online',
    ];

    protected static function booted()
    {
        self::creating(function (Node $node) {
            $node->agent_token = Str::random(42);
        });
    }

    public function getOnlineAttribute()
    {
        return true;
//        return $this->last_seen_at > now()->subSeconds(35);
    }
}
