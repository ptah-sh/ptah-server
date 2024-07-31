<?php

namespace App\Traits;

use App\Models\Scopes\TeamScope;
use App\Models\Team;

trait HasOwningTeam
{
    protected static function bootHasOwningTeam(): void
    {
        if (! app()->runningInConsole()) {
            static::addGlobalScope(new TeamScope);
        }
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }
}
