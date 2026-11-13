<?php

/**
 * Public CMS/static pages (App\Controllers\Site\Pages).
 *
 * CI3 reached these through its catch-all route with translate_uri_dashes = TRUE,
 * so every multi-word page answers to both the dash and the underscore spelling.
 *
 * @var CodeIgniter\Router\RouteCollection $routes
 */

$routes->get('faq', 'Site\Pages::faq');

$routes->get('privacy-policy', 'Site\Pages::privacyPolicy');
$routes->get('privacy_policy', 'Site\Pages::privacyPolicy');
$routes->get('privacy', 'Site\Pages::privacyPolicy');

$routes->get('terms-and-conditions', 'Site\Pages::termsAndConditions');
$routes->get('terms_and_conditions', 'Site\Pages::termsAndConditions');
$routes->get('terms', 'Site\Pages::termsAndConditions');

$routes->get('disclaimer', 'Site\Pages::disclaimer');
$routes->get('refund', 'Site\Pages::refund');

$routes->get('quick-pay', 'Site\Pages::quickPay');
$routes->get('quick_pay', 'Site\Pages::quickPay');

$routes->get('about-coldwell-banker', 'Site\Pages::aboutColdwellBanker');
$routes->get('about_coldwell_banker', 'Site\Pages::aboutColdwellBanker');

$routes->get('area-insights', 'Site\Pages::areaInsights');
$routes->get('area_insights', 'Site\Pages::areaInsights');

$routes->get('nri', 'Site\Pages::nri');

$routes->get('contact-us', 'Site\Pages::contactUs');
$routes->get('contact_us', 'Site\Pages::contactUs');

// CI3 redirect() answers a GET with 307.
$routes->get('blog', 'Site\Pages::blog');

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
