<?php

use App\Database\Seeds\CmsSeeder;
use App\Libraries\Cms\Cms;
use App\Models\CmsBlockItemsModel;
use App\Models\CmsBlocksModel;
use App\Models\CmsPagesModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\Security\Exceptions\SecurityException;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Config\Services;

/**
 * Admin > CMS > Pages / Articles, blocks and block items (App\Controllers\Admin\CmsPages).
 *
 * Run with an own table prefix: env 'database.tests.DBPrefix=tb_' vendor/bin/phpunit tests/unit/CmsPagesTest.php
 *
 * @internal
 */
final class CmsPagesTest extends CIUnitTestCase
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
        $pfx = getenv('database.tests.DBPrefix'); // .env would otherwise win over the per-run prefix
        if (is_string($pfx) && preg_match('/^\w+$/', $pfx)) {
            // Windows PHP drops dotted env names from $_SERVER/$_ENV and .env then wins: re-apply it
            $_SERVER['database.tests.DBPrefix'] = $_ENV['database.tests.DBPrefix'] = $pfx;
            \CodeIgniter\Config\Factories::reset('config');
        }
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

    private function admin(): self
    {
        return $this->withSession(['admin_user' => 'tester']);
    }

    private function adminPost(string $url, array $data = [])
    {
        array_walk_recursive($data, static function (&$v): void {
            $v = (string) $v; // real requests only carry strings
        });

        return $this->admin()->post($url, $data + [csrf_token() => csrf_hash()]);
    }

    private function assert404(callable $fn): void
    {
        try {
            $r = $fn();
            $this->assertSame(404, $r->response()->getStatusCode());
        } catch (PageNotFoundException) {
            $this->assertTrue(true);
        }
    }

    private function pageData(array $over = []): array
    {
        return $over + [
            'title' => 'Our Story', 'slug' => '', 'type' => 'page', 'nav_key' => '', 'excerpt' => '', 'body' => '',
            'status' => '1', 'published_at' => '2026-01-01T10:00', 'image_alt' => '', 'meta_title' => '',
            'meta_description' => '', 'meta_keywords' => '',
        ];
    }

    private function page(string $slug): ?array
    {
        return (new CmsPagesModel())->where('slug', $slug)->first();
    }

    private function home(): array
    {
        return (new CmsPagesModel())->where('is_home', 1)->first();
    }

    private function makePage(string $slug = 'test-page'): int
    {
        return (int) (new CmsPagesModel())->insert(['type' => 'page', 'title' => 'T ' . $slug, 'slug' => $slug, 'status' => 1, 'published_at' => '2026-01-01 00:00:00']);
    }

    private function makeBlock(int $pageId, string $type, int $sort = 10, array $settings = []): int
    {
        return (int) (new CmsBlocksModel())->insert(['page_id' => $pageId, 'type' => $type, 'settings' => json_encode($settings), 'sort_order' => $sort, 'status' => 1]);
    }

    public function testLoginRequired(): void
    {
        $pid = $this->makePage();
        $bid = $this->makeBlock($pid, 'expertise');
        foreach (['admin/cms/pages', 'admin/cms/pages/add', 'admin/cms/pages/edit/' . $pid, "admin/cms/pages/{$pid}/blocks/{$bid}"] as $url) {
            $r = $this->withSession([])->get($url);
            $r->assertRedirect();
            $this->assertStringEndsWith('admin/login', $r->getRedirectUrl(), $url);
        }
        $r = $this->withSession([])->post('admin/cms/pages/delete/' . $pid, [csrf_token() => csrf_hash()]);
        $r->assertRedirect();
        $this->assertNotNull((new CmsPagesModel())->find($pid));
    }

    public function testCsrfRequired(): void
    {
        $pid = $this->makePage();
        try {
            $r = $this->admin()->post('admin/cms/pages/delete/' . $pid, []);
            $this->assertContains($r->response()->getStatusCode(), [403, 303, 302]);
        } catch (SecurityException) {
            // expected
        }
        $this->assertNotNull((new CmsPagesModel())->find($pid));
    }

    public function testListAndForms(): void
    {
        $this->makePage('listed-page');
        $r = $this->admin()->get('admin/cms/pages');
        $r->assertStatus(200);
        $r->assertSee('listed-page');
        $this->admin()->get('admin/cms/pages?type=article')->assertStatus(200);
        $this->admin()->get('admin/cms/pages/add?type=article')->assertStatus(200);
        $this->admin()->get('admin/cms/pages/edit/' . $this->home()['id'])->assertStatus(200);
    }

    public function testPageCrudAndSlugRules(): void
    {
        $this->adminPost('admin/cms/pages/add', $this->pageData())->assertRedirect();
        $p = $this->page('our-story');
        $this->assertNotNull($p);
        $this->assertSame(0, (int) $p['is_home']);

        // duplicate title gets a counter; duplicate typed slug is rejected
        $this->adminPost('admin/cms/pages/add', $this->pageData())->assertRedirect();
        $this->assertNotNull($this->page('our-story-2'));
        $r = $this->adminPost('admin/cms/pages/add', $this->pageData(['title' => 'Other', 'slug' => 'our-story']));
        $r->assertStatus(200);
        $r->assertSee('already used');
        $this->assertNull($this->page('other'));

        // reserved slugs (fixed list and public routes)
        foreach (['admin', 'blog', 'home', 'uploads', 'corporate-team', 'partner-with-us'] as $slug) {
            $r = $this->adminPost('admin/cms/pages/add', $this->pageData(['title' => 'Typed Title', 'slug' => $slug]));
            $r->assertStatus(200);
            $r->assertSee('reserved');
            $r->assertSee('Typed Title'); // typed input kept
        }
        // title "Home" -> auto slug avoids reserved
        $this->adminPost('admin/cms/pages/add', $this->pageData(['title' => 'Home']))->assertRedirect();
        $this->assertNotNull($this->page('home-2'));

        // edit
        $this->adminPost('admin/cms/pages/edit/' . $p['id'], $this->pageData(['title' => 'Renamed', 'slug' => 'renamed']))->assertRedirect();
        $this->assertSame('Renamed', (new CmsPagesModel())->find($p['id'])['title']);

        // home: slug fixed, cannot unpublish or delete
        $home = $this->home();
        $this->adminPost('admin/cms/pages/edit/' . $home['id'], $this->pageData(['title' => 'Home X', 'slug' => 'new-home']))->assertRedirect();
        $this->assertSame('home', (new CmsPagesModel())->find($home['id'])['slug']);
        $this->adminPost('admin/cms/pages/edit/' . $home['id'], $this->pageData(['title' => 'Home', 'status' => '0']))->assertSee('cannot be unpublished');
        $this->adminPost('admin/cms/pages/status/' . $home['id'] . '/0');
        $this->assertSame(1, (int) (new CmsPagesModel())->find($home['id'])['status']);
        $this->adminPost('admin/cms/pages/delete/' . $home['id']);
        $this->assertNotNull((new CmsPagesModel())->find($home['id']));

        // delete + status
        $this->adminPost('admin/cms/pages/status/' . $p['id'] . '/0')->assertRedirect();
        $this->assertSame(0, (int) (new CmsPagesModel())->find($p['id'])['status']);
        $bid = $this->makeBlock((int) $p['id'], 'expertise');
        $this->adminPost('admin/cms/pages/delete/' . $p['id'])->assertRedirect();
        $this->assertNull((new CmsPagesModel())->find($p['id']));
        $this->assertNull((new CmsBlocksModel())->find($bid));
    }

    public function testArticleBodySanitisedAndRequired(): void
    {
        $r = $this->adminPost('admin/cms/pages/add', $this->pageData(['title' => 'News One', 'type' => 'article', 'body' => '']));
        $r->assertStatus(200);
        $this->assertNull($this->page('news-one'));

        $this->adminPost('admin/cms/pages/add', $this->pageData([
            'title' => 'News One', 'type' => 'article',
            'body'  => '<p>Hello</p><script>alert(1)</script><img src="/a.jpg" onerror="x()">',
        ]))->assertRedirect();
        $a = $this->page('news-one');
        $this->assertSame('article', $a['type']);
        $this->assertStringContainsString('<p>Hello</p>', $a['body']);
        $this->assertStringNotContainsString('script', $a['body']);
        $this->assertStringNotContainsString('onerror', $a['body']);
    }

    public function testBlockAddAndSettings(): void
    {
        $pid = $this->makePage();
        $this->adminPost("admin/cms/pages/{$pid}/blocks/add", ['type' => 'rich_text'])->assertRedirect();
        $b = (new CmsBlocksModel())->where('page_id', $pid)->first();
        $this->assertSame('rich_text', $b['type']);
        $this->admin()->get("admin/cms/pages/{$pid}/blocks/{$b['id']}")->assertStatus(200);

        $this->adminPost("admin/cms/pages/{$pid}/blocks/{$b['id']}", [
            'label'    => 'Intro',
            'status'   => '1',
            'settings' => ['title' => 'Hi', 'body' => '<p>Ok</p><script>bad()</script><a href="javascript:x()">l</a>', 'align' => 'center', 'evil' => 'x'],
        ])->assertRedirect();
        $b = (new CmsBlocksModel())->find($b['id']);
        $s = json_decode($b['settings'], true);
        $this->assertSame(['eyebrow', 'title', 'body', 'align'], array_keys($s));
        $this->assertStringNotContainsString('script', $s['body']);
        $this->assertStringNotContainsString('javascript', $s['body']);
        $this->assertSame('center', $s['align']);
        $this->assertSame('Intro', $b['label']);

        // javascript: url rejected
        $mid = $this->makeBlock($pid, 'mission', 50, ['statement' => 'Old']);
        $r   = $this->adminPost("admin/cms/pages/{$pid}/blocks/{$mid}", ['label' => '', 'status' => '1', 'settings' => ['statement' => 'New', 'cta_url' => 'javascript:alert(1)']]);
        $r->assertStatus(200);
        $this->assertSame('Old', json_decode((new CmsBlocksModel())->find($mid)['settings'], true)['statement']);

        // unknown type
        $this->adminPost("admin/cms/pages/{$pid}/blocks/add", ['type' => 'nope'])->assertRedirect();
        $this->assertSame(0, (new CmsBlocksModel())->where('type', 'nope')->countAllResults());

        // Cms::page reflects changes
        Cms::clearCache();
        $data = Cms::page('test-page');
        $rt   = array_values(array_filter($data['blocks'], static fn ($x) => $x['type'] === 'rich_text'));
        $this->assertSame('Hi', $rt[0]['settings']['title']);
    }

    public function testHeroFirstRule(): void
    {
        $pid = $this->makePage();
        $other = $this->makeBlock($pid, 'expertise', 10);
        $this->adminPost("admin/cms/pages/{$pid}/blocks/add", ['type' => 'page_hero'])->assertRedirect();
        $hero = (new CmsBlocksModel())->where('page_id', $pid)->where('type', 'page_hero')->first();
        $this->assertLessThan(10, (int) $hero['sort_order']);

        // second hero refused
        $this->adminPost("admin/cms/pages/{$pid}/blocks/add", ['type' => 'hero_slider'])->assertRedirect();
        $this->assertSame(0, (new CmsBlocksModel())->where('page_id', $pid)->where('type', 'hero_slider')->countAllResults());

        // sort that puts hero after another block refused
        $this->adminPost("admin/cms/pages/{$pid}/blocks/sort", ['order' => [$hero['id'] => 50, $other => 20]])->assertRedirect();
        $this->assertSame((int) $hero['sort_order'], (int) (new CmsBlocksModel())->find($hero['id'])['sort_order']);
        $this->adminPost("admin/cms/pages/{$pid}/blocks/sort", ['order' => [$hero['id'] => 1, $other => 20]])->assertRedirect();
        $this->assertSame(20, (int) (new CmsBlocksModel())->find($other)['sort_order']);
    }

    public function testItemsCrudMaxAndRequiredImage(): void
    {
        $pid = $this->makePage();
        $bid = $this->makeBlock($pid, 'expertise', 10, ['title' => 'X']);
        $items = new CmsBlockItemsModel();

        // required image: photo group without upload fails
        $r = $this->adminPost("admin/cms/pages/{$pid}/blocks/{$bid}/items/photo/add", ['image_alt' => 'A', 'status' => '1']);
        $r->assertStatus(200);
        $r->assertSee('is required');
        $this->assertSame(0, $items->where('block_id', $bid)->countAllResults());

        // milestone add / required field / javascript url not applicable
        $this->admin()->get("admin/cms/pages/{$pid}/blocks/{$bid}/items/milestone/add")->assertStatus(200);
        $this->adminPost("admin/cms/pages/{$pid}/blocks/{$bid}/items/milestone/add", ['title' => '', 'subtitle' => 'x'])->assertStatus(200);
        for ($i = 1; $i <= 6; $i++) {
            $this->adminPost("admin/cms/pages/{$pid}/blocks/{$bid}/items/milestone/add", ['title' => '20' . $i, 'subtitle' => 'Year ' . $i, 'status' => '1', 'sort_order' => ''])->assertRedirect();
        }
        $this->assertSame(6, $items->where('block_id', $bid)->where('item_group', 'milestone')->countAllResults());
        // max reached
        $this->adminPost("admin/cms/pages/{$pid}/blocks/{$bid}/items/milestone/add", ['title' => '2099', 'subtitle' => 'Too many'])->assertRedirect();
        $this->assertSame(6, $items->where('block_id', $bid)->where('item_group', 'milestone')->countAllResults());
        $r = $this->admin()->get("admin/cms/pages/{$pid}/blocks/{$bid}");
        $r->assertStatus(200);
        $r->assertSee('disabled title="Maximum reached"');

        $first = $items->where('block_id', $bid)->where('title', '201')->first();
        $this->adminPost("admin/cms/pages/{$pid}/blocks/{$bid}/items/milestone/edit/{$first['id']}", ['title' => '1999', 'subtitle' => 'Edited', 'status' => '1', 'sort_order' => '5'])->assertRedirect();
        $this->assertSame('Edited', $items->find($first['id'])['subtitle']);
        $this->adminPost("admin/cms/pages/{$pid}/blocks/{$bid}/items/milestone/status/{$first['id']}/0")->assertRedirect();
        $this->assertSame(0, (int) $items->find($first['id'])['status']);
        $this->adminPost("admin/cms/pages/{$pid}/blocks/{$bid}/items/milestone/sort", ['order' => [$first['id'] => 99]])->assertRedirect();
        $this->assertSame(99, (int) $items->find($first['id'])['sort_order']);
        $this->adminPost("admin/cms/pages/{$pid}/blocks/{$bid}/items/milestone/delete/{$first['id']}")->assertRedirect();
        $this->assertNull($items->find($first['id']));

        // javascript: url rejected on an item
        $cid = $this->makeBlock($pid, 'city_slider', 20, ['title' => 'Cities']);
        $r   = $this->adminPost("admin/cms/pages/{$pid}/blocks/{$cid}/items/city/add", ['title' => 'Pune', 'cta_url' => 'javascript:alert(1)']);
        $r->assertStatus(200);
        $this->assertSame(0, $items->where('block_id', $cid)->countAllResults());

        // Cms::page reflects items (visible only)
        Cms::clearCache();
        $data = Cms::page('test-page');
        $exp  = array_values(array_filter($data['blocks'], static fn ($x) => $x['type'] === 'expertise'))[0];
        $this->assertCount(5, $exp['items']['milestone']);
    }

    public function testOwnership404(): void
    {
        $p1 = $this->makePage('page-one');
        $p2 = $this->makePage('page-two');
        $b1 = $this->makeBlock($p1, 'expertise', 10, ['title' => 'X']);
        $b2 = $this->makeBlock($p2, 'expertise', 10, ['title' => 'Y']);
        $it = (int) (new CmsBlockItemsModel())->insert(['block_id' => $b2, 'item_group' => 'milestone', 'title' => '1', 'subtitle' => 's', 'sort_order' => 1, 'status' => 1]);

        $this->assert404(fn () => $this->admin()->get("admin/cms/pages/{$p1}/blocks/{$b2}"));
        $this->assert404(fn () => $this->adminPost("admin/cms/pages/{$p1}/blocks/{$b2}/delete"));
        $this->assertNotNull((new CmsBlocksModel())->find($b2));
        $this->assert404(fn () => $this->admin()->get("admin/cms/pages/{$p1}/blocks/{$b1}/items/milestone/edit/{$it}"));
        $this->assert404(fn () => $this->adminPost("admin/cms/pages/{$p1}/blocks/{$b1}/items/milestone/delete/{$it}"));
        $this->assertNotNull((new CmsBlockItemsModel())->find($it));
        $this->assert404(fn () => $this->admin()->get("admin/cms/pages/{$p1}/blocks/{$b1}/items/nogroup/add"));
        $this->assert404(fn () => $this->admin()->get("admin/cms/pages/{$p2}/blocks/{$b2}/items/slide/add"));
        $bad = $this->makeBlock($p1, 'unknown_type', 30);
        $this->assert404(fn () => $this->admin()->get("admin/cms/pages/{$p1}/blocks/{$bad}"));
        $this->assert404(fn () => $this->admin()->get('admin/cms/pages/edit/999999'));
    }
}
