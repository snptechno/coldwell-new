<?php

use App\Database\Seeds\GroupAPagesSeeder;
use App\Libraries\Cms\BlockTypes;
use App\Libraries\Cms\Cms;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

final class GroupAPagesTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $migrate     = true;
    protected $migrateOnce = false;
    protected $refresh     = true;
    protected $namespace   = 'App';
    protected $seed        = GroupAPagesSeeder::class;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(GroupAPagesSeeder::class);
        Cms::clearCache();
    }

    public function testGroupABlockTypesRegistered(): void
    {
        foreach (['legal_text', 'sitemap', 'faq', 'leaders', 'testimonials'] as $type) {
            $this->assertTrue(BlockTypes::has($type), "Block type {$type} must be registered.");
        }
    }

    public function testAllTenGroupAPagesRenderWithSecurityHeaders(): void
    {
        $slugs = [
            'corporate-team',
            'selling-a-home',
            'cb-training-program',
            'faq',
            'privacy-policy',
            'terms-and-conditions',
            'disclaimer',
            'refund',
            'site-map',
            'testimonials',
        ];

        foreach ($slugs as $slug) {
            $r = $this->get($slug);
            $r->assertOK();
            $r->assertHeader('X-Content-Type-Options', 'nosniff');
            $r->assertSee('cb-page');
        }
    }
}
