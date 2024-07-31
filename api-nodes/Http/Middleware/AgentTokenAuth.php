<?php

namespace ApiNodes\Http\Middleware;

use App\Models\Node;
use App\Models\Scopes\TeamScope;
use App\Models\Team;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AgentTokenAuth
{
    const AUTH_HEADER = 'x-ptah-token';

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header(self::AUTH_HEADER);

        if (! $token) {
            if ($request->bearerToken()) {
                return $next($request);
            }

            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        $node = Node::withoutGlobalScope(TeamScope::class)->with('team')->whereAgentToken($token)->firstOrFail();

        $node->last_seen_at = now();

        $node->save();

        app()->singleton(Node::class, fn () => $node);
        app()->singleton(Team::class, fn () => $node->team);

        return $next($request);
    }
}
