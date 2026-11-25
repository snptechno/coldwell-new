<?php

use CodeIgniter\Router\RouteCollection;

/**
 * Coldwell Banker India - routes.
 *
 * Auto-routing is OFF (app/Config/Routing.php): every URL must be declared, with
 * the HTTP method it accepts. This replaces CI3's catch-all
 * `^(:any)(/:any)?$ => Home/$0` (application/config/routes.php), which let any
 * controller method be called by URL and let a plain GET insert rows.
 *
 * Routes are split per site area in app/Config/Routes/*.php so each area can be
 * maintained on its own. Every live URL keeps its exact path, including the
 * dash AND underscore spellings that CI3's translate_uri_dashes accepted.
 *
 * @var RouteCollection $routes
 */

foreach (glob(APPPATH . 'Config/Routes/*.php') ?: [] as $file) {
    require $file;
}

// Admin login & logout stay outside the 'adminauth' filter.
$routes->match(['GET', 'POST'], 'admin', 'Admin\Login::index');
$routes->match(['GET', 'POST'], 'admin/login', 'Admin\Login::index');
$routes->match(['GET', 'POST'], 'admin/login/index', 'Admin\Login::index');
$routes->get('admin/login/logout', 'Admin\Login::logout');

// Admin - every route in this group requires a logged-in admin (App\Filters\AdminAuth).
$routes->group('admin', ['filter' => 'adminauth'], static function (RouteCollection $routes): void {
    // Dashboard
    $routes->get('dashboard', 'Admin\Dashboard::index');
    $routes->get('dashboard/index', 'Admin\Dashboard::index');
    $routes->get('dashboard/index/(:segment)', 'Admin\Dashboard::index/$1');

    // Learning Overview
    $routes->get('section/learning_overview', 'Admin\Section::learning_overview');
    $routes->match(['GET', 'POST'], 'section/update_overview', 'Admin\Section::update_overview');
    $routes->match(['GET', 'POST'], 'section/update_overview/(:num)', 'Admin\Section::update_overview/$1');

    // Learning Gallery
    $routes->get('section/learning_gallery', 'Admin\Section::learning_gallery');
    $routes->match(['GET', 'POST'], 'section/add_learning_gallery', 'Admin\Section::add_learning_gallery');
    $routes->match(['GET', 'POST'], 'section/add_learning_gallery/(:num)', 'Admin\Section::add_learning_gallery/$1');
    $routes->post('section/delete_learning_gallery_image/(:num)', 'Admin\Section::delete_learning_gallery_image/$1');
    $routes->post('section/change_status_learning_gallery/(:num)/(:num)', 'Admin\Section::change_status_learning_gallery/$1/$2');
    $routes->post('section/update_group_id_life_at_coldwell_gallery', 'Admin\Section::update_group_id_life_at_coldwell_gallery');

    // Learning Gallery Thumbnails
    $routes->get('section/learning_gallery_thumbnails', 'Admin\Section::learning_gallery_thumbnails');
    $routes->match(['GET', 'POST'], 'section/add_gallery_thumbnail', 'Admin\Section::add_gallery_thumbnail');
    $routes->match(['GET', 'POST'], 'section/add_gallery_thumbnail/(:num)', 'Admin\Section::add_gallery_thumbnail/$1');
    $routes->post('section/delete_gallery_thumbnail_image/(:num)', 'Admin\Section::delete_gallery_thumbnail_image/$1');
    $routes->post('section/change_status_learning_gallery_thumbnail/(:num)/(:num)', 'Admin\Section::change_status_learning_gallery_thumbnail/$1/$2');
    $routes->post('section/update_group_id_life_at_coldwell_gallery_thumb', 'Admin\Section::update_group_id_life_at_coldwell_gallery_thumb');

    // Learning Events
    $routes->get('section/learning_events', 'Admin\Section::learning_events');
    $routes->match(['GET', 'POST'], 'section/add_events', 'Admin\Section::add_events');
    $routes->match(['GET', 'POST'], 'section/add_events/(:num)', 'Admin\Section::add_events/$1');
    $routes->get('section/change_event_status/(:num)/(:num)', 'Admin\Section::change_event_status/$1/$2');
    $routes->post('section/delete_event/(:num)', 'Admin\Section::delete_event/$1');

    // Cities
    $routes->get('section/manage_cities', 'Admin\Section::manage_cities');
    $routes->match(['GET', 'POST'], 'section/add_city', 'Admin\Section::add_city');
    $routes->match(['GET', 'POST'], 'section/add_city/(:num)', 'Admin\Section::add_city/$1');
    $routes->post('section/delete_city/(:num)', 'Admin\Section::delete_city/$1');

    // Franchises
    $routes->get('section/manage_franchises', 'Admin\Section::manage_franchises');
    $routes->match(['GET', 'POST'], 'section/add_franchise', 'Admin\Section::add_franchise');
    $routes->match(['GET', 'POST'], 'section/add_franchise/(:num)', 'Admin\Section::add_franchise/$1');
    $routes->post('section/delete_franchise/(:num)', 'Admin\Section::delete_franchise/$1');
    $routes->post('section/change_status_franchises/(:num)/(:num)', 'Admin\Section::change_status_franchises/$1/$2');

    // Real Estate Specialists
    $routes->get('section/specialist_list/(:num)', 'Admin\Section::specialist_list/$1');
    $routes->get('section/specialist_list/(:num)/(:num)', 'Admin\Section::specialist_list/$1/$2');
    $routes->match(['GET', 'POST'], 'section/update_specialist/(:num)', 'Admin\Section::update_specialist/$1');
    $routes->match(['GET', 'POST'], 'section/update_specialist/(:num)/(:num)', 'Admin\Section::update_specialist/$1/$2');
    $routes->get('section/manage_real_estate_specialist/(:num)', 'Admin\Section::manage_real_estate_specialist/$1');
    $routes->match(['GET', 'POST'], 'section/update_real_estate_specialist/(:num)', 'Admin\Section::update_real_estate_specialist/$1');
    $routes->match(['GET', 'POST'], 'section/update_real_estate_specialist/(:num)/(:num)', 'Admin\Section::update_real_estate_specialist/$1/$2');
    $routes->post('section/delete_specialist/(:num)/(:num)', 'Admin\Section::delete_specialist/$1/$2');

    // Franchise Owners
    $routes->get('section/franchise_owners_list/(:num)', 'Admin\Section::franchise_owners_list/$1');
    $routes->get('section/franchise_owners_list/(:num)/(:num)', 'Admin\Section::franchise_owners_list/$1/$2');
    $routes->match(['GET', 'POST'], 'section/update_franchise_owner/(:num)', 'Admin\Section::update_franchise_owner/$1');
    $routes->match(['GET', 'POST'], 'section/update_franchise_owner/(:num)/(:num)', 'Admin\Section::update_franchise_owner/$1/$2');
    $routes->get('section/manage_franchise_owners/(:num)', 'Admin\Section::manage_franchise_owners/$1');
    $routes->match(['GET', 'POST'], 'section/update_franchise_owners/(:num)', 'Admin\Section::update_franchise_owners/$1');
    $routes->match(['GET', 'POST'], 'section/update_franchise_owners/(:num)/(:num)', 'Admin\Section::update_franchise_owners/$1/$2');
    $routes->post('section/delete_franchise_owner/(:num)/(:num)', 'Admin\Section::delete_franchise_owner/$1/$2');

    // Franchise Testimonials
    $routes->get('section/manage_franchiese_testimonials/(:num)', 'Admin\Section::manage_franchiese_testimonials/$1');
    $routes->match(['GET', 'POST'], 'section/add_franchise_testimonials/(:num)', 'Admin\Section::add_franchise_testimonials/$1');
    $routes->match(['GET', 'POST'], 'section/add_franchise_testimonials/(:num)/(:num)', 'Admin\Section::add_franchise_testimonials/$1/$2');
    $routes->post('section/delete_franchise_testimonials/(:num)/(:num)', 'Admin\Section::delete_franchise_testimonials/$1/$2');
    $routes->post('section/change_status_franchise_testimonials/(:num)/(:num)/(:num)', 'Admin\Section::change_status_franchise_testimonials/$1/$2/$3');

    // Corporate Teams
    $routes->get('section/manage_corporate_teams', 'Admin\Section::manage_corporate_teams');
    $routes->match(['GET', 'POST'], 'section/update_corporate_team', 'Admin\Section::update_corporate_team');
    $routes->match(['GET', 'POST'], 'section/update_corporate_team/(:num)', 'Admin\Section::update_corporate_team/$1');
    $routes->post('section/delete_corporate_team/(:num)', 'Admin\Section::delete_corporate_team/$1');
    $routes->post('section/delete_corporate_team/(:num)/(:num)', 'Admin\Section::delete_corporate_team/$1/$2');
    $routes->post('section/change_status_corporate_team/(:num)/(:num)', 'Admin\Section::change_status_corporate_team/$1/$2');
    $routes->post('section/update_order_no_corporate_team', 'Admin\Section::update_order_no_corporate_team');

    // Pages
    $routes->get('section/list_pages', 'Admin\Section::list_pages');
    $routes->get('section/update_page/(:num)', 'Admin\Section::update_page/$1');
    $routes->match(['GET', 'POST'], 'section/save_page/(:num)', 'Admin\Section::save_page/$1');
    $routes->post('section/change_status_page/(:num)/(:num)', 'Admin\Section::change_status_page/$1/$2');
    $routes->post('section/delete_page/(:num)', 'Admin\Section::delete_page/$1');

    // Videos
    $routes->get('section/finding_home_videos', 'Admin\Section::finding_home_videos');
    $routes->match(['GET', 'POST'], 'section/add_home_videos', 'Admin\Section::add_home_videos');
    $routes->match(['GET', 'POST'], 'section/add_home_videos/(:num)', 'Admin\Section::add_home_videos/$1');
    $routes->post('section/delete_home_videos/(:num)', 'Admin\Section::delete_home_videos/$1');
    $routes->post('section/change_status_finding_home_videos/(:num)/(:num)', 'Admin\Section::change_status_finding_home_videos/$1/$2');

    $routes->get('section/selling_home_videos', 'Admin\Section::selling_home_videos');
    $routes->match(['GET', 'POST'], 'section/add_selling_videos', 'Admin\Section::add_selling_videos');
    $routes->match(['GET', 'POST'], 'section/add_selling_videos/(:num)', 'Admin\Section::add_selling_videos/$1');
    $routes->post('section/delete_selling_videos/(:num)', 'Admin\Section::delete_selling_videos/$1');
    $routes->post('section/change_status_selling_videos/(:num)/(:num)', 'Admin\Section::change_status_selling_videos/$1/$2');

    $routes->get('section/testimonial_videos', 'Admin\Section::testimonial_videos');
    $routes->match(['GET', 'POST'], 'section/add_testimonial_videos', 'Admin\Section::add_testimonial_videos');
    $routes->match(['GET', 'POST'], 'section/add_testimonial_videos/(:num)', 'Admin\Section::add_testimonial_videos/$1');
    $routes->post('section/change_status_testimonial_videos/(:num)/(:num)', 'Admin\Section::change_status_testimonial_videos/$1/$2');
    $routes->post('section/delete_testimonial_videos/(:num)', 'Admin\Section::delete_testimonial_videos/$1');

    $routes->get('section/cb_india_videos', 'Admin\Section::cb_india_videos');
    $routes->match(['GET', 'POST'], 'section/add_cb_india_videos', 'Admin\Section::add_cb_india_videos');
    $routes->match(['GET', 'POST'], 'section/add_cb_india_videos/(:num)', 'Admin\Section::add_cb_india_videos/$1');
    $routes->post('section/change_status_cb_india_videos/(:num)/(:num)', 'Admin\Section::change_status_cb_india_videos/$1/$2');
    $routes->post('section/delete_cb_india_videos/(:num)', 'Admin\Section::delete_cb_india_videos/$1');

    $routes->get('section/featured_videos', 'Admin\Section::featured_videos');
    $routes->match(['GET', 'POST'], 'section/add_featured_videos', 'Admin\Section::add_featured_videos');
    $routes->match(['GET', 'POST'], 'section/add_featured_videos/(:num)', 'Admin\Section::add_featured_videos/$1');
    $routes->post('section/change_status_featured_videos/(:num)/(:num)', 'Admin\Section::change_status_featured_videos/$1/$2');
    $routes->post('section/delete_featured_videos/(:num)', 'Admin\Section::delete_featured_videos/$1');

    $routes->get('section/success_stories_videos', 'Admin\Section::success_stories_videos');
    $routes->match(['GET', 'POST'], 'section/success_story_videos', 'Admin\Section::success_story_videos');
    $routes->match(['GET', 'POST'], 'section/success_story_videos/(:num)', 'Admin\Section::success_story_videos/$1');
    $routes->match(['GET', 'POST'], 'section/add_success_story_videos', 'Admin\Section::add_success_story_videos');
    $routes->match(['GET', 'POST'], 'section/add_success_story_videos/(:num)', 'Admin\Section::add_success_story_videos/$1');
    $routes->post('section/delete_success_story_videos/(:num)', 'Admin\Section::delete_success_story_videos/$1');
    $routes->post('section/change_status_success_story/(:num)/(:num)', 'Admin\Section::change_status_success_story/$1/$2');

    // Jobs & Applications & Careers
    $routes->get('section/job_openings_list', 'Admin\Section::job_openings_list');
    $routes->match(['GET', 'POST'], 'section/update_job_opening', 'Admin\Section::update_job_opening');
    $routes->match(['GET', 'POST'], 'section/update_job_opening/(:num)', 'Admin\Section::update_job_opening/$1');
    // The job list's edit icon links to /update_job_opening/{id}/{id}; CI3 ignored the extra segment.
    $routes->match(['GET', 'POST'], 'section/update_job_opening/(:num)/(:num)', 'Admin\Section::update_job_opening/$1');
    $routes->match(['GET', 'POST'], 'section/save_job', 'Admin\Section::save_job');
    $routes->match(['GET', 'POST'], 'section/save_job/(:num)', 'Admin\Section::save_job/$1');
    $routes->match(['GET', 'POST'], 'section/add_job_opening', 'Admin\Section::add_job_opening');
    $routes->match(['GET', 'POST'], 'section/add_job_opening/(:num)', 'Admin\Section::add_job_opening/$1');
    $routes->post('section/delete_job_opening/(:num)', 'Admin\Section::delete_job_opening/$1');
    $routes->post('section/change_status_job_opening/(:num)/(:num)', 'Admin\Section::change_status_job_opening/$1/$2');

    $routes->get('section/job_application_list', 'Admin\Section::job_application_list');
    $routes->get('section/check_details_candidate/(:num)', 'Admin\Section::check_details_candidate/$1');
    $routes->get('section/job_candidate_details/(:num)', 'Admin\Section::job_candidate_details/$1');
    $routes->get('section/get_candidate_resume/(:num)', 'Admin\Section::get_candidate_resume/$1');

    $routes->get('section/manage_testimonials', 'Admin\Section::manage_testimonials');
    $routes->match(['GET', 'POST'], 'section/add_carrer_testimonial', 'Admin\Section::add_carrer_testimonial');
    $routes->match(['GET', 'POST'], 'section/add_carrer_testimonial/(:num)', 'Admin\Section::add_carrer_testimonial/$1');
    $routes->post('section/change_status_carrers_testimonial/(:num)/(:num)', 'Admin\Section::change_status_carrers_testimonial/$1/$2');
    $routes->post('section/delete_carrer_testimonial/(:num)', 'Admin\Section::delete_carrer_testimonial/$1');

    $routes->get('section/life_at_coldwell_banker', 'Admin\Section::life_at_coldwell_banker');
    $routes->match(['GET', 'POST'], 'section/add_life_at_coldwell_banker', 'Admin\Section::add_life_at_coldwell_banker');
    $routes->match(['GET', 'POST'], 'section/add_life_at_coldwell_banker/(:num)', 'Admin\Section::add_life_at_coldwell_banker/$1');
    $routes->get('section/life_at_coldwell_banker_images', 'Admin\Section::life_at_coldwell_banker_images');
    $routes->get('section/add_images_coldwell_banker', 'Admin\Section::add_images_coldwell_banker');
    $routes->match(['GET', 'POST'], 'section/add_life_at_coldwell_banker_image', 'Admin\Section::add_life_at_coldwell_banker_image');
    $routes->match(['GET', 'POST'], 'section/add_life_at_coldwell_banker_image/(:num)', 'Admin\Section::add_life_at_coldwell_banker_image/$1');
    $routes->post('section/delete_image_life_at_coldwell/(:num)', 'Admin\Section::delete_image_life_at_coldwell/$1');

    // Home Page (App\Controllers\Admin\HomePage) - redesigned home content. Writes are POST + CSRF.
    // The section segment is validated against App\Libraries\HomeSections::ITEMS (404 otherwise).
    $routes->get('home-page', 'Admin\HomePage::index');
    $routes->match(['GET', 'POST'], 'home-page/content', 'Admin\HomePage::content');
    $routes->get('home-page/([a-z_]+)', 'Admin\HomePage::items/$1');
    $routes->match(['GET', 'POST'], 'home-page/([a-z_]+)/add', 'Admin\HomePage::item_form/$1');
    $routes->match(['GET', 'POST'], 'home-page/([a-z_]+)/edit/(:num)', 'Admin\HomePage::item_form/$1/$2');
    $routes->post('home-page/([a-z_]+)/status/(:num)/(:num)', 'Admin\HomePage::item_status/$1/$2/$3');
    $routes->post('home-page/([a-z_]+)/delete/(:num)', 'Admin\HomePage::item_delete/$1/$2');
    $routes->post('home-page/([a-z_]+)/sort', 'Admin\HomePage::item_sort/$1');

    // Research & Media
    $routes->get('section/manage_research', 'Admin\Section::manage_research');
    $routes->match(['GET', 'POST'], 'section/add_research', 'Admin\Section::add_research');
    $routes->match(['GET', 'POST'], 'section/add_research/(:num)', 'Admin\Section::add_research/$1');
    $routes->post('section/change_status_research/(:num)/(:num)', 'Admin\Section::change_status_research/$1/$2');
    $routes->post('section/delete_research/(:num)', 'Admin\Section::delete_research/$1');

    $routes->get('section/coldwell_banker_news', 'Admin\Section::coldwell_banker_news');
    $routes->match(['GET', 'POST'], 'section/add_coldwell_banker_news', 'Admin\Section::add_coldwell_banker_news');
    $routes->match(['GET', 'POST'], 'section/add_coldwell_banker_news/(:num)', 'Admin\Section::add_coldwell_banker_news/$1');
    $routes->post('section/delete_coldwell_banker_news/(:num)', 'Admin\Section::delete_coldwell_banker_news/$1');

    $routes->get('section/coldwell_banker_industry_news', 'Admin\Section::coldwell_banker_industry_news');
    $routes->match(['GET', 'POST'], 'section/add_coldwell_banker_industry_news', 'Admin\Section::add_coldwell_banker_industry_news');
    $routes->match(['GET', 'POST'], 'section/add_coldwell_banker_industry_news/(:num)', 'Admin\Section::add_coldwell_banker_industry_news/$1');
    $routes->post('section/delete_coldwell_banker_industry_news/(:num)', 'Admin\Section::delete_coldwell_banker_industry_news/$1');

    $routes->get('section/coldwell_banker_knowledge_series', 'Admin\Section::coldwell_banker_knowledge_series');
    $routes->match(['GET', 'POST'], 'section/add_coldwell_banker_knowledge_series', 'Admin\Section::add_coldwell_banker_knowledge_series');
    $routes->match(['GET', 'POST'], 'section/add_coldwell_banker_knowledge_series/(:num)', 'Admin\Section::add_coldwell_banker_knowledge_series/$1');
    $routes->post('section/delete_coldwell_banker_knowledge_series/(:num)', 'Admin\Section::delete_coldwell_banker_knowledge_series/$1');

    $routes->get('section/coldwell_banker_newsletter', 'Admin\Section::coldwell_banker_newsletter');
    $routes->match(['GET', 'POST'], 'section/add_coldwell_banker_newsletter', 'Admin\Section::add_coldwell_banker_newsletter');
    $routes->match(['GET', 'POST'], 'section/add_coldwell_banker_newsletter/(:num)', 'Admin\Section::add_coldwell_banker_newsletter/$1');
    $routes->post('section/delete_coldwell_banker_newsletter/(:num)', 'Admin\Section::delete_coldwell_banker_newsletter/$1');

    $routes->get('section/coldwell_banker_awards', 'Admin\Section::coldwell_banker_awards');
    $routes->match(['GET', 'POST'], 'section/add_coldwell_banker_award', 'Admin\Section::add_coldwell_banker_award');
    $routes->match(['GET', 'POST'], 'section/add_coldwell_banker_award/(:num)', 'Admin\Section::add_coldwell_banker_award/$1');
    $routes->post('section/delete_coldwell_banker_award/(:num)', 'Admin\Section::delete_coldwell_banker_award/$1');

    // MIME check AJAX
    $routes->post('section/check_mime', 'Admin\Section::check_mime');

    // Enquiries & Exports
    $routes->get('section/projects_contact_enquiry', 'Admin\Section::projects_contact_enquiry');
    $routes->get('section/franchisee_enquiry', 'Admin\Section::franchisee_enquiry');
    $routes->post('section/delete_franchisee/(:num)', 'Admin\Section::delete_franchisee/$1');
    $routes->get('section/contact_enquiry', 'Admin\Section::contact_enquiry');
    $routes->post('section/delete_contact/(:num)', 'Admin\Section::delete_contact/$1');
    $routes->get('section/franchisee_project_enquiries', 'Admin\Section::franchisee_project_enquiries');
    $routes->post('section/delete_franchisee_project_enquiry/(:num)', 'Admin\Section::delete_franchisee_project_enquiry/$1');
    $routes->get('section/newsletter_list', 'Admin\Section::newsletter_list');
    $routes->post('section/delete_newslettes_subscriber/(:num)', 'Admin\Section::delete_newslettes_subscriber/$1');
    $routes->get('section/nri_enquiry', 'Admin\Section::nri_enquiry');
    $routes->get('section/nri_enquiry_list', 'Admin\Section::nri_enquiry_list');

    $routes->match(['GET', 'POST'], 'section/export_to_excell', 'Admin\Section::export_to_excell');
    $routes->get('section/export_to_excell/(:segment)', 'Admin\Section::export_to_excell/$1');
    $routes->match(['GET', 'POST'], 'section/export_franchisee_enquiries_to_excell', 'Admin\Section::export_franchisee_enquiries_to_excell');
    $routes->get('section/export_franchisee_enquiries_to_excell/(:segment)', 'Admin\Section::export_franchisee_enquiries_to_excell/$1');
    $routes->match(['GET', 'POST'], 'section/export_franchisee_project_enquiries_to_excell', 'Admin\Section::export_franchisee_project_enquiries_to_excell');
    $routes->get('section/export_franchisee_project_enquiries_to_excell/(:segment)', 'Admin\Section::export_franchisee_project_enquiries_to_excell/$1');
    // Blog (App\Controllers\Admin\Blog) - replaces the Drupal admin at /blogs. Writes are POST + CSRF.
    $routes->get('blog/posts', 'Admin\Blog::posts');
    $routes->match(['GET', 'POST'], 'blog/posts/add', 'Admin\Blog::post_form');
    $routes->match(['GET', 'POST'], 'blog/posts/edit/(:num)', 'Admin\Blog::post_form/$1');
    $routes->post('blog/posts/status/(:num)/(:num)', 'Admin\Blog::post_status/$1/$2');
    $routes->post('blog/posts/delete/(:num)', 'Admin\Blog::post_delete/$1');
    $routes->post('blog/upload-image', 'Admin\Blog::upload_image');
    $routes->get('blog/slug-check', 'Admin\Blog::slug_check');
    $routes->get('blog/categories', 'Admin\Blog::categories');
    $routes->match(['GET', 'POST'], 'blog/categories/add', 'Admin\Blog::category_form');
    $routes->match(['GET', 'POST'], 'blog/categories/edit/(:num)', 'Admin\Blog::category_form/$1');
    $routes->post('blog/categories/status/(:num)/(:num)', 'Admin\Blog::category_status/$1/$2');
    $routes->post('blog/categories/delete/(:num)', 'Admin\Blog::category_delete/$1');
});

// HEAD must answer wherever GET does (CI3 did; search engines and uptime monitors
// use HEAD). CI4 only matches HEAD when it is registered, so mirror every GET route.
// The GET route's options (above all the 'adminauth' group filter) must be copied too,
// otherwise HEAD /admin/... would run admin controllers without a login.
foreach ($routes->getRoutes('GET') as $from => $to) {
    $options = $routes->getRoutesOptions($from, 'GET');
    $routes->head($from, $to, array_intersect_key($options, array_flip(['filter', 'namespace', 'hostname', 'subdomain', 'priority'])));
}

// CMS pages and articles (Site\CmsPage, docs/CMS.md). Catch-all for one lowercase slug
// segment: it MUST stay the last route in this file so every route above still wins.
$routes->get('([a-z0-9][a-z0-9-]*)', 'Site\CmsPage::show/$1', ['filter' => 'secheaders']);
$routes->head('([a-z0-9][a-z0-9-]*)', 'Site\CmsPage::show/$1', ['filter' => 'secheaders']); // HEAD mirror: the loop above ran before this route
