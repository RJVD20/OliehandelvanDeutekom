<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MaintenanceModeMiddleware
{
    /**
     * Show maintenance page for non-admins when enabled.
     * Admins can still access the site and admin panel.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Setting::getBool('maintenance_enabled', false)) {
            return $next($request);
        }

        if ($this->isBypassedRequest($request)) {
            return $next($request);
        }

        if (auth()->check() && (bool) (auth()->user()->is_admin ?? false)) {
            return $next($request);
        }

        return response()->view('maintenance', [], 503);
    }

    private function isBypassedRequest(Request $request): bool
    {
        // Always allow the admin panel (auth/admin middleware will protect it).
        if ($request->is('admin') || $request->is('admin/*')) {
            return true;
        }

        // Allow auth pages so admins can still log in.
        // Use both route names (when available) and path checks (for early middleware execution).
        if (
            $request->routeIs('login') ||
            $request->routeIs('logout') ||
            $request->is('login') ||
            $request->is('logout')
        ) {
            return true;
        }

        if (
            $request->routeIs('password.*') ||
            $request->routeIs('verification.*') ||
            $request->is('forgot-password') ||
            $request->is('reset-password/*') ||
            $request->is('verify-email') ||
            $request->is('verify-email/*')
        ) {
            return true;
        }

        // Allow assets and common public files.
        if (
            $request->is('build/*') ||
            $request->is('images/*') ||
            $request->is('storage/*') ||
            $request->is('favicon.ico') ||
            $request->is('robots.txt') ||
            $request->is('sitemap.xml') ||
            $request->is('@vite/*')
        ) {
            return true;
        }

        // Health endpoint
        if ($request->is('up')) {
            return true;
        }

        return false;
    }
}
