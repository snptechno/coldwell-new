<?php

use App\Database\Seeds\AboutPageSeeder;
use App\Database\Seeds\CmsSeeder;
use App\Libraries\Cms\BlockTypes;
use App\Libraries\Cms\Cms;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * About Us as a CMS page (Figma 73:58): seeder, new block types (stats_panel, leaders,
 * feature_cards, values), routing, hidden unverified figures and payload escaping.
 *
 * @internal
 */
final class CmsAboutTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $migrate     = true;
    protected $migrateOnce = false;
    protected $refresh     = true;
    protected $namespace   = 'App';
    protected $seed        = CmsSeeder::class;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AboutPageSeeder::class);
        Cms::clearCache();
    }

    public function testAboutRendersEveryDesignSection(): void
    {
        $r = $this->get('about-coldwell-banker');
        $r->assertOK();
        foreach (['About the Coldwell Banker® Brand', 'Our vision defines us', 'A Story of Leadership', 'About Coldwell Banker India',
            'Our Leadership', 'Ramnik Chopra', 'Managing Director', 'Diana Joyal', 'Why Coldwell Banker', 'Generation Blue',
            'Launch a national website', 'Our Values', 'Customer Focus', 'Let’s Build a Brighter'] as $text) {
            $r->assertSee($text);
        }
        foreach (['cb-purpose', 'cb-figrow', 'cb-split__panel', 'cb-icard', 'cb-leader-feat', 'cb-leader-grid', 'cb-features__panel', 'cb-checklist', 'cb-values__row', 'cb-vcard'] as $cls) {
            $r->assertSee($cls);
        }
        // real, verified figures shown; placeholders and unverified figures hidden
        $r->assertSee('Franchise offices');
        $r->assertSee('>29<');
        $r->assertDontSee('[XX]');
        $r->assertDontSee('150+');
        // About Us is the active menu item
        $this->assertMatchesRegularExpression('#<a[^>]+aria-current="page"[^>]*>About Us</a>#', $r->getBody());
    }

    public function testUnderscoreUrlRedirectsAndSeederIsIdempotent(): void
    {
        $r = $this->get('about_coldwell_banker');
        $r->assertRedirectTo(site_url('about-coldwell-banker'));
        $this->seed(AboutPageSeeder::class);
        $this->assertSame(1, db_connect()->table('cms_pages')->where('slug', 'about-coldwell-banker')->countAllResults());
    }

    public function testMenuAboutLinkPointsAtTheCmsPage(): void
    {
        $about = array_values(array_filter(Cms::menu('main'), static fn ($i) => $i['label'] === 'About Us'))[0];
        $this->assertSame(site_url('about-coldwell-banker'), $about['href']);
    }

    public function testNewBlockTypesAreAvailableInAdmin(): void
    {
        foreach (['stats_panel', 'leaders', 'feature_cards', 'values'] as $type) {
            $this->assertTrue(BlockTypes::has($type));
        }
        $pageId = (int) db_connect()->table('cms_pages')->select('id')->where('slug', 'about-coldwell-banker')->get()->getRow('id');
        $r      = $this->withSession(['admin_user' => 'tester'])->get('admin/cms/pages/edit/' . $pageId);
        $r->assertOK();
        $r->assertSee('Leadership');
        $r->assertSee('Icon cards (values)');
    }

    public function testPayloadsInNewBlocksAreEscaped(): void
    {
        $db    = db_connect();
        $pid   = (int) $db->table('cms_pages')->select('id')->where('slug', 'about-coldwell-banker')->get()->getRow('id');
        $xss   = '"><img src=x onerror=alert(1)><script>alert(1)</script>';
        foreach ($db->table('cms_blocks')->where('page_id', $pid)->get()->getResultArray() as $b) {
            $set = json_decode($b['settings'], true);
            foreach ($set as $k => $v) {
                $set[$k] = in_array($k, ['layout', 'image'], true) ? $v : (str_ends_with($k, 'url') ? 'javascript:alert(1)' : $xss);
            }
            $db->table('cms_blocks')->where('id', $b['id'])->update(['settings' => json_encode($set)]);
        }
        $db->table('cms_block_items')->whereIn('block_id', $db->table('cms_blocks')->select('id')->where('page_id', $pid))
            ->update(['title' => $xss, 'subtitle' => $xss, 'body' => $xss . "\n- " . $xss, 'image_alt' => $xss, 'kicker' => '"><svg onload=alert(1)>', 'image' => '../../.env']);
        Cms::clearCache();

        $body = $this->get('about-coldwell-banker')->getBody();
        $this->assertStringNotContainsString('<script>alert(1)', $body);
        $this->assertStringNotContainsString('<img src=x', $body);
        $this->assertStringNotContainsString('<svg onload', $body);
        $this->assertStringNotContainsString('javascript:alert', $body);
        $this->assertStringNotContainsString('.env', $body);
        $this->assertStringContainsString('&lt;script&gt;', $body);
    }
}
