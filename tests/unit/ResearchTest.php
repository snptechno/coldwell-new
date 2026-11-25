<?php

use App\Database\Seeds\ResearchPageSeeder;
use App\Libraries\Cms\BlockTypes;
use App\Libraries\Cms\Cms;
use App\Models\ResearchModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

final class ResearchTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $migrate     = true;
    protected $migrateOnce = false;
    protected $refresh     = true;
    protected $namespace   = 'App';
    protected $seed        = ResearchPageSeeder::class;

    protected function setUp(): void
    {
        parent::setUp();
        $db = db_connect();
        if (! $db->tableExists('research')) {
            $forge = \Config\Database::forge();
            $forge->addField([
                'id'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'type'         => ['type' => 'VARCHAR', 'constraint' => 100],
                'title'        => ['type' => 'VARCHAR', 'constraint' => 255],
                'description'  => ['type' => 'TEXT', 'null' => true],
                'image_name'   => ['type' => 'VARCHAR', 'constraint' => 255, 'default' => ''],
                'pdf_name'     => ['type' => 'VARCHAR', 'constraint' => 255, 'default' => ''],
                'is_active'    => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
                'created_date' => ['type' => 'DATETIME'],
            ]);
            $forge->addKey('id', true);
            $forge->createTable('research', true);
        }
        $this->seed(ResearchPageSeeder::class);
        Cms::clearCache();
    }

    public function testCmsBlockTypesRegisteredForAdmin(): void
    {
        foreach (['research_tabs', 'featured_report', 'research_grid', 'approach_panel'] as $type) {
            $this->assertTrue(BlockTypes::has($type), "Block type {$type} must be registered for CMS admin.");
        }
    }

    public function testPublicResearchPageRendersFigmaBlocksAndSecurityHeaders(): void
    {
        model(ResearchModel::class)->createItem([
            'type'         => 'Project Reports',
            'title'        => 'Mumbai Luxury Report <script>alert(1)</script>',
            'description'  => 'In-depth market overview for Mumbai buyers.',
            'image_name'   => 'sample.jpg',
            'pdf_name'     => 'sample.pdf',
            'is_active'    => 1,
            'created_date' => '2026-10-01 10:00:00',
        ]);

        $r = $this->get('research');
        $r->assertOK();
        $r->assertHeader('X-Content-Type-Options', 'nosniff');
        $r->assertSee('Market Research &amp; Project Reports');
        $r->assertSee('Project Reports');
        $r->assertSee('Area Insights');
        $r->assertSee('All Project Reports');
        $r->assertSee('OUR APPROACH');
        $r->assertSee('Talk to an Advisor');
        $r->assertSee('Download PDF');
        $r->assertDontSee('<script>alert(1)</script>');
    }

    public function testLoadMoreResearchValidationAndEscaping(): void
    {
        $model = model(ResearchModel::class);
        for ($i = 1; $i <= 3; $i++) {
            $model->createItem([
                'type'         => 'Project Reports',
                'title'        => "Report #{$i} <b>Bold</b>",
                'description'  => "Summary {$i}",
                'image_name'   => "img{$i}.jpg",
                'pdf_name'     => "doc{$i}.pdf",
                'is_active'    => 1,
                'created_date' => '2026-10-01 10:00:00',
            ]);
        }

        // Invalid type rejected
        $bad = $this->post('research/load-more', ['type' => 'InvalidType', 'offset' => 0, 'limit' => 6]);
        $bad->assertStatus(404);

        // Negative offset rejected
        $neg = $this->post('research/load-more', ['type' => 'Project Reports', 'offset' => -1, 'limit' => 6]);
        $neg->assertStatus(404);

        // Valid request returns escaped card HTML
        $ok = $this->post('research/load-more', [
            'type'      => 'Project Reports',
            'offset'    => 1,
            'limit'     => 2,
            'cta_label' => 'Download',
        ]);
        $ok->assertOK();
        $ok->assertSee('cb-rcard');
        $ok->assertSee('rel="noopener noreferrer"');
        $ok->assertDontSee('<b>Bold</b>');
    }
}
