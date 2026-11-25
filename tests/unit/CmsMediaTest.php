<?php

use App\Database\Seeds\CmsSeeder;
use App\Database\Seeds\MediaPageSeeder;
use App\Libraries\Cms\BlockTypes;
use App\Libraries\Cms\Cms;
use App\Libraries\Cms\Media;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * Media Center as a CMS page (Figma 79:58): seeder, automatic media blocks, title/date
 * parsing, YouTube link handling, graceful rendering when the legacy media tables are
 * missing (the test DB has only the App migrations) and the redirect.
 *
 * @internal
 */
final class CmsMediaTest extends CIUnitTestCase
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
        $this->seed(MediaPageSeeder::class);
        Cms::clearCache();
    }

    public function testPageRendersAndAutomaticBlocksDegradeGracefully(): void
    {
        $r = $this->get('media-center');
        $r->assertOK();
        $r->assertSee('Newsroom &amp; Insights');
        $r->assertSee('Let’s Build a Brighter');
        // no media tables in the test DB: automatic blocks render nothing instead of erroring
        $r->assertDontSee('Latest from the Newsroom');
        $r->assertDontSee('Awards &amp; Recognition');
        $this->assertMatchesRegularExpression('#<a[^>]+aria-current="page"[^>]*>Media</a>#', $r->getBody());
        foreach (['featured_news', 'news_feed', 'awards_list', 'video_gallery', 'archive_lists'] as $t) {
            $this->assertTrue(BlockTypes::has($t), $t);
        }
    }

    public function testUnderscoreUrlRedirectsAndSeederIsIdempotent(): void
    {
        $this->get('media_center')->assertRedirectTo(site_url('media-center'));
        $this->seed(MediaPageSeeder::class);
        $this->assertSame(1, db_connect()->table('cms_pages')->where('slug', 'media-center')->countAllResults());
    }

    public function testLegacyTitlesAreSplitIntoTitleSourceAndDate(): void
    {
        $p = Media::parseTitle('Web platforms for realty brokers gaining ground - Business Line - 03.09.2015', '2026-04-27 10:31:43');
        $this->assertSame('Web platforms for realty brokers gaining ground', $p['title']);
        $this->assertSame('Business Line', $p['source']);
        $this->assertSame('2015-09-03', date('Y-m-d', $p['ts']));

        $p = Media::parseTitle('Newsletter - 04.12.2015', '2017-03-02 14:18:25');
        $this->assertSame('Newsletter', $p['title']);
        $this->assertSame('2015-12-04', date('Y-m-d', $p['ts']));

        $p = Media::parseTitle('Kukreja Constructions <b>Joins</b> Hands', '2017-04-22 00:09:06');
        $this->assertSame('Kukreja Constructions Joins Hands', $p['title'], 'tags stripped');
        $this->assertSame('2017-04-22', date('Y-m-d', $p['ts']));
    }

    public function testYoutubeLinksOnlyAcceptRealVideoIds(): void
    {
        $v = Media::youtube('https://www.youtube.com/embed/jgaoqWq0fT0');
        $this->assertSame('https://www.youtube.com/watch?v=jgaoqWq0fT0', $v['watch']);
        $this->assertSame('https://i.ytimg.com/vi/jgaoqWq0fT0/hqdefault.jpg', $v['thumb']);
        $this->assertSame('https://www.youtube.com/watch?v=QxzGm8FLazc', Media::youtube('https://youtu.be/QxzGm8FLazc')['watch']);
        $this->assertSame('', Media::youtube('javascript:alert(1)')['watch']);
        $this->assertSame('', Media::youtube('https://evil.example/embed/"><script>')['watch']);
    }
}
