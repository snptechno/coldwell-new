<?php

use App\Database\Seeds\FindingAHomePageSeeder;
use App\Libraries\Cms\BlockTypes;
use App\Libraries\Cms\Cms;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

final class FindingAHomeTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $migrate     = true;
    protected $migrateOnce = false;
    protected $refresh     = true;
    protected $namespace   = 'App';
    protected $seed        = FindingAHomePageSeeder::class;

    protected function setUp(): void
    {
        parent::setUp();
        $db = db_connect();
        foreach (['cb_india_videos', 'featured_videos', 'finding_home_videos', 'selling_home_videos', 'testimonial_videos'] as $tbl) {
            if (! $db->tableExists($tbl)) {
                $forge = \Config\Database::forge();
                $forge->addField([
                    'id'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                    'title'        => ['type' => 'VARCHAR', 'constraint' => 255],
                    'description'  => ['type' => 'TEXT', 'null' => true],
                    'video_url'    => ['type' => 'VARCHAR', 'constraint' => 255, 'default' => ''],
                    'is_active'    => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
                    'created_date' => ['type' => 'DATETIME', 'null' => true],
                ]);
                $forge->addKey('id', true);
                $forge->createTable($tbl, true);
            }
        }
        $db->table('finding_home_videos')->insert([
            'title'        => 'Homebuyer Tip <script>alert(1)</script>',
            'description'  => 'Guide for buyers',
            'video_url'    => 'https://www.youtube.com/embed/fTnb-KitX_Y',
            'is_active'    => 1,
            'created_date' => '2026-10-01 10:00:00',
        ]);
        $this->seed(FindingAHomePageSeeder::class);
        Cms::clearCache();
    }

    public function testCmsBlockTypesRegisteredForAdmin(): void
    {
        $this->assertTrue(BlockTypes::has('media_tabs'));
        $this->assertTrue(BlockTypes::has('video_hub'));
    }

    public function testFindingAHomePageRendersFigmaBlocksAndSecurityHeaders(): void
    {
        $r = $this->get('finding-a-home');
        $r->assertOK();
        $r->assertHeader('X-Content-Type-Options', 'nosniff');
        $r->assertSee('Stories in Motion');
        $r->assertSee('Videos');
        $r->assertSee('Finding a Home');
        $r->assertSee('More From CB India');
        $r->assertSee('rel="noopener noreferrer"');
        $r->assertDontSee('<script>alert(1)</script>');
    }

    public function testUnderscoreAliasRedirectsPermanently(): void
    {
        $r = $this->get('finding_a_home');
        $r->assertRedirectTo(site_url('finding-a-home'));
        $this->assertSame(301, $r->response()->getStatusCode());
    }
}
