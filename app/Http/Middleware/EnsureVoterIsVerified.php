<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureVoterIsVerified
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (!session('voter_verified') || !session('logged_in')) {
            return redirect('/')->withErrors([
                'error' => 'Please login and verify your identity to access the ballot.',
            ]);
        }

        return $next($request);
    }
}
