<?php

/**
 * Public CMS/static pages (App\Controllers\Site\Pages).
 *
 * CI3 reached these through its catch-all route with translate_uri_dashes = TRUE,
 * so every multi-word page answers to both the dash and the underscore spelling.
 *
 * @var CodeIgniter\Router\RouteCollection $routes
 */

// FAQ, the legal pages and the site map are CMS pages (FaqPageSeeder, LegalPagesSeeder,
// SiteMapPageSeeder), served by the catch-all in Routes.php. Old spellings redirect; the
// Site\Pages methods and views stay for rollback.
$routes->get('privacy_policy', static fn () => redirect()->to(site_url('privacy-policy'), 301));
$routes->get('privacy', static fn () => redirect()->to(site_url('privacy-policy'), 301));
$routes->get('terms_and_conditions', static fn () => redirect()->to(site_url('terms-and-conditions'), 301));
$routes->get('terms', static fn () => redirect()->to(site_url('terms-and-conditions'), 301));
$routes->get('site_map', static fn () => redirect()->to(site_url('site-map'), 301));

// CB Training Program is a CMS page (slug cb-training-program, TrainingPageSeeder). The CMS
// catch-all only matches lowercase slugs, so the CI3 spelling redirects.
$routes->get('CB-training-program', static fn () => redirect()->to(site_url('cb-training-program'), 301));
$routes->get('CB_training_program', static fn () => redirect()->to(site_url('cb-training-program'), 301));

$routes->get('quick-pay', 'Site\Pages::quickPay');
$routes->get('quick_pay', 'Site\Pages::quickPay');

// About Us is a CMS page (slug about-coldwell-banker, served by the catch-all in Routes.php;
// seeded by AboutPageSeeder). The old underscore spelling redirects. Site\Pages::aboutColdwellBanker
// and views/site/about_coldwell_banker.php stay for rollback.
$routes->get('about_coldwell_banker', static fn () => redirect()->to(site_url('about-coldwell-banker'), 301));

// Area Insights is a CMS page (slug area-insights, served by the catch-all in Routes.php;
// seeded by GroupBPagesSeeder). Site\Pages::areaInsights stays for rollback.
$routes->get('area_insights', static fn () => redirect()->to(site_url('area-insights'), 301));

// NRI Services is a CMS page (slug nri, served by the catch-all in Routes.php; seeded by
// NriPageSeeder, Figma 134:600). Site\Pages::nri and views/site/nri.php stay for rollback.

// Contact Us is a CMS page (slug contact-us, served by the catch-all in Routes.php; seeded by
// ContactPageSeeder). Site\Pages::contactUs and views/site/contact_us.php stay for rollback.
$routes->get('contact_us', static fn () => redirect()->to(site_url('contact-us'), 301));

// /blog is now the CI4 blog: see public_blog.php (Site\Blog).

// CI3 rendered a missing view (HTTP 500); now an explicit 404.
$routes->get('test-page', 'Site\Pages::testPage');
$routes->get('test_page', 'Site\Pages::testPage');

// Enquiry POSTs (step 7: Site\Enquiry).
$routes->post('nri-enquiry', 'Site\Enquiry::nriEnquiry');
$routes->post('nri_enquiry', 'Site\Enquiry::nriEnquiry');
$routes->post('contact-enquiry', 'Site\Enquiry::contactEnquiry');
$routes->post('contact_enquiry', 'Site\Enquiry::contactEnquiry');
$routes->post('home/contact_enquiry', 'Site\Enquiry::contactEnquiry');
$routes->post('contact-enquiry-popup', 'Site\Enquiry::contactEnquiryPopup');
$routes->post('contact_enquiry_popup', 'Site\Enquiry::contactEnquiryPopup');
$routes->post('home/contact_enquiry_popup', 'Site\Enquiry::contactEnquiryPopup');
