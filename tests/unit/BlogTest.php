<?php

use App\Libraries\BlogContent;
use App\Models\BlogCategoriesModel;
use App\Models\BlogPostsModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * Blog module: slugs, sanitiser, public pages, SEO tags, legacy Drupal 301s, admin auth.
 * Runs on the `tests` DB group (coldwell_tests, prefix tests_) with the App migrations.
 *
 * @internal
 */
final class BlogTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $migrate     = true;
    protected $migrateOnce = false;
    protected $refresh     = true;
    protected $namespace   = 'App';

    private int $catId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->catId = (int) (new BlogCategoriesModel())->insert([
            'name' => 'News', 'slug' => 'news', 'status' => 1, 'legacy_drupal_tid' => 35, 'legacy_alias' => 'categories/news',
        ]);
        $posts = new BlogPostsModel();
        $posts->insert([
            'title' => 'Garden Homes', 'slug' => 'garden-homes', 'excerpt' => 'Green living excerpt.',
            'content' => '<p>Body of garden homes.</p>', 'featured_image' => 'g.jpg', 'category_id' => $this->catId,
            'status' => 1, 'published_at' => '2015-03-03 10:00:00', 'meta_description' => 'Garden homes meta description.',
            'legacy_drupal_nid' => 174, 'legacy_alias' => 'content/garden-homes',
        ]);
        $posts->insert([
            'title' => 'Secret Draft', 'slug' => 'secret-draft', 'content' => '<p>x</p>', 'status' => 0,
            'published_at' => '2015-03-03 10:00:00', 'legacy_drupal_nid' => 999,
        ]);
        $posts->insert([
            'title' => 'Future Post', 'slug' => 'future-post', 'content' => '<p>x</p>', 'status' => 1,
            'published_at' => date('Y-m-d H:i:s', time() + 86400),
        ]);
    }

    public function testSlugify(): void
    {
        $this->assertSame('8-reasons-you-should-consider-buying-than-renting', BlogContent::slugify('8 Reasons you should consider Buying than Renting'));
        $this->assertSame('bizzare-homes-a-style-statement', BlogContent::slugify('  Bizzare Homes - A style statement! '));
        $this->assertSame('from-the-mds-desk', BlogContent::slugify("From The MD's Desk"));
        $this->assertSame('rock-and-roll', BlogContent::slugify('Rock & Roll'));
        $this->assertTrue(BlogContent::isValidSlug('garden-homes'));
        $this->assertFalse(BlogContent::isValidSlug('Garden Homes'));
    }

    public function testUniqueSlugAppendsCounter(): void
    {
        $m = new BlogPostsModel();
        $this->assertSame('garden-homes-2', $m->uniqueSlug('Garden Homes'));
        $id = (int) $m->byLegacyNid(174)['id'];
        $this->assertSame('garden-homes', $m->uniqueSlug('Garden Homes', $id), 'own slug is free when editing');
        $this->assertSame('brand-new', $m->uniqueSlug('Brand New'));
    }

    public function testSanitizerStripsScripts(): void
    {
        $out = BlogContent::sanitize('<p onclick="x()">Hi <a href="javascript:alert(1)">a</a><script>alert(1)</script><img src="/a.jpg" onerror="y"></p>');
        $this->assertStringNotContainsString('script', $out);
        $this->assertStringNotContainsString('onclick', $out);
        $this->assertStringNotContainsString('onerror', $out);
        $this->assertStringNotContainsString('javascript', $out);
        $this->assertStringContainsString('<img src="/a.jpg">', $out);
    }

    public function testListingShowsLivePostsOnly(): void
    {
        $r = $this->get('blog');
        $r->assertStatus(200);
        $r->assertSee('Garden Homes');
        $r->assertDontSee('Secret Draft');
        $r->assertDontSee('Future Post');
        $r->assertSee('rel="canonical"', null);
        $this->get('blog?page=5')->assertStatus(404);
    }

    public function testCategoryPage(): void
    {
        $r = $this->get('blog/category/news');
        $r->assertStatus(200);
        $r->assertSee('Garden Homes');
        $this->get('blog/category/unknown')->assertStatus(404);
    }

    public function testDetailHasSeoTags(): void
    {
        $r = $this->get('blog/garden-homes');
        $r->assertStatus(200);
        $body = $r->getBody();
        $this->assertStringContainsString('<title>Garden Homes | Coldwell Banker India</title>', $body);
        $this->assertStringContainsString('<meta name="description" content="Garden homes meta description."', $body);
        $this->assertMatchesRegularExpression('#<link rel="canonical" href="[^"]*/blog/garden-homes"#', $body);
        $this->assertStringContainsString('<meta property="og:type" content="article"', $body);
        $this->assertStringContainsString('og:image', $body);
        $this->assertStringContainsString('twitter:card', $body);
        $this->assertStringContainsString('"@type":"BlogPosting"', $body);
        $this->assertStringContainsString('"@type":"BreadcrumbList"', $body);
    }

    public function testDraftScheduledAndUnknownAre404(): void
    {
        $this->get('blog/secret-draft')->assertStatus(404);
        $this->get('blog/future-post')->assertStatus(404);
        $this->get('blog/no-such-post')->assertStatus(404);
    }

    public function testLegacyDrupalUrlsRedirect301(): void
    {
        $cases = [
            'blogs'                                    => '/blog',
            'blogs/articles'                           => '/blog',
            'blogs/content/garden-homes'               => '/blog/garden-homes',
            'blogs/node/174'                           => '/blog/garden-homes',
            'blogs/node/999'                           => '/blog', // draft
            'blogs/categories/news'                    => '/blog/category/news',
            'blogs/taxonomy/term/35'                   => '/blog/category/news',
            'blogs/rss.xml'                            => '/blog/feed',
            'blogs/content/how-beat-heat-summers-home' => '/blog',
        ];
        foreach ($cases as $from => $to) {
            $r = $this->get($from);
            $r->assertStatus(301);
            $this->assertStringEndsWith($to, $r->getRedirectUrl(), $from);
        }
    }

    public function testFeedAndSitemap(): void
    {
        $feed = $this->get('blog/feed');
        $feed->assertStatus(200);
        $this->assertSame('application/rss+xml; charset=UTF-8', $feed->response()->getHeaderLine('Content-Type'));
        $this->assertSame(1, substr_count((string) $feed->response()->getBody(), '<item>'));
        $this->assertStringContainsString('/blog/garden-homes</link>', (string) $feed->response()->getBody());

        $map = $this->get('sitemap.xml');
        $map->assertStatus(200);
        $this->assertStringContainsString('/blog/garden-homes</loc>', (string) $map->response()->getBody());
        $this->assertStringNotContainsString('secret-draft', (string) $map->response()->getBody());
    }

    public function testAdminRequiresLogin(): void
    {
        foreach (['admin/blog/posts', 'admin/blog/posts/add', 'admin/blog/categories'] as $url) {
            $r = $this->withSession([])->get($url);
            $r->assertRedirect();
            $this->assertStringEndsWith('admin/login', $r->getRedirectUrl());
        }
    }

    public function testAdminListAndFormRenderWhenLoggedIn(): void
    {
        $r = $this->withSession(['admin_user' => 'tester'])->get('admin/blog/posts?q=garden');
        $r->assertStatus(200);
        $r->assertSee('Garden Homes');
        $r->assertDontSee('Secret Draft');
        $r = $this->withSession(['admin_user' => 'tester'])->get('admin/blog/posts/add');
        $r->assertStatus(200);
        $r->assertSee('Google preview');
        $r = $this->withSession(['admin_user' => 'tester'])->get('admin/blog/slug-check?slug=Garden%20Homes');
        $r->assertStatus(200);
        $r->assertJSONFragment(['slug' => 'garden-homes', 'available' => false, 'suggestion' => 'garden-homes-2']);
    }
}
