<?php

namespace App\Http\Controllers;

use App\Models\User;

abstract class Controller
{
    // TODO: replace with Gate::authorize()
    protected function authorizeOr403(string $ability, ...$arguments)
    {
        $user = auth()->user();

        if ($user->cannot($ability, ...$arguments)) {
            abort(403);
        }
    }
}
