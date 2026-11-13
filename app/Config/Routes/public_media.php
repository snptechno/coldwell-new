<?php

/**
 * Media Center, Awards, Research (App\Controllers\Site\Media).
 * CI3 accepted both the dash and the underscore spelling (translate_uri_dashes).
 *
 * @var CodeIgniter\Router\RouteCollection $routes
 */

$routes->get('media-center', 'Site\Media::mediaCenter');
$routes->get('media_center', 'Site\Media::mediaCenter');
$routes->get('industry-news', 'Site\Media::industryNews');
$routes->get('industry_news', 'Site\Media::industryNews');
$routes->get('knowledge-series', 'Site\Media::knowledgeSeries');
$routes->get('knowledge_series', 'Site\Media::knowledgeSeries');
$routes->get('newsletter', 'Site\Media::newsletter');
$routes->get('awards', 'Site\Media::awards');
$routes->get('research', 'Site\Media::research');

// Lightbox iframes. The list views link to the underscore form, the "load more"
// fragments to the dash form.
$routes->get('iframe-image/(:num)', 'Site\Media::iframeImage/$1');
$routes->get('iframe_image/(:num)', 'Site\Media::iframeImage/$1');
$routes->get('iframe-knowledge-image/(:num)', 'Site\Media::iframeKnowledgeImage/$1');
$routes->get('iframe_knowledge_image/(:num)', 'Site\Media::iframeKnowledgeImage/$1');
$routes->get('iframe-industry-news/(:num)', 'Site\Media::iframeIndustryNews/$1');
$routes->get('iframe_industry_news/(:num)', 'Site\Media::iframeIndustryNews/$1');
$routes->get('iframe-newsletter/(:num)', 'Site\Media::iframeNewsletter/$1');
$routes->get('iframe_newsletter/(:num)', 'Site\Media::iframeNewsletter/$1');
$routes->get('iframe-awards/(:num)', 'Site\Media::iframeAwards/$1');
$routes->get('iframe_awards/(:num)', 'Site\Media::iframeAwards/$1');

// AJAX "load more" (old JS in the list views POSTs start_no to baseUrl + '<name>').
// These names are in the CSRF filter's except list (app/Config/Filters.php).
$routes->post('load_more_news', 'Site\Media::loadMoreNews');
$routes->post('load_more_industry_news', 'Site\Media::loadMoreIndustryNews');
$routes->post('load_more_knowledge_series', 'Site\Media::loadMoreKnowledgeSeries');
$routes->post('load_more_newsletter', 'Site\Media::loadMoreNewsletter');
