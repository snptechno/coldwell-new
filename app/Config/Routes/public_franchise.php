<?php

/**
 * Public franchise (our offices) pages and AJAX endpoints (App\Controllers\Site\Franchise).
 * CI3 reached each Home method with dashes and underscores (translate_uri_dashes),
 * so both spellings are registered for the pages.
 *
 * @var CodeIgniter\Router\RouteCollection $routes
 */

$routes->get('our-franchise', 'Site\Franchise::ourFranchise');
$routes->get('our_franchise', 'Site\Franchise::ourFranchise');

// CI3: $route['our-franchise-details/(:any)/(:num)'] plus the catch-all; the name segment is ignored.
$routes->get('our-franchise-details/(:segment)/(:num)', 'Site\Franchise::ourFranchiseDetails/$1/$2');
$routes->get('our_franchise_details/(:segment)/(:num)', 'Site\Franchise::ourFranchiseDetails/$1/$2');

// Broken in CI3 (HTTP 500: missing views map / partner_with_us / franchise_details): now a 404.
$routes->get('map', 'Site\Franchise::map');
$routes->get('partner-with-us', 'Site\Franchise::partnerWithUs');
$routes->get('partner_with_us', 'Site\Franchise::partnerWithUs');
$routes->get('franchise-details', 'Site\Franchise::franchiseDetails');
$routes->get('franchise_details', 'Site\Franchise::franchiseDetails');

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
