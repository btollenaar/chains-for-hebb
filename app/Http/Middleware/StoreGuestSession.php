<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class StoreGuestSession
{
    /**
     * Handle an incoming request.
     *
     * Store the guest session ID before login/registration so we can migrate their cart.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only store session ID if user is not already authenticated
        if (!Auth::check() && session()->has('_token')) {
            session()->put('guest_session_id', session()->getId());
        }

        return $next($request);
    }
}
