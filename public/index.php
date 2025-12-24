<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    $uriPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

    $bypass = (
        $uriPath === '/login' ||
        $uriPath === '/logout' ||
        $uriPath === '/forgot-password' ||
        str_starts_with($uriPath, '/reset-password') ||
        str_starts_with($uriPath, '/verify-email') ||
        $uriPath === '/up' ||
        $uriPath === '/favicon.ico' ||
        $uriPath === '/robots.txt' ||
        $uriPath === '/sitemap.xml' ||
        str_starts_with($uriPath, '/admin') ||
        str_starts_with($uriPath, '/build/') ||
        str_starts_with($uriPath, '/images/') ||
        str_starts_with($uriPath, '/storage/')
    );

    if (!$bypass) {
        http_response_code(503);
        header('Content-Type: text/html; charset=UTF-8');
        header('Retry-After: 3600');

        echo '<!doctype html>';
        echo '<html lang="nl">';
        echo '<head>';
        echo '<meta charset="utf-8">';
        echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
        echo '<title>Onderhoud</title>';
        echo '</head>';
        echo '<body style="font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; background:#f3f4f6; margin:0; padding:24px;">';
        echo '<div style="max-width:720px; margin:0 auto; background:#fff; border-radius:8px; padding:24px;">';
        echo '<h1 style="margin:0 0 8px; font-size:22px;">We zijn even bezig met onderhoud</h1>';
        echo '<p style="margin:0 0 16px; color:#4b5563;">De website is tijdelijk niet beschikbaar. Probeer het later opnieuw.</p>';
        echo '<div style="display:flex; gap:12px; flex-wrap:wrap;">';
        echo '<a href="/login" style="display:inline-block; padding:10px 14px; background:#111827; color:#fff; text-decoration:none; border-radius:6px;">Admin inloggen</a>';
        echo '<a href="/" style="display:inline-block; padding:10px 14px; background:#e5e7eb; color:#111827; text-decoration:none; border-radius:6px;">Opnieuw proberen</a>';
        echo '</div>';
        echo '</div>';
        echo '</body>';
        echo '</html>';
        exit;
    }
}


// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());

