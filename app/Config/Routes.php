<?php

use CodeIgniter\Router\RouteCollection;

/**
 * Coldwell Banker India - routes.
 *
 * Auto-routing is OFF (app/Config/Routing.php): every URL must be declared here,
 * with the HTTP method it accepts. This replaces CI3's catch-all
 * `^(:any)(/:any)?$ => Home/$0` (application/config/routes.php:77), which let any
 * method be called by URL and let a plain GET insert rows (audit PUB-21).
 *
 * The full public URL list (audit report section 4.1) is ported in step 4/5,
 * keeping every existing URL - including dash and underscore spellings - unchanged.
 *
 * @var RouteCollection $routes
 */

// Public site (ported in later steps)
$routes->get('/', 'Home::index');

// Admin - every route in this group requires a logged-in admin (App\Filters\AdminAuth).
// Login/logout stay outside the filter. Mutating actions will be POST-only (audit ADM-03).
$routes->group('admin', ['filter' => 'adminauth'], static function (RouteCollection $routes): void {
    // ported in step 6
});
