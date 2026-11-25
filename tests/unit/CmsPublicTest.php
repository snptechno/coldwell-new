<?php

use App\Database\Seeds\CmsSeeder;
use App\Libraries\Cms\Cms;
use CodeIgniter\Config\Factories;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Config\Services;

/**
 * Public rendering of CMS pages (App\Controllers\Site\CmsPage, app/Views/cms/*).
 *
 * Run with an own table prefix: env 'database.tests.DBPrefix=tc_' vendor/bin/phpunit tests/unit/CmsPublicTest.php
 *
 * @internal
 */
final class CmsPublicTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $migrate     = true;
    protected $migrateOnce = false;
    protected $refresh     = true;
    protected $namespace   = 'App';
    protected $seed        = CmsSeeder::class;

    private const XSS = '<script>alert(1)</script>"><img src=x onerror=alert(1)>';

    protected function setUp(): void
    {
        // Pin this file's prefix (.env wins over a shell env var on Windows).
        $_ENV['database.tests.DBPrefix'] = 'tc_';
        Factories::reset('config');
        parent::setUp();
        $cfg = config('Cache');
        $dir = WRITEPATH . 'cache/test_' . preg_replace('/\W/', '', (string) db_connect()->getPrefix()) . '/';
        if (! is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        $cfg->handler           = 'file';
        $cfg->file['storePath'] = $dir;
        Services::injectMock('cache', Services::cache($cfg, false));
        Cms::clearCache();
    }

    protected function tearDown(): void
    {
        Cms::clearCache();
        parent::tearDown();
    }

    private function addPage(array $row): int
    {
        $now = date('Y-m-d H:i:s');
        $this->db->table('cms_pages')->insert($row + [
            'type' => 'page', 'is_home' => 0, 'status' => 1, 'published_at' => $now, 'created_at' => $now, 'updated_at' => $now,
        ]);

        return (int) $this->db->insertID();
    }

    private function addBlock(int $pageId, string $type, array $settings, int $sort = 10, int $status = 1): int
    {
        $this->db->table('cms_blocks')->insert([
            'page_id' => $pageId, 'type' => $type, 'label' => $type, 'settings' => json_encode($settings),
            'sort_order' => $sort, 'status' => $status,
        ]);

        return (int) $this->db->insertID();
    }

    private function homeBlockId(string $type): int
    {
        return (int) $this->db->table('cms_blocks b')->select('b.id')->join('cms_pages p', 'p.id = b.page_id')
            ->where('p.is_home', 1)->where('b.type', $type)->get()->getRow('id');
    }

    public function testHomeRendersSeededBlocks(): void
    {
        $r = $this->get('/');
        $r->assertOK();
        $html = $r->getBody();
        foreach (['Global Legacy.', 'Years of legacy', 'At our core, real estate', 'Our Presence Across India', 'Delhi NCR',
            'Ways to Grow With Us', 'Franchise Ownership', 'A Century of Expertise', 'Founded in San Francisco',
            'Brighter', 'Quick Links', 'Privacy Policy', 'Franchise Enquiry', 'info@coldwellbanker.in'] as $text) {
            $this->assertStringContainsString($text, $html, $text);
        }
        // Hidden items (unverified) stay out.
        $this->assertStringNotContainsString('[XX]K+', $html);
        $this->assertStringNotContainsString('>Chennai<', $html);
        // Structure of the approved design.
        $this->assertStringContainsString('class="cb-hero__card" data-hero', $html);
        $this->assertStringContainsString('id="cb-city-0"', $html);
        $this->assertStringContainsString('name="source" value="home"', $html);
        $this->assertStringContainsString('<link rel="canonical" href="' . site_url('/') . '"', $html);
        $this->assertMatchesRegularExpression('#<a href="[^"]*"\s+aria-current="page">Home</a>#', $html);
        $this->assertStringContainsString('application/ld+json', $html);
    }

    public function testIndexAliasRendersHome(): void
    {
        $this->get('index')->assertOK();
    }

    public function testUnknownSlug404(): void
    {
        $this->get('no-such-cms-page')->assertStatus(404);
    }

    public function testHomeSlugRedirectsToRoot(): void
    {
        $r = $this->get('home');
        $r->assertStatus(301);
        $this->assertSame(rtrim(site_url('/'), '/'), rtrim($r->getRedirectUrl(), '/'));
    }

    public function testDraft404ForVisitors(): void
    {
        $id = $this->addPage(['title' => 'Draft page', 'slug' => 'draft-page', 'status' => 0]);
        $this->addBlock($id, 'rich_text', ['title' => 'Secret draft', 'body' => '<p>Draft body</p>']);
        $this->get('draft-page')->assertStatus(404);
    }

    public function testDraftPreviewForAdmin(): void
    {
        $id = $this->addPage(['title' => 'Draft page', 'slug' => 'draft-page', 'status' => 0]);
        $this->addBlock($id, 'rich_text', ['title' => 'Secret draft', 'body' => '<p>Draft body</p>']);
        $this->addBlock($id, 'rich_text', ['title' => 'Hidden block', 'body' => '<p>x</p>'], 20, 0);
        $r = $this->withSession(['admin_user' => 'tester'])->get('draft-page?preview=1');
        $r->assertOK();
        $r->assertSee('Secret draft');
        $r->assertSee('Hidden block');
        $r->assertSee('cb-preview');
        $this->assertSame('noindex', $r->response()->getHeaderLine('X-Robots-Tag'));
    }

    public function testPreviewFlagIgnoredWithoutSession(): void
    {
        $this->addPage(['title' => 'Draft page', 'slug' => 'draft-page', 'status' => 0]);
        $this->get('draft-page?preview=1')->assertStatus(404);
    }

    public function testScheduledPage404(): void
    {
        $this->addPage(['title' => 'Later', 'slug' => 'later-page', 'published_at' => date('Y-m-d H:i:s', time() + 86400)]);
        $this->get('later-page')->assertStatus(404);
    }

    public function testInnerPageWithHeaderBarAndRichText(): void
    {
        $id = $this->addPage(['title' => 'About', 'slug' => 'about-cms', 'nav_key' => 'about us', 'meta_title' => 'About SEO title', 'noindex' => 1]);
        $this->addBlock($id, 'rich_text', ['eyebrow' => 'About', 'title' => 'Our story', 'body' => '<p>Hello <strong>world</strong></p>', 'align' => 'center']);
        $this->addBlock($id, 'newsletter_cta', ['title' => 'Join', 'image' => 'assets/images/home-v2/cta.jpg'], 20);
        $r = $this->get('about-cms');
        $r->assertOK();
        $html = $r->getBody();
        $this->assertStringContainsString('class="cb-hbar"', $html);
        $this->assertStringContainsString('<title>About SEO title</title>', $html);
        $this->assertStringContainsString('<meta name="robots" content="noindex', $html);
        $this->assertStringContainsString('<link rel="canonical" href="' . site_url('about-cms') . '"', $html);
        $this->assertStringContainsString('cb-rich--center', $html);
        $this->assertStringContainsString('<strong>world</strong>', $html);
        $this->assertStringContainsString('name="source" value="cms"', $html);
        $this->assertMatchesRegularExpression('#aria-current="page">About Us</a>#', $html);
    }

    public function testPageHero(): void
    {
        $id = $this->addPage(['title' => 'Franchise', 'slug' => 'franchise-cms']);
        $this->addBlock($id, 'page_hero', ['eyebrow' => 'Franchise', 'title' => 'Own a territory', 'lead' => 'Lead', 'image' => 'assets/images/home-v2/hero.jpg']);
        $html = $this->get('franchise-cms')->getBody();
        $this->assertStringContainsString('cb-hero__card--page', $html);
        $this->assertStringContainsString('<h1 class="cb-display-l cb-pre">Own a territory</h1>', $html);
        $this->assertStringNotContainsString('class="cb-hbar"', $html);
    }

    public function testArticleBodyRendersSanitised(): void
    {
        $this->addPage(['type' => 'article', 'title' => 'Market note', 'slug' => 'market-note', 'excerpt' => 'Short summary',
            'body' => '<p>Safe <em>text</em></p><script>alert(1)</script><p onclick="alert(2)">Click</p><a href="javascript:alert(3)">bad</a>']);
        $html = $this->get('market-note')->getBody();
        $this->assertStringContainsString('Market note', $html);
        $this->assertStringContainsString('Short summary', $html);
        $this->assertStringContainsString('<em>text</em>', $html);
        $this->assertStringContainsString('cb-prose', $html);
        $this->assertStringNotContainsString('<script>alert(1)', $html);
        $this->assertStringNotContainsString('onclick', $html);
        $this->assertStringNotContainsString('javascript:', $html);
    }

    public function testXssInBlocksItemsMenusAndSettingsIsEscaped(): void
    {
        $hero = $this->homeBlockId('hero_slider');
        $this->db->table('cms_blocks')->where('id', $hero)->update(['settings' => json_encode(['video_label' => self::XSS, 'video_url' => 'javascript:alert(1)'])]);
        $this->db->table('cms_block_items')->where('block_id', $hero)->where('item_group', 'slide')->where('sort_order', 10)
            ->update(['title' => self::XSS, 'cta_label' => 'Go', 'cta_url' => 'javascript:alert(1)', 'image' => 'javascript:alert(1)']);
        $mission = $this->homeBlockId('mission');
        $this->db->table('cms_blocks')->where('id', $mission)->update(['settings' => json_encode(['statement' => self::XSS, 'cta_label' => 'x', 'cta_url' => ' JaVaScRiPt:alert(1)'])]);
        $this->db->table('cms_menu_items')->where('label', 'About Us')->update(['label' => self::XSS, 'url' => 'javascript:alert(1)']);
        $this->db->table('cms_menu_items')->where('label', 'Contact')->update(['label' => self::XSS]);
        $this->db->table('cms_settings')->where('setting_key', 'footer_copyright')->update(['value' => self::XSS]);
        $this->db->table('cms_settings')->where('setting_key', 'nav_cta_url')->update(['value' => 'javascript:alert(1)']);
        $this->db->table('cms_settings')->where('setting_key', 'footer_email')->update(['value' => self::XSS]);
        $id = $this->addPage(['title' => self::XSS, 'slug' => 'xss-page', 'meta_description' => self::XSS]);
        $this->addBlock($id, 'rich_text', ['title' => self::XSS, 'eyebrow' => self::XSS, 'body' => self::XSS . '<a href="javascript:alert(1)">x</a>']);
        Cms::clearCache();

        foreach (['/', 'xss-page'] as $url) {
            $html = $this->get($url)->getBody();
            $this->assertStringNotContainsString('<script>alert(1)', $html, $url);
            $this->assertStringNotContainsString('<img src=x', $html, $url);
            $this->assertStringNotContainsStringIgnoringCase('javascript:', $html, $url);
            $this->assertStringContainsString('&lt;script&gt;alert(1)&lt;/script&gt;', $html, $url);
        }
        $home = $this->get('/')->getBody();
        $this->assertStringNotContainsString('Franchise Enquiry', $home); // CTA with unsafe link is hidden
    }
}
