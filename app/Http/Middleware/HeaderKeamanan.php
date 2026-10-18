<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Symfony\Component\HttpFoundation\Response;

/**
 * Header keamanan dasar untuk semua halaman. Halaman publik juga diberi Content-Security-Policy
 * yang hanya mengizinkan skrip dari website sendiri, serta sematan peta Google dan video YouTube.
 * Panel admin (Filament) memakai skrip sebaris, jadi tidak diberi CSP ketat.
 */
class HeaderKeamanan
{
    protected const CSP_PUBLIK = [
        "default-src 'self'",
        "script-src 'self'",
        "style-src 'self' 'unsafe-inline'",
        "img-src 'self' data: https://i.ytimg.com",
        "font-src 'self'",
        'frame-src https://www.google.com https://maps.google.com https://www.youtube-nocookie.com',
        "connect-src 'self'",
        "form-action 'self'",
        "base-uri 'self'",
        "frame-ancestors 'self'",
        "object-src 'none'",
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');

        if ($request->isSecure() && app()->isProduction()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000');
        }

        if (! $request->is('admin', 'admin/*', 'livewire*', 'filament/*') && ! Vite::isRunningHot()) {
            $response->headers->set('Content-Security-Policy', implode('; ', self::CSP_PUBLIK));
        }

        return $response;
    }
}
