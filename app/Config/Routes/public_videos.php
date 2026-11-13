<?php

/**
 * Public video gallery pages (App\Controllers\Site\Videos).
 * CI3 accepted both the dash and the underscore spelling (translate_uri_dashes).
 *
 * @var CodeIgniter\Router\RouteCollection $routes
 */

$routes->get('finding-a-home', 'Site\Videos::findingAHome');
$routes->get('finding_a_home', 'Site\Videos::findingAHome');
$routes->get('selling-a-home', 'Site\Videos::sellingAHome');
$routes->get('selling_a_home', 'Site\Videos::sellingAHome');
$routes->get('testimonials', 'Site\Videos::testimonials');
$routes->get('cb-india-videos', 'Site\Videos::cbIndiaVideos');
$routes->get('cb_india_videos', 'Site\Videos::cbIndiaVideos');
$routes->get('featured-videos', 'Site\Videos::featuredVideos');
$routes->get('featured_videos', 'Site\Videos::featuredVideos');
