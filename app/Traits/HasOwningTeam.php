<?php

namespace App\Traits;

use App\Models\Scopes\TeamScope;
use App\Models\Team;

trait HasOwningTeam
{
    protected static function bootHasOwningTeam(): void
    {
        static::addGlobalScope(new TeamScope());

        static::creating(function ($model) {
            $model->team_id = auth()->user()->currentTeam->id;
        });
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }
}
