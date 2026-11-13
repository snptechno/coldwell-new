<?php

/**
 * Public home page (App\Controllers\Site\Home).
 *
 * CI3 served the home page at "/" (default_controller), at "/index" and at
 * "/index/<anything>" (catch-all -> Home/index/$1, the argument was ignored).
 * "/index.php" is the front controller itself and reaches "/".
 *
 * @var CodeIgniter\Router\RouteCollection $routes
 */

$routes->get('/', 'Site\Home::index');
$routes->get('index', 'Site\Home::index');
$routes->get('index/(:segment)', 'Site\Home::index/$1');
