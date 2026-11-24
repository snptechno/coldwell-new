<?php

use CodeIgniter\Router\RouteCollection;

/**
 * Admin > CMS > Pages / Articles, their blocks and block items (App\Controllers\Admin\CmsPages).
 * See docs/CMS.md. Every route needs an admin login; every write is a POST with the CSRF token
 * (the GET+POST form routes only write on POST).
 *
 * @var RouteCollection $routes
 */
$routes->group('admin', ['filter' => 'adminauth'], static function (RouteCollection $routes): void {
    $routes->get('cms/pages', 'Admin\CmsPages::index');
    $routes->match(['GET', 'POST'], 'cms/pages/add', 'Admin\CmsPages::form');
    $routes->match(['GET', 'POST'], 'cms/pages/edit/(:num)', 'Admin\CmsPages::form/$1');
    $routes->post('cms/pages/status/(:num)/(:num)', 'Admin\CmsPages::status/$1/$2');
    $routes->post('cms/pages/delete/(:num)', 'Admin\CmsPages::delete/$1');

    // Blocks of a page
    $routes->post('cms/pages/(:num)/blocks/add', 'Admin\CmsPages::blockAdd/$1');
    $routes->post('cms/pages/(:num)/blocks/sort', 'Admin\CmsPages::blockSort/$1');
    $routes->match(['GET', 'POST'], 'cms/pages/(:num)/blocks/(:num)', 'Admin\CmsPages::blockForm/$1/$2');
    $routes->post('cms/pages/(:num)/blocks/(:num)/status/(:num)', 'Admin\CmsPages::blockStatus/$1/$2/$3');
    $routes->post('cms/pages/(:num)/blocks/(:num)/delete', 'Admin\CmsPages::blockDelete/$1/$2');

    // Items of a block group
    $routes->match(['GET', 'POST'], 'cms/pages/(:num)/blocks/(:num)/items/(:segment)/add', 'Admin\CmsPages::itemForm/$1/$2/$3');
    $routes->match(['GET', 'POST'], 'cms/pages/(:num)/blocks/(:num)/items/(:segment)/edit/(:num)', 'Admin\CmsPages::itemForm/$1/$2/$3/$4');
    $routes->post('cms/pages/(:num)/blocks/(:num)/items/(:segment)/sort', 'Admin\CmsPages::itemSort/$1/$2/$3');
    $routes->post('cms/pages/(:num)/blocks/(:num)/items/(:segment)/status/(:num)/(:num)', 'Admin\CmsPages::itemStatus/$1/$2/$3/$4/$5');
    $routes->post('cms/pages/(:num)/blocks/(:num)/items/(:segment)/delete/(:num)', 'Admin\CmsPages::itemDelete/$1/$2/$3/$4');
});
