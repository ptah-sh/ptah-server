<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgentRelease extends Model
{
    use HasFactory;

    protected $fillable = [
        'tag_name',
        'download_url',
        'os',
        'arch',
    ];
}
