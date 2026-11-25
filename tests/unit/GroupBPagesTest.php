<?php

use App\Database\Seeds\GroupBPagesSeeder;
use App\Libraries\Cms\BlockTypes;
use App\Libraries\Cms\Cms;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

final class GroupBPagesTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $migrate     = true;
    protected $migrateOnce = false;
    protected $refresh     = true;
    protected $namespace   = 'App';
    protected $seed        = GroupBPagesSeeder::class;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(GroupBPagesSeeder::class);
        Cms::clearCache();
    }

    public function testMediaArchiveBlockRegisteredForAdmin(): void
    {
        $this->assertTrue(BlockTypes::has('media_archive'));
        $this->assertTrue(BlockTypes::has('media_tabs'));
        $this->assertTrue(BlockTypes::has('video_hub'));
    }

    public function testAllEightGroupBPagesRenderWithSecurityHeaders(): void
    {
        $pages = [
            'industry-news'        => 'Industry News &amp; Market Pulse',
            'knowledge-series'     => 'Coldwell Banker Knowledge Series',
            'newsletter'           => 'Coldwell Banker India Newsletter',
            'awards'               => 'Awards &amp; Recognition',
            'cb-india-videos'      => 'Stories in Motion',
            'featured-videos'      => 'Featured Videos',
            'franchisees-openings' => 'Franchise Career Openings',
            'area-insights'        => 'Market Research &amp; Area Insights',
        ];

        foreach ($pages as $slug => $expectedHeading) {
            $r = $this->get($slug);
            $r->assertOK();
            $r->assertHeader('X-Content-Type-Options', 'nosniff');
            $r->assertSee($expectedHeading);
        }
    }

    public function testUnderscoreAliasesRedirect301(): void
    {
        $aliases = [
            'industry_news'        => 'industry-news',
            'knowledge_series'     => 'knowledge-series',
            'cb_india_videos'      => 'cb-india-videos',
            'featured_videos'      => 'featured-videos',
            'franchisees_openings' => 'franchisees-openings',
            'area_insights'        => 'area-insights',
        ];

        foreach ($aliases as $from => $to) {
            $r = $this->get($from);
            $r->assertRedirectTo(site_url($to));
            $this->assertSame(301, $r->response()->getStatusCode(), "Expected 301 for {$from}");
        }
    }
}
