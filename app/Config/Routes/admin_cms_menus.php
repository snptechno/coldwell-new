<?php

/**
 * Admin > CMS > Menus and Site settings (App\Controllers\Admin\CmsMenus, CmsSettings). See docs/CMS.md.
 *
 * Every route needs an admin login (adminauth). GET routes only read; every write is a POST
 * carrying the CSRF token. {location} must be a key of App\Libraries\Cms\SiteSchema::MENUS.
 *
 * @var CodeIgniter\Router\RouteCollection $routes
 */

use CodeIgniter\Router\RouteCollection;

$routes->group('admin', ['filter' => 'adminauth'], static function (RouteCollection $routes): void {
    $routes->get('cms/menus', 'Admin\CmsMenus::index');
    $routes->get('cms/menus/(:segment)', 'Admin\CmsMenus::show/$1');
    $routes->match(['GET', 'POST'], 'cms/menus/(:segment)/add', 'Admin\CmsMenus::form/$1');
    $routes->match(['GET', 'POST'], 'cms/menus/(:segment)/edit/(:num)', 'Admin\CmsMenus::form/$1/$2');
    $routes->post('cms/menus/(:segment)/sort', 'Admin\CmsMenus::sort/$1');
    $routes->post('cms/menus/(:segment)/status/(:num)/(:num)', 'Admin\CmsMenus::status/$1/$2/$3');
    $routes->post('cms/menus/(:segment)/delete/(:num)', 'Admin\CmsMenus::delete/$1/$2');

    $routes->match(['GET', 'POST'], 'cms/settings', 'Admin\CmsSettings::index');
});
