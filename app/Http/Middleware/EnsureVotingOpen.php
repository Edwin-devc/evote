<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureVotingOpen
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $votingOpen = (bool) cache()->get('voting_open', config('voting.open'));

        if (!$votingOpen) {
            return response()->view('voting-closed', [], 403);
        }

        return $next($request);
    }
}
