<?php

use CodeIgniter\Router\RouteCollection;

/**
 * Coldwell Banker India - routes.
 *
 * Auto-routing is OFF (app/Config/Routing.php): every URL must be declared, with
 * the HTTP method it accepts. This replaces CI3's catch-all
 * `^(:any)(/:any)?$ => Home/$0` (application/config/routes.php), which let any
 * controller method be called by URL and let a plain GET insert rows.
 *
 * Routes are split per site area in app/Config/Routes/*.php so each area can be
 * maintained on its own. Every live URL keeps its exact path, including the
 * dash AND underscore spellings that CI3's translate_uri_dashes accepted.
 *
 * @var RouteCollection $routes
 */

foreach (glob(APPPATH . 'Config/Routes/*.php') ?: [] as $file) {
    require $file;
}

// HEAD must answer wherever GET does (CI3 did; search engines and uptime monitors
// use HEAD). CI4 only matches HEAD when it is registered, so mirror every public GET.
// Registered before the admin group so admin routes keep their own rules.
foreach ($routes->getRoutes('GET') as $from => $to) {
    $routes->head($from, $to);
}

// Admin - every route in this group requires a logged-in admin (App\Filters\AdminAuth).
// Login/logout stay outside the filter. Mutating actions will be POST-only.
$routes->group('admin', ['filter' => 'adminauth'], static function (RouteCollection $routes): void {
    // ported in step 6
});
