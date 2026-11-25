<?php

/**
 * Public franchise (our offices) pages and AJAX endpoints (App\Controllers\Site\Franchise).
 * CI3 reached each Home method with dashes and underscores (translate_uri_dashes),
 * so both spellings are registered for the pages.
 *
 * @var CodeIgniter\Router\RouteCollection $routes
 */

// Our Network is a CMS page (slug our-franchise, served by the catch-all in Routes.php; seeded by
// NetworkPageSeeder). Site\Franchise::ourFranchise and views/site/our_franchisee.php stay for
// rollback; the office detail pages below are unchanged.
$routes->get('our_franchise', static fn () => redirect()->to(site_url('our-franchise'), 301));

// CI3: $route['our-franchise-details/(:any)/(:num)'] plus the catch-all; the name segment is ignored.
$routes->get('our-franchise-details/(:segment)/(:num)', 'Site\Franchise::ourFranchiseDetails/$1/$2');
$routes->get('our_franchise_details/(:segment)/(:num)', 'Site\Franchise::ourFranchiseDetails/$1/$2');

// Broken in CI3 (HTTP 500: missing views map / partner_with_us / franchise_details): now a 404.
$routes->get('map', 'Site\Franchise::map');
// Franchise is now a CMS page (slug franchise-details, served by the catch-all in Routes.php,
// seeded by FranchisePageSeeder). The partner-with-us pages never worked (CI3 500, then 404), so
// they and the underscore spelling 301 to the CMS page / its call-back form.
// Closures, not addRedirect(): Routes.php mirrors every GET route as HEAD by controller name,
// which turns addRedirect() targets into a bogus controller (and the dashed spelling then fell
// through to the CMS catch-all as a 404).
// Written out literally (not in a loop) so Admin\CmsPages::reservedSlugs() still sees these URLs.
$routes->get('franchise_details', static fn () => redirect()->to(site_url('franchise-details'), 301));
$routes->get('partner-with-us', static fn () => redirect()->to(site_url('franchise-details'), 301));
$routes->get('partner_with_us', static fn () => redirect()->to(site_url('franchise-details'), 301));

// AJAX from views/our_franchisee_details.php: $.ajax({url: baseUrl + 'get_owner_info' | 'get_specialist_info', type: 'POST'}).
// ("home/get_owner_info" is only used by the unused view our_franchisee_details-24-05-2017.php and is a 404 in CI3.)
$routes->post('get_owner_info', 'Site\Franchise::getOwnerInfo');
$routes->post('get_specialist_info', 'Site\Franchise::getSpecialistInfo');

// AJAX from views/our_franchisee.php (all type: "POST", underscore URLs).
$routes->post('get_franchisee_location_by_city', 'Site\Franchise::getFranchiseeLocationByCity');
$routes->post('search_franchisee_by_location', 'Site\Franchise::searchFranchiseeByLocation');
$routes->post('get_franchisee_name', 'Site\Franchise::getFranchiseeName');
// Called on page load to fill the map view. Exists in CI3 (Home.php:918) and answers 200 there.
// NOTE: needs 'get_all_franchisee_location' in the CSRF 'except' list of app/Config/Filters.php
// (the JS sends no token); until then the POST is rejected by the CSRF filter.
$routes->post('get_all_franchisee_location', 'Site\Franchise::getAllFranchiseeLocation');

// Franchise enquiry POSTs (step 7: Site\Enquiry).
$routes->post('franchisee-enquiry', 'Site\Enquiry::franchiseeEnquiry');
$routes->post('franchisee_enquiry', 'Site\Enquiry::franchiseeEnquiry');
$routes->post('franchisee-details-enquiry/(:num)', 'Site\Enquiry::franchiseeDetailsEnquiry/$1');
$routes->post('franchisee_details_enquiry/(:num)', 'Site\Enquiry::franchiseeDetailsEnquiry/$1');
