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
        'name',
    ];
}
