<?php

namespace App\Models\Scopes;

use App\Models\Team;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TeamScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $teamId = auth()->user()?->currentTeam->id ?: app(Team::class)->id;

        $builder->where($builder->qualifyColumn('team_id'), $teamId);
    }
}
