<?php

namespace App\Models\Scopes;

use App\Models\Team;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Request;

class TeamScope implements Scope
{
    private static $enabled = true;

    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (! self::$enabled) {
            return;
        }

        $user = auth()->user();
        if ($user) {
            // API Request
            if (Request::isJson() && Request::bearerToken()) {
                $builder->whereIn($builder->qualifyColumn('team_id'), $user->ownedTeams()->pluck('id')->all());

                return;
            }

            // Web Request
            $builder->where($builder->qualifyColumn('team_id'), $user->currentTeam->id);

            return;
        }

        // X-Ptah-Token
        $builder->where($builder->qualifyColumn('team_id'), app(Team::class)->id);
    }

    public static function disable(): void
    {
        self::$enabled = false;
    }

    public static function enable(): void
    {
        self::$enabled = true;
    }
}
