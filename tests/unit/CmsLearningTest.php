<?php

use App\Database\Seeds\CmsSeeder;
use App\Database\Seeds\LearningPageSeeder;
use App\Libraries\Cms\BlockTypes;
use App\Libraries\Cms\Cms;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * Learning & Development as a CMS page (Figma 83:58): seeder, overview / numbered_cards /
 * event_calendar / album_gallery blocks, expertise logo + quote, the "no stale dates" rule,
 * redirect and escaping. The legacy events/gallery tables are not in the test DB, so the
 * calendar must show its "next batches" message and the gallery must render nothing.
 *
 * @internal
 */
final class CmsLearningTest extends CIUnitTestCase
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
        $this->seed(LearningPageSeeder::class);
        Cms::clearCache();
    }

    public function testPageRendersDesignSections(): void
    {
        $r = $this->get('learning-development');
        $r->assertOK();
        foreach (['If You Learn, You Last.', 'View Training Calendar', 'href="#calendar"', 'Overview', 'Franchise Owners', 'Managers',
            'Coldwell Banker University', 'cb-quote-rule', 'logo-cbu', '30+ yrs', 'Training Programs', 'Capital Gains', 'Primary',
            'Training Calendar', 'id="calendar"', 'New training batches will be announced soon.', 'Register Interest'] as $t) {
            $r->assertSee($t);
        }
        // no events table / no upcoming events: never placeholder or past dates
        $r->assertDontSee('[DD]');
        $r->assertDontSee('2017');
        // gallery tables missing in the test DB: section omitted, page still renders
        $r->assertDontSee('Photo Gallery');
        $this->assertMatchesRegularExpression('#cb-menu__btn is-current#', $r->getBody(), 'What We Do highlighted');
        foreach (['overview', 'numbered_cards', 'event_calendar', 'album_gallery'] as $t) {
            $this->assertTrue(BlockTypes::has($t), $t);
        }
    }

    public function testUnderscoreUrlRedirectsAndSeederIsIdempotent(): void
    {
        $this->get('learning_development')->assertRedirectTo(site_url('learning-development'));
        $this->seed(LearningPageSeeder::class);
        $this->assertSame(1, db_connect()->table('cms_pages')->where('slug', 'learning-development')->countAllResults());
    }

    public function testInputIsEscaped(): void
    {
        $db  = db_connect();
        $pid = (int) $db->table('cms_pages')->select('id')->where('slug', 'learning-development')->get()->getRow('id');
        foreach ($db->table('cms_blocks')->where('page_id', $pid)->whereIn('type', ['overview', 'expertise', 'numbered_cards'])->get()->getResultArray() as $b) {
            $set = json_decode($b['settings'], true);
            foreach (['chips', 'quote', 'statement', 'title'] as $k) {
                if (array_key_exists($k, $set)) {
                    $set[$k] = "<script>alert(1)</script>\n\"><img src=x onerror=alert(1)>";
                }
            }
            $db->table('cms_blocks')->where('id', $b['id'])->update(['settings' => json_encode($set)]);
        }
        $nc = (int) $db->table('cms_blocks')->select('id')->where(['page_id' => $pid, 'type' => 'numbered_cards'])->get()->getRow('id');
        $db->table('cms_block_items')->where('block_id', $nc)->update(['kicker' => '<svg onload=alert(1)>, ok']);
        Cms::clearCache();

        $body = $this->get('learning-development')->getBody();
        $this->assertStringNotContainsString('<script>alert(1)', $body);
        $this->assertStringNotContainsString('<img src=x', $body);
        $this->assertStringNotContainsString('<svg onload', $body);
    }
}
