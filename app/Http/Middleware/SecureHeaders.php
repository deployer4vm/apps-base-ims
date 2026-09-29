<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecureHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($request->isSecure() && app()->environment('production')) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set(
            'Permissions-Policy',
            'camera=(), geolocation=(), microphone=(), payment=(), usb=()'
        );
        $response->headers->set('X-XSS-Protection', '0');
        $response->headers->set('X-Permitted-Cross-Domain-Policies', 'none');

        if (!$response->headers->has('Content-Security-Policy')) {
            $response->headers->set(
                'Content-Security-Policy',
                "default-src 'self'; base-uri 'self'; object-src 'none'; frame-ancestors 'self'; "
                ."form-action 'self'; img-src 'self' data: https:; font-src 'self' data: https:; "
                ."style-src 'self' 'unsafe-inline' https:; "
                ."script-src 'self' 'unsafe-inline' 'unsafe-eval' https:; "
                ."connect-src 'self' https: wss:"
            );
        }

        return $response;
    }
}
