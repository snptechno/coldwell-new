<?php

/**
 * Public career / corporate team pages (App\Controllers\Site\Careers).
 * CI3 reached each Home method with dashes and underscores (translate_uri_dashes),
 * so both spellings are registered.
 *
 * @var CodeIgniter\Router\RouteCollection $routes
 */

$routes->get('career', 'Site\Careers::career');

$routes->get('franchisees-openings', 'Site\Careers::franchiseesOpenings');
$routes->get('franchisees_openings', 'Site\Careers::franchiseesOpenings');

$routes->get('corporate-team', 'Site\Careers::corporateTeam');
$routes->get('corporate_team', 'Site\Careers::corporateTeam');

// Broken in CI3 (HTTP 500 for every request): now a 404, see the controller.
$routes->get('apply-job-opening', 'Site\Careers::applyJobOpening');
$routes->get('apply_job_opening', 'Site\Careers::applyJobOpening');
$routes->get('apply-job-opening/(:segment)', 'Site\Careers::applyJobOpening/$1');
$routes->get('apply_job_opening/(:segment)', 'Site\Careers::applyJobOpening/$1');
$routes->get('campus-recruitment', 'Site\Careers::campusRecruitment');
$routes->get('campus_recruitment', 'Site\Careers::campusRecruitment');

// AJAX from views/corporate_team.php: $.ajax({url: baseUrl+'get_team_information', type: 'POST'}).
// Only the underscore spelling is used by the JS (and is the one exempted from CSRF in Filters).
$routes->post('get_team_information', 'Site\Careers::getTeamInformation');
