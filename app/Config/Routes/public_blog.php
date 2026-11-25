<?php

/**
 * Blog (App\Controllers\Site\Blog) - replaces the Drupal 7 blog that lived at /blogs.
 *
 * Friendly URLs: /blog, /blog/category/{slug}, /blog/{slug}. Every old Drupal URL
 * under /blogs/... answers 301 to its new address (Site\Blog::legacy).
 *
 * @var CodeIgniter\Router\RouteCollection $routes
 */

$routes->get('blog', 'Site\Blog::index');
$routes->get('blog/feed', 'Site\Blog::feed');
$routes->get('blog/rss', 'Site\Blog::feed');
$routes->get('blog/category/(:segment)', 'Site\Blog::category/$1');
$routes->get('blog/(:segment)', 'Site\Blog::show/$1');

$routes->get('sitemap.xml', 'Site\Blog::sitemap');

// Legacy Drupal URLs: /blogs, /blogs/content/{alias}, /blogs/node/N, /blogs/categories/{alias},
// /blogs/taxonomy/term/N, /blogs/articles, /blogs/rss.xml, /blogs/sites/default/files/...
$routes->get('blogs', 'Site\Blog::legacy');
$routes->get('blogs/(:any)', 'Site\Blog::legacy/$1');
