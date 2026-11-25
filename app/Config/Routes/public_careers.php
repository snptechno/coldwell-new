<?php

/**
 * Public career / corporate team pages (App\Controllers\Site\Careers).
 * CI3 reached each Home method with dashes and underscores (translate_uri_dashes),
 * so both spellings are registered.
 *
 * @var CodeIgniter\Router\RouteCollection $routes
 */

// 'career' is a CMS page (CareersPageSeeder), served by the CMS catch-all route.

// 'franchisees-openings' is a CMS page (GroupBPagesSeeder), served by the CMS catch-all route.
$routes->get('franchisees_openings', static fn () => redirect()->to(site_url('franchisees-openings'), 301));

// Corporate Team is a CMS page (slug corporate-team, CorporateTeamPageSeeder), served by the
// catch-all. Site\Careers::corporateTeam stays for rollback.
$routes->get('corporate_team', static fn () => redirect()->to(site_url('corporate-team'), 301));

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

// Job application POST (step 7: Site\Enquiry::jobApplications, resume upload).
// POST-only: in CI3 this method was reachable via GET and inserted an empty row.
$routes->post('job-applications', 'Site\Enquiry::jobApplications');
$routes->post('job_applications', 'Site\Enquiry::jobApplications');
