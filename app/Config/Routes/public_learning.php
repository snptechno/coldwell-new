<?php

/**
 * Learning & Development (App\Controllers\Site\Learning).
 * CI3 accepted both the dash and the underscore spelling (translate_uri_dashes).
 *
 * @var CodeIgniter\Router\RouteCollection $routes
 */

$routes->get('learning-development', 'Site\Learning::learningDevelopment');
$routes->get('learning_development', 'Site\Learning::learningDevelopment');

// AJAX: the old JS (commented out in views/learning_development.php:221) POSTs group_id to
// baseUrl + 'load_group_images_gallery'. Only this spelling is CSRF-exempt in app/Config/Filters.php.
$routes->post('load_group_images_gallery', 'Site\Learning::loadGroupImagesGallery');
