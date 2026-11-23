<?php

namespace Config;

use CodeIgniter\Config\Filters as BaseFilters;
use CodeIgniter\Filters\Cors;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\ForceHTTPS;
use CodeIgniter\Filters\Honeypot;
use CodeIgniter\Filters\InvalidChars;
use CodeIgniter\Filters\PageCache;
use CodeIgniter\Filters\PerformanceMetrics;
use CodeIgniter\Filters\SecureHeaders;

class Filters extends BaseFilters
{
    /**
     * Configures aliases for Filter classes to
     * make reading things nicer and simpler.
     *
     * @var array<string, class-string|list<class-string>>
     *
     * [filter_name => classname]
     * or [filter_name => [classname1, classname2, ...]]
     */
    public array $aliases = [
        'csrf'          => CSRF::class,
        'toolbar'       => DebugToolbar::class,
        'honeypot'      => Honeypot::class,
        'invalidchars'  => InvalidChars::class,
        'secureheaders' => SecureHeaders::class,
        'cors'          => Cors::class,
        'forcehttps'    => ForceHTTPS::class,
        'pagecache'     => PageCache::class,
        'performance'   => PerformanceMetrics::class,
        'adminauth'     => \App\Filters\AdminAuth::class, // protects the "admin" route group
        'secheaders'    => \App\Filters\SecurityHeaders::class, // CSP on the blog (public + admin)
    ];

    /**
     * List of special required filters.
     *
     * The filters listed here are special. They are applied before and after
     * other kinds of filters, and always applied even if a route does not exist.
     *
     * Filters set by default provide framework functionality. If removed,
     * those functions will no longer work.
     *
     * @see https://codeigniter.com/user_guide/incoming/filters.html#provided-filters
     *
     * @var array{before: list<string>, after: list<string>}
     */
    public array $required = [
        'before' => [
            'forcehttps', // Force Global Secure Requests
            'pagecache',  // Web Page Caching
        ],
        'after' => [
            'pagecache',   // Web Page Caching
            'performance', // Performance Metrics
            'toolbar',     // Debug Toolbar
        ],
    ];

    /**
     * List of filter aliases that are always
     * applied before and after every request.
     *
     * @var array{
     *     before: array<string, array{except: list<string>|string}>|list<string>,
     *     after: array<string, array{except: list<string>|string}>|list<string>
     * }
     */
    public array $globals = [
        'before' => [
            // 'honeypot',
            // audit ADM-03/SEC-08: CI3 had CSRF off; every POST must carry a token.
            // Exception: the public site's READ-ONLY AJAX endpoints, which the unchanged
            // front-end JS calls with POST and no token. They return public data and change
            // nothing, so CSRF (which protects state changes) does not apply. Every endpoint
            // that writes data (enquiry forms, newsletter, job applications, admin) stays protected.
            'csrf' => ['except' => [
                'load_more_news', 'load_more_newsletter', 'load_more_industry_news', 'load_more_knowledge_series',
                'load_group_images_gallery', 'get_team_information',
                'get_owner_info', 'get_specialist_info', 'home/get_owner_info', 'home/get_specialist_info',
                'get_all_franchisee_location', // map markers on /our-franchise
                'get_franchisee_location_by_city', 'get_franchisee_name', 'search_franchisee_by_location',
                // CKEditor 4.5 image upload posts from an iframe without the field; Admin\Blog::upload_image
                // checks the token from the query string itself, and the route needs an admin login.
                'admin/blog/upload-image',
            ]],
            'invalidchars', // rejects invalid UTF-8 / control chars in input
        ],
        'after' => [
            // 'honeypot',
            'secureheaders', // X-Frame-Options, X-Content-Type-Options, etc. (audit LC-07)
        ],
    ];

    /**
     * List of filter aliases that works on a
     * particular HTTP method (GET, POST, etc.).
     *
     * Example:
     * 'POST' => ['foo', 'bar']
     *
     * If you use this, you should disable auto-routing because auto-routing
     * permits any HTTP method to access a controller. Accessing the controller
     * with a method you don't expect could bypass the filter.
     *
     * @var array<string, list<string>>
     */
    public array $methods = [];

    /**
     * List of filter aliases that should run on any
     * before or after URI patterns.
     *
     * Example:
     * 'isLoggedIn' => ['before' => ['account/*', 'profiles/*']]
     *
     * @var array<string, array<string, list<string>>>
     */
    public array $filters = [
        // CSP on the blog's public pages and its admin (see App\Filters\SecurityHeaders).
        'secheaders' => ['after' => ['blog', 'blog/*', 'blogs', 'blogs/*', 'sitemap.xml', 'admin/blog', 'admin/blog/*']],
    ];
}
