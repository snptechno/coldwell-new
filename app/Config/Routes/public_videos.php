<?php

/**
 * Public video gallery pages (App\Controllers\Site\Videos).
 * CI3 accepted both the dash and the underscore spelling (translate_uri_dashes).
 *
 * @var CodeIgniter\Router\RouteCollection $routes
 */

// Finding a Home is a CMS page (slug finding-a-home, served by the catch-all in Routes.php;
// seeded by FindingAHomePageSeeder). Site\Videos::findingAHome stays for rollback.
$routes->get('finding_a_home', static fn () => redirect()->to(site_url('finding-a-home'), 301));
// Selling a Home and Testimonials are CMS pages (SellingAHomePageSeeder, TestimonialsPageSeeder).
// Site\Videos::sellingAHome / ::testimonials stay for rollback.
$routes->get('selling_a_home', static fn () => redirect()->to(site_url('selling-a-home'), 301));
// CB India Videos & Featured Videos are CMS pages (GroupBPagesSeeder), served by the CMS catch-all.
$routes->get('cb_india_videos', static fn () => redirect()->to(site_url('cb-india-videos'), 301));
$routes->get('featured_videos', static fn () => redirect()->to(site_url('featured-videos'), 301));
