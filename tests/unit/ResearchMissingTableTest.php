<?php

use App\Database\Seeds\CmsSeeder;
use App\Database\Seeds\ResearchPageSeeder;
use App\Libraries\Cms\Cms;
use App\Models\ResearchModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * Regression (CI on PR #1): the research CMS blocks read the legacy `research` table, which has
 * no migration. On a clean database it does not exist, and the page must still render (empty
 * state) instead of throwing a DatabaseException — the same rule Cms\Offices follows.
 *
 * @internal
 */
final class ResearchMissingTableTest extends CIUnitTestCase
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
        $this->seed(ResearchPageSeeder::class);
        \Config\Database::forge($this->db)->dropTable('research', true); // test DB only
        Cms::clearCache();
    }

    public function testPublicReadDegradesToEmpty(): void
    {
        $this->assertSame([], model(ResearchModel::class)->activeByType('Project Reports'));
    }

    public function testResearchPageRendersWithoutTheTable(): void
    {
        $this->get('research')->assertOK();
    }
}
