<?php

/**
 * Public home page (App\Controllers\Site\CmsPage::home: the CMS page with is_home = 1).
 *
 * CI3 served the home page at "/" (default_controller), at "/index" and at
 * "/index/<anything>" (catch-all -> Home/index/$1, the argument was ignored).
 * "/index.php" is the front controller itself and reaches "/".
 *
 * @var CodeIgniter\Router\RouteCollection $routes
 */

$routes->get('/', 'Site\CmsPage::home');
$routes->get('index', 'Site\CmsPage::home');
$routes->get('index/(:segment)', 'Site\CmsPage::home/$1');
