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
        if (auth()->guest()) {
            return $next($request);
        }

        $team = auth()->user()->currentTeam;
        $subscription = $team->subscription();

        $doesntHaveSubscription = $subscription === null || ! $subscription->onTrial() && ! $subscription->active();
        if ($doesntHaveSubscription && ! $request->routeIs('teams.billing.show')) {
            return redirect()->route('teams.billing.show', $team);
        }

        return $next($request);
    }
}
