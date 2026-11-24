<?php

/**
 * Learning & Development (App\Controllers\Site\Learning).
 * CI3 accepted both the dash and the underscore spelling (translate_uri_dashes).
 *
 * @var CodeIgniter\Router\RouteCollection $routes
 */

// Learning & Development is a CMS page (slug learning-development, served by the catch-all in
// Routes.php; seeded by LearningPageSeeder). Site\Learning::learningDevelopment and
// views/site/learning_development.php stay for rollback.
$routes->get('learning_development', static fn () => redirect()->to(site_url('learning-development'), 301));

// AJAX: the old JS (commented out in views/learning_development.php:221) POSTs group_id to
// baseUrl + 'load_group_images_gallery'. Only this spelling is CSRF-exempt in app/Config/Filters.php.
$routes->post('load_group_images_gallery', 'Site\Learning::loadGroupImagesGallery');
