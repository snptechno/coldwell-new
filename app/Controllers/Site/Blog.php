<?php

namespace App\Controllers\Site;

use App\Controllers\BaseController;
use App\Libraries\BlogCache;
use App\Libraries\BlogContent;
use App\Models\BlogCategoriesModel;
use App\Models\BlogPostsModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Public blog (replaces the Drupal 7 site at /blogs).
 *
 *   /blog                     listing, ?page=N
 *   /blog/category/{slug}     category listing
 *   /blog/{slug}              post
 *   /blog/feed                RSS 2.0
 *   /sitemap.xml              XML sitemap (main pages + blog)
 *   /blogs/...                301s from every old Drupal URL
 */
class Blog extends BaseController
{
    private const PER_PAGE  = 9;
    private const SITE_NAME = 'Coldwell Banker India';

    public function index(): string
    {
        return $this->listing(null);
    }

    public function category(string $slug): string
    {
        $cat = BlogCache::remember('cat:' . $slug, static fn () => model(BlogCategoriesModel::class)->activeBySlug($slug));
        if ($cat === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        return $this->listing($cat);
    }

    private function listing(?array $cat): string
    {
        $posts = model(BlogPostsModel::class);
        $asked = $this->request->getGet('page');
        $page  = $asked === null ? 1 : (int) $asked;
        $catId = $cat !== null ? (int) $cat['id'] : null;
        $list  = BlogCache::remember('list:' . ($catId ?? 0) . ':' . $page, static function () use ($posts, $catId): array {
            $rows = $posts->publicPage(self::PER_PAGE, $catId);

            return ['rows' => $rows, 'pages' => $posts->pager->getPageCount()];
        });
        $rows = $list['rows'];
        $last = max(1, (int) $list['pages']);
        // ?page=0, ?page=abc and pages past the end are not real pages.
        if ($asked !== null && ((string) $page !== (string) $asked || $page < 1 || $page > $last)) {
            throw PageNotFoundException::forPageNotFound();
        }

        $baseUrl = $cat !== null ? base_url('blog/category/' . $cat['slug']) : base_url('blog');
        $pageUrl = static fn (int $n) => $n <= 1 ? $baseUrl : $baseUrl . '?page=' . $n;

        $title = $cat !== null ? ($cat['meta_title'] ?: $cat['name'] . ' - Real Estate Blog') : 'Real Estate Blog - News, Tips & Insights';
        if ($page > 1) {
            $title .= ' - Page ' . $page;
        }
        $desc = $cat !== null
            ? ($cat['meta_description'] ?: ($cat['description'] ?: 'Articles about ' . $cat['name'] . ' from ' . self::SITE_NAME . '.'))
            : 'Real estate news, buying and renting advice, property trends and stories from ' . self::SITE_NAME . '.';

        $crumbs = [['Home', base_url()], ['Blog', base_url('blog')]];
        if ($cat !== null) {
            $crumbs[] = [$cat['name'], $baseUrl];
        }

        $seo = [
            'title'       => $title . ' | ' . self::SITE_NAME,
            'description' => $desc,
            'canonical'   => $pageUrl($page),
            'type'        => 'website',
            'image'       => ! empty($rows[0]['featured_image']) ? base_url('uploads/blog/' . rawurlencode($rows[0]['featured_image'])) : null,
            'prev'        => $page > 1 ? $pageUrl($page - 1) : null,
            'next'        => $page < $last ? $pageUrl($page + 1) : null,
            'robots'      => 'index, follow',
            'jsonld'      => [
                [
                    '@context' => 'https://schema.org',
                    '@type'    => 'Blog',
                    'name'     => $cat !== null ? $cat['name'] . ' - ' . self::SITE_NAME . ' Blog' : self::SITE_NAME . ' Blog',
                    'url'      => $baseUrl,
                    'blogPost' => array_map(static fn ($r) => [
                        '@type'         => 'BlogPosting',
                        'headline'      => $r['title'],
                        'url'           => base_url('blog/' . $r['slug']),
                        'datePublished' => date('c', strtotime($r['published_at'])),
                    ], $rows),
                ],
                $this->breadcrumbLd($crumbs),
            ],
        ];

        return view('site/blog/index', [
            'seo'         => $seo,
            'posts'       => $rows,
            'category'    => $cat,
            'page'        => $page,
            'last_page'   => $last,
            'page_url'    => $pageUrl,
            'crumbs'      => $crumbs,
            'categories'  => $this->sidebarCategories(),
            'recent'      => $this->recentPosts(5),
            'page_active' => 'blog',
        ]);
    }

    public function show(string $slug): string|RedirectResponse
    {
        $model = model(BlogPostsModel::class);
        $post  = BlogCache::remember('post:' . $slug, static fn () => $model->liveBySlug($slug));
        if ($post === null) {
            // Uppercase or stray characters: send to the canonical slug if one matches.
            $clean = BlogContent::slugify($slug);
            if ($clean !== $slug && $clean !== '' && $model->liveBySlug($clean) !== null) {
                return redirect()->to(base_url('blog/' . $clean), 301);
            }

            throw PageNotFoundException::forPageNotFound();
        }
        if ($post['slug'] !== $slug) { // e.g. /blog/Garden-Homes (MySQL compares case-insensitively)
            return redirect()->to(base_url('blog/' . $post['slug']), 301);
        }
        $postId = (int) $post['id'];
        BlogCache::countView($postId, static fn (int $n) => $model->incrementViews($postId, $n));

        $url    = base_url('blog/' . $post['slug']);
        $image  = $post['og_image'] ?: $post['featured_image'];
        $imgUrl = $image ? base_url('uploads/blog/' . rawurlencode($image)) : null;
        $desc   = $post['meta_description'] ?: ($post['excerpt'] ?: BlogContent::excerpt((string) $post['content']));
        $desc   = BlogContent::excerpt($desc, 160);

        $crumbs = [['Home', base_url()], ['Blog', base_url('blog')]];
        if (! empty($post['category_slug'])) {
            $crumbs[] = [$post['category_name'], base_url('blog/category/' . $post['category_slug'])];
        }
        $crumbs[] = [$post['title'], $url];

        $published = date('c', strtotime($post['published_at']));
        $modified  = date('c', strtotime($post['updated_at'] ?: $post['published_at']));

        $seo = [
            'title'       => ($post['meta_title'] ?: $post['title']) . ' | ' . self::SITE_NAME,
            'description' => $desc,
            'keywords'    => $post['meta_keywords'] ?? '',
            'canonical'   => $post['canonical_url'] ?: $url,
            'type'        => 'article',
            'image'       => $imgUrl,
            'image_alt'   => $post['image_alt'] ?: $post['title'],
            'robots'      => (int) $post['noindex'] === 1 ? 'noindex, follow' : 'index, follow, max-image-preview:large',
            'article'     => [
                'published_time' => $published,
                'modified_time'  => $modified,
                'section'        => $post['category_name'] ?? null,
                'author'         => $post['author'] ?: self::SITE_NAME,
            ],
            'jsonld' => [
                array_filter([
                    '@context'         => 'https://schema.org',
                    '@type'            => 'BlogPosting',
                    'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $url],
                    'headline'         => mb_substr($post['title'], 0, 110),
                    'description'      => $desc,
                    'image'            => $imgUrl ? [$imgUrl] : null,
                    'datePublished'    => $published,
                    'dateModified'     => $modified,
                    'author'           => ['@type' => $post['author'] && $post['author'] !== self::SITE_NAME ? 'Person' : 'Organization', 'name' => $post['author'] ?: self::SITE_NAME],
                    'publisher'        => [
                        '@type' => 'Organization',
                        'name'  => self::SITE_NAME,
                        'logo'  => ['@type' => 'ImageObject', 'url' => base_url('assets/images/logo.png')],
                    ],
                    'articleSection'   => $post['category_name'] ?? null,
                    'keywords'         => $post['meta_keywords'] ?: null,
                    'url'              => $url,
                ], static fn ($v) => $v !== null && $v !== ''),
                $this->breadcrumbLd($crumbs),
            ],
        ];

        return view('site/blog/show', [
            'seo'         => $seo,
            'post'        => $post,
            'crumbs'      => $crumbs,
            'related'     => BlogCache::remember('related:' . $post['id'], static fn () => $model->related($post, 3)),
            'categories'  => $this->sidebarCategories(),
            'recent'      => $this->recentPosts(5),
            'page_active' => 'blog',
        ]);
    }

    public function feed(): ResponseInterface
    {
        $rows = BlogCache::remember('feed', static fn () => model(BlogPostsModel::class)->withCategory()->live()
            ->orderBy('blog_posts.published_at', 'DESC')->findAll(20));

        return $this->response
            ->setContentType('application/rss+xml', 'UTF-8')
            ->setBody(view('site/blog/feed', ['posts' => $rows], ['debug' => false]));
    }

    public function sitemap(): ResponseInterface
    {
        $urls = [];
        // Main public pages (static routes).
        foreach (['', 'about-coldwell-banker', 'our-franchise', 'media-center', 'awards', 'career', 'research', 'nri',
            'finding-a-home', 'selling-a-home', 'learning-development', 'contact-us', 'faq', 'privacy-policy', 'terms-and-conditions'] as $p) {
            $urls[] = ['loc' => base_url($p), 'changefreq' => 'monthly', 'priority' => $p === '' ? '1.0' : '0.6'];
        }
        $posts  = BlogCache::remember('sitemap', static fn () => model(BlogPostsModel::class)->forSitemap());
        $latest = $posts[0]['updated_at'] ?? $posts[0]['published_at'] ?? null;
        $urls[] = ['loc' => base_url('blog'), 'lastmod' => $latest, 'changefreq' => 'weekly', 'priority' => '0.8'];
        foreach ($this->sidebarCategories() as $c) {
            $urls[] = ['loc' => base_url('blog/category/' . $c['slug']), 'changefreq' => 'weekly', 'priority' => '0.5'];
        }
        foreach ($posts as $p) {
            $urls[] = ['loc' => base_url('blog/' . $p['slug']), 'lastmod' => $p['updated_at'] ?: $p['published_at'], 'changefreq' => 'monthly', 'priority' => '0.7'];
        }

        return $this->response
            ->setContentType('application/xml', 'UTF-8')
            ->setBody(view('site/blog/sitemap', ['urls' => $urls], ['debug' => false]));
    }

    /**
     * 301 for every URL of the old Drupal blog at /blogs/... .
     * Old aliases are kept in blog_posts.legacy_alias / blog_categories.legacy_alias.
     */
    public function legacy(string ...$segments): RedirectResponse
    {
        $path = trim(implode('/', $segments), '/');
        $path = (string) preg_replace('#^index\.php/?#', '', $path);
        if ($q = $this->request->getGet('q')) { // Drupal ?q=node/12 style
            $path = trim((string) $q, '/');
        }

        return redirect()->to($this->legacyTarget($path), 301);
    }

    private function legacyTarget(string $path): string
    {
        $posts = model(BlogPostsModel::class);
        $cats  = model(BlogCategoriesModel::class);
        $lower = strtolower($path);

        if ($lower === '' || $lower === 'articles' || $lower === 'blog' || str_starts_with($lower, 'articles/page')) {
            return base_url('blog');
        }
        if (in_array($lower, ['rss.xml', 'articles/feed', 'blog/feed', 'feed'], true)) {
            return base_url('blog/feed');
        }
        if (preg_match('#^node/(\d+)#', $lower, $m) && ($p = $posts->byLegacyNid((int) $m[1])) && $this->isLive($p)) {
            return base_url('blog/' . $p['slug']);
        }
        if (preg_match('#^taxonomy/term/(\d+)#', $lower, $m) && ($c = $cats->byLegacyTid((int) $m[1])) && (int) $c['status'] === 1) {
            return base_url('blog/category/' . $c['slug']);
        }
        if (preg_match('#^sites/default/files/(?:styles/[^/]+/public/)?(.+)$#', $path, $m)) {
            $file = basename($m[1]);
            if (is_file(rtrim(FCPATH, '\\/') . '/uploads/blog/' . $file)) {
                return base_url('uploads/blog/' . rawurlencode($file));
            }

            return base_url('blog');
        }
        // Aliases: content/{slug}, categories/{slug} (or anything else stored at import).
        if (($p = $posts->byLegacyAlias($lower)) && $this->isLive($p)) {
            return base_url('blog/' . $p['slug']);
        }
        if (($c = $cats->byLegacyAlias($lower)) && (int) $c['status'] === 1) {
            return base_url('blog/category/' . $c['slug']);
        }
        if (preg_match('#^content/([a-z0-9-]+)$#', $lower, $m) && $posts->liveBySlug($m[1])) {
            return base_url('blog/' . $m[1]);
        }
        if (preg_match('#^categories/([a-z0-9-]+)$#', $lower, $m) && $cats->activeBySlug($m[1])) {
            return base_url('blog/category/' . $m[1]);
        }

        return base_url('blog');
    }

    /** Sidebar categories with live-post counts (cached). */
    private function sidebarCategories(): array
    {
        return BlogCache::remember('categories', static fn () => model(BlogCategoriesModel::class)->listPublicWithCounts());
    }

    /** Latest live posts (cached). */
    private function recentPosts(int $limit): array
    {
        return BlogCache::remember('recent:' . $limit, static fn () => model(BlogPostsModel::class)->recent($limit));
    }

    /** Published and not scheduled in the future (a 301 to a 404 would waste the redirect). */
    private function isLive(array $p): bool
    {
        return (int) $p['status'] === 1 && ! empty($p['published_at']) && strtotime((string) $p['published_at']) <= time();
    }

    /** @param list<array{0: string, 1: string}> $crumbs */
    private function breadcrumbLd(array $crumbs): array
    {
        $items = [];
        foreach ($crumbs as $i => [$name, $url]) {
            $items[] = ['@type' => 'ListItem', 'position' => $i + 1, 'name' => $name, 'item' => $url];
        }

        return ['@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => $items];
    }
}
