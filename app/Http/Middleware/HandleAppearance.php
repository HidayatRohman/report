<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class HandleAppearance
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            View::share('appearance', $request->cookie('appearance') ?? 'system');
        } catch (\Exception $e) {
            // Fallback if View facade fails (during bootstrap issues)
            // Continue without appearance sharing
            \Log::warning('HandleAppearance middleware failed: ' . $e->getMessage());
        }

        return $next($request);
    }
}
