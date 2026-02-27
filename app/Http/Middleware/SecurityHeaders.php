<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

      $csp = [
    "default-src 'self'",
    "script-src 'self' http://localhost:5173 'unsafe-inline' 'unsafe-eval'",
    "style-src 'self' http://localhost:5173 'unsafe-inline'",
    "img-src 'self' data: blob:",
    "font-src 'self' data:",
    "connect-src 'self' http://localhost:5173 ws://localhost:5173",
    "object-src 'none'",
    "base-uri 'self'",
    "frame-ancestors 'none'"
];

    $response->headers->set(
        'Content-Security-Policy',
        implode('; ', $csp) . ';'
    );     

        return $response;
    }
}
