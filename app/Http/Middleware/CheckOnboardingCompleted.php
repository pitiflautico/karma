<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckOnboardingCompleted
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only check for authenticated users
        if ($request->user()) {
            // If user hasn't completed onboarding and not already on onboarding page
            if (!$request->user()->onboarding_completed && !$request->is('onboarding')) {
                return redirect()->route('onboarding');
            }
        }

        return $next($request);
    }
}
