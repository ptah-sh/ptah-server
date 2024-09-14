<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTeamSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $team = auth()->user()->currentTeam;

        if (! $team->hasValidSubscription() && ! $team->onTrial()) {
            return redirect()->route('teams.billing.show', $team)->warningBanner('You must have a valid subscription to access this resource.');
        }

        return $next($request);
    }
}
