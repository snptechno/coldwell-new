<?php

use App\Database\Seeds\CmsSeeder;
use App\Libraries\Cms\Cms;
use App\Libraries\Cms\SiteSchema;
use App\Models\CmsMenuItemsModel;
use App\Models\CmsMenusModel;
use App\Models\CmsSettingsModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\Security\Exceptions\SecurityException;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Config\Services;

/**
 * Admin > CMS > Menus and Site settings (App\Controllers\Admin\CmsMenus, CmsSettings).
 *
 * Run with an own table prefix: env 'database.tests.DBPrefix=ta_' vendor/bin/phpunit tests/unit/CmsMenusTest.php
 * The CMS file cache is moved to a per-prefix folder so parallel runs cannot share cms_* entries.
 *
 * @internal
 */
final class CmsMenusTest extends CIUnitTestCase
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
        // .env's database.tests.DBPrefix wins over a shell env var on Windows (DotEnv fills $_ENV
        // first), so pin this file's own prefix here to stay isolated from parallel runs.
        $_ENV['database.tests.DBPrefix'] = 'ta_';
        \CodeIgniter\Config\Factories::reset('config');
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
        return $this->admin()->post($url, $data + [csrf_token() => csrf_hash()]);
    }

    /** A missing route/row is a 404: thrown in tests, or rendered as a 404 response. */
    private function assert404(callable $fn, string $msg = ''): void
    {
        try {
            $r = $fn();
        } catch (PageNotFoundException) {
            $this->addToAssertionCount(1);

            return;
        }
        $this->assertSame(404, $r->response()->getStatusCode(), $msg);
    }

    private function menuId(string $location): int
    {
        return (int) (new CmsMenusModel())->where('location', $location)->first()['id'];
    }

    private function itemByLabel(string $location, string $label): ?array
    {
        return (new CmsMenuItemsModel())->where('menu_id', $this->menuId($location))->where('label', $label)->first();
    }

    private function labels(string $location): array
    {
        return array_column(Cms::menu($location), 'label');
    }

    // ------------------------------------------------------------------

    public function testLoginRequiredForGetAndPost(): void
    {
        foreach (['admin/cms/menus', 'admin/cms/menus/main', 'admin/cms/menus/main/add', 'admin/cms/settings'] as $url) {
            $r = $this->withSession([])->get($url);
            $r->assertRedirect();
            $this->assertStringEndsWith('admin/login', $r->getRedirectUrl(), $url);
        }
        $about = $this->itemByLabel('main', 'About Us');
        $posts = [
            'admin/cms/menus/footer_legal/add'                   => ['label' => 'Hack', 'link_type' => 'url', 'url' => 'x', 'status' => '1'],
            'admin/cms/menus/main/delete/' . $about['id']        => [],
            'admin/cms/menus/main/status/' . $about['id'] . '/0' => [],
            'admin/cms/menus/main/sort'                          => ['order' => [$about['id'] => '999']],
            'admin/cms/settings'                                 => ['nav_cta_label' => 'Hacked'],
        ];
        foreach ($posts as $url => $data) {
            $r = $this->withSession([])->post($url, $data + [csrf_token() => csrf_hash()]);
            $r->assertRedirect();
            $this->assertStringEndsWith('admin/login', $r->getRedirectUrl(), $url);
        }
        $this->assertNull($this->itemByLabel('footer_legal', 'Hack'));
        $this->assertSame($about, $this->itemByLabel('main', 'About Us'));
        $this->assertSame('Franchise Enquiry', (new CmsSettingsModel())->map()['nav_cta_label']);
    }

    public function testPostWithoutCsrfTokenIsRejected(): void
    {
        $about = $this->itemByLabel('main', 'About Us');
        try {
            $r = $this->admin()->post('admin/cms/menus/main/delete/' . $about['id'], []);
            $this->assertContains($r->response()->getStatusCode(), [403, 303, 302]);
        } catch (SecurityException) {
            // expected in testing (Security::$redirect is off outside production)
        }
        $this->assertNotNull($this->itemByLabel('main', 'About Us'));
    }

    public function testIndexListsEveryLocation(): void
    {
        $r = $this->admin()->get('admin/cms/menus');
        $r->assertStatus(200);
        foreach (SiteSchema::MENUS as $loc => $def) {
            $r->assertSee($def['name']);
            $r->assertSee('admin/cms/menus/' . $loc);
        }
        $this->admin()->get('admin/cms/menus/main')->assertSee('What We Do');
        $this->admin()->get('admin/cms/menus/main')->assertSee('Save order');
    }

    public function testUnknownLocationAndForeignItemAre404(): void
    {
        $this->assert404(fn () => $this->admin()->get('admin/cms/menus/nope'));
        $this->assert404(fn () => $this->adminPost('admin/cms/menus/nope/add', ['label' => 'x', 'link_type' => 'url', 'url' => 'x', 'status' => '1']));
    }

    public function testItemOfAnotherMenuIs404OnEdit(): void
    {
        $privacy = $this->itemByLabel('footer_legal', 'Privacy Policy');
        $this->assert404(fn () => $this->admin()->get('admin/cms/menus/main/edit/' . $privacy['id']));
    }

    public function testItemOfAnotherMenuIs404OnPosts(): void
    {
        $privacy = $this->itemByLabel('footer_legal', 'Privacy Policy');
        foreach (['delete/' . $privacy['id'], 'status/' . $privacy['id'] . '/0', 'edit/' . $privacy['id']] as $action) {
            $this->assert404(fn () => $this->adminPost('admin/cms/menus/main/' . $action, ['label' => 'Changed', 'link_type' => 'url', 'url' => 'x', 'status' => '1']), 'expected 404 for ' . $action);
        }
        $this->assertSame($privacy, $this->itemByLabel('footer_legal', 'Privacy Policy'));

        // Sorting another menu's item through this menu is a no-op.
        $this->adminPost('admin/cms/menus/main/sort', ['order' => [$privacy['id'] => '999']])->assertRedirect();
        $this->assertSame($privacy['sort_order'], $this->itemByLabel('footer_legal', 'Privacy Policy')['sort_order']);
    }

    public function testAddEditHideSortDelete(): void
    {
        $this->assertNotContains('Cookies', $this->labels('footer_legal'));

        $r = $this->adminPost('admin/cms/menus/footer_legal/add', [
            'label' => '  Cookies  ', 'link_type' => 'url', 'url' => 'cookie-policy', 'active_keys' => ' cookies , ,legal ', 'status' => '1',
        ]);
        $r->assertRedirect();
        $this->assertStringEndsWith('admin/cms/menus/footer_legal', $r->getRedirectUrl());
        $row = $this->itemByLabel('footer_legal', 'Cookies');
        $this->assertNotNull($row);
        $this->assertSame('cookie-policy', $row['url']);
        $this->assertNull($row['page_id']);
        $this->assertSame('cookies,legal', $row['active_keys']);
        $this->assertSame(40, (int) $row['sort_order'], 'auto sort puts it last');
        $legal = Cms::menu('footer_legal');
        $this->assertSame('Cookies', end($legal)['label'], 'Cms::menu sees the new item (cache cleared)');

        // Edit: link to the home page instead.
        $homeId = (int) db_connect()->table('cms_pages')->where('is_home', 1)->get()->getRow('id');
        $this->adminPost('admin/cms/menus/footer_legal/edit/' . $row['id'], [
            'label' => 'Cookie notice', 'link_type' => 'page', 'page_id' => (string) $homeId, 'url' => 'ignored', 'new_tab' => '1', 'sort_order' => '5', 'status' => '1',
        ])->assertRedirect();
        $row = (new CmsMenuItemsModel())->find($row['id']);
        $this->assertSame('Cookie notice', $row['label']);
        $this->assertSame($homeId, (int) $row['page_id']);
        $this->assertNull($row['url']);
        $this->assertSame(1, (int) $row['new_tab']);
        $first = Cms::menu('footer_legal')[0];
        $this->assertSame('Cookie notice', $first['label']);
        $this->assertSame(site_url('/'), $first['href']);

        // Hide.
        $this->adminPost("admin/cms/menus/footer_legal/status/{$row['id']}/0")->assertRedirect();
        $this->assertNotContains('Cookie notice', $this->labels('footer_legal'));
        $this->adminPost("admin/cms/menus/footer_legal/status/{$row['id']}/1")->assertRedirect();
        $this->assertContains('Cookie notice', $this->labels('footer_legal'));

        // Sort: put Disclaimer first.
        $disc = $this->itemByLabel('footer_legal', 'Disclaimer');
        $this->adminPost('admin/cms/menus/footer_legal/sort', ['order' => [$disc['id'] => '1', $row['id'] => 'abc']])->assertRedirect();
        $this->assertSame('Disclaimer', $this->labels('footer_legal')[0]);
        $this->assertSame(5, (int) (new CmsMenuItemsModel())->find($row['id'])['sort_order'], 'non-numeric order is ignored');

        // Delete.
        $this->adminPost("admin/cms/menus/footer_legal/delete/{$row['id']}")->assertRedirect();
        $this->assertNull((new CmsMenuItemsModel())->find($row['id']));
        $this->assertNotContains('Cookie notice', $this->labels('footer_legal'));
    }

    public function testDeletingParentDeletesChildren(): void
    {
        $what = $this->itemByLabel('main', 'What We Do');
        $m    = new CmsMenuItemsModel();
        $this->assertSame(5, $m->where('parent_id', $what['id'])->countAllResults());
        $this->adminPost("admin/cms/menus/main/delete/{$what['id']}")->assertRedirect();
        $this->assertSame(0, $m->where('parent_id', $what['id'])->countAllResults());
        $this->assertNull($m->find($what['id']));
        $this->assertNotContains('What We Do', $this->labels('main'));
        $this->assertNotNull($this->itemByLabel('main', 'Franchise'));
    }

    public function testValidationErrorsKeepInput(): void
    {
        $r = $this->adminPost('admin/cms/menus/footer_legal/add', ['label' => '', 'link_type' => 'url', 'url' => 'typed-path', 'status' => '1']);
        $r->assertStatus(200);
        $r->assertSee('The Label field is required.');
        $r->assertSee('value="typed-path"', null);

        $r = $this->adminPost('admin/cms/menus/footer_legal/add', ['label' => str_repeat('a', 121), 'link_type' => 'url', 'url' => 'x', 'status' => '1']);
        $r->assertStatus(200);
        $this->assertNull($this->itemByLabel('footer_legal', str_repeat('a', 121)));

        $r = $this->adminPost('admin/cms/menus/footer_legal/add', ['label' => 'No page', 'link_type' => 'page', 'page_id' => '99999', 'status' => '1']);
        $r->assertStatus(200);
        $r->assertSee('Choose a CMS page.');
        $this->assertNull($this->itemByLabel('footer_legal', 'No page'));
    }

    public function testDangerousUrlsAreRejected(): void
    {
        foreach (['javascript:alert(1)', 'JaVaScRiPt:alert(1)', 'data:text/html,<script>alert(1)</script>', '//evil.example', "java\tscript:alert(1)", 'vbscript:x'] as $i => $url) {
            $r = $this->adminPost('admin/cms/menus/footer_legal/add', ['label' => 'Bad ' . $i, 'link_type' => 'url', 'url' => $url, 'status' => '1']);
            $r->assertStatus(200);
            $r->assertSee('Use a site path');
            $this->assertNull($this->itemByLabel('footer_legal', 'Bad ' . $i), $url);
        }
        // Escaped when echoed back.
        $r = $this->adminPost('admin/cms/menus/footer_legal/add', ['label' => '<script>alert(1)</script>', 'link_type' => 'url', 'url' => '"><img src=x onerror=alert(1)>', 'status' => '1']);
        $this->assertStringNotContainsString('<script>alert(1)</script>', $r->getBody());
        $this->assertStringNotContainsString('<img src=x', $r->getBody());
    }

    public function testParentRules(): void
    {
        $what  = $this->itemByLabel('main', 'What We Do');
        $about = $this->itemByLabel('main', 'About Us');
        $priv  = $this->itemByLabel('footer_legal', 'Privacy Policy');
        $res   = $this->itemByLabel('main', 'Research');

        // depth-1 location rejects a parent.
        $r = $this->adminPost('admin/cms/menus/footer_legal/add', ['label' => 'Child', 'link_type' => 'url', 'url' => 'x', 'parent_id' => (string) $priv['id'], 'status' => '1']);
        $r->assertStatus(200);
        $r->assertSee('This menu has no sub-items.');
        $this->assertNull($this->itemByLabel('footer_legal', 'Child'));

        // parent from another menu rejected.
        $r = $this->adminPost('admin/cms/menus/main/add', ['label' => 'Child', 'link_type' => 'url', 'url' => 'x', 'parent_id' => (string) $priv['id'], 'status' => '1']);
        $r->assertStatus(200);
        $r->assertSee('Choose a top-level item of this menu.');

        // a child cannot be a parent (one level only).
        $r = $this->adminPost('admin/cms/menus/main/add', ['label' => 'Child', 'link_type' => 'url', 'url' => 'x', 'parent_id' => (string) $res['id'], 'status' => '1']);
        $r->assertSee('Choose a top-level item of this menu.');

        // never itself.
        $r = $this->adminPost('admin/cms/menus/main/edit/' . $about['id'], ['label' => 'About Us', 'link_type' => 'url', 'url' => 'about-coldwell-banker', 'parent_id' => (string) $about['id'], 'status' => '1']);
        $r->assertSee('An item cannot be its own parent.');

        // an item with children stays top level.
        $r = $this->adminPost('admin/cms/menus/main/edit/' . $what['id'], ['label' => 'What We Do', 'link_type' => 'url', 'url' => 'x', 'parent_id' => (string) $about['id'], 'status' => '1']);
        $r->assertSee('This item has sub-items');
        $this->assertNull((new CmsMenuItemsModel())->find($what['id'])['parent_id']);

        // valid child: appears under About Us in Cms::menu().
        $this->adminPost('admin/cms/menus/main/add', ['label' => 'Our Team', 'link_type' => 'url', 'url' => 'corporate-team', 'parent_id' => (string) $about['id'], 'status' => '1'])->assertRedirect();
        $tree     = Cms::menu('main');
        $aboutRow = $tree[array_search('About Us', array_column($tree, 'label'), true)];
        $this->assertSame(['Our Team'], array_column($aboutRow['children'], 'label'));
        $this->assertSame(site_url('corporate-team'), $aboutRow['children'][0]['href']);
    }

    public function testLocationMaxIsEnforced(): void
    {
        $max = SiteSchema::MENUS['footer_legal']['max']; // 3 seeded
        for ($i = 4; $i <= $max; $i++) {
            $this->adminPost('admin/cms/menus/footer_legal/add', ['label' => 'L' . $i, 'link_type' => 'url', 'url' => 'x', 'status' => '0'])->assertRedirect();
        }
        $r = $this->adminPost('admin/cms/menus/footer_legal/add', ['label' => 'Too many', 'link_type' => 'url', 'url' => 'x', 'status' => '1']);
        $r->assertStatus(200);
        $r->assertSee('maximum of ' . $max);
        $this->assertNull($this->itemByLabel('footer_legal', 'Too many'));
        $this->assertSame($max, (new CmsMenuItemsModel())->where('menu_id', $this->menuId('footer_legal'))->countAllResults());
    }

    public function testMenuRowIsCreatedOnFirstWriteOnly(): void
    {
        db_connect()->table('cms_menus')->where('location', 'social')->delete(); // cascades its items
        $this->admin()->get('admin/cms/menus')->assertStatus(200);
        $this->admin()->get('admin/cms/menus/social')->assertSee('No links yet.');
        $this->assertNull((new CmsMenusModel())->where('location', 'social')->first(), 'GET never writes');

        $this->adminPost('admin/cms/menus/social/add', ['label' => 'Instagram', 'link_type' => 'url', 'url' => 'https://www.instagram.com/x', 'status' => '1'])->assertRedirect();
        $this->assertNotNull((new CmsMenusModel())->where('location', 'social')->first());
        $this->assertSame(['Instagram'], $this->labels('social'));
        $this->assertTrue(Cms::menu('social')[0]['new_tab'], 'external links open in a new tab');
    }

    // ------------------------------------------------------------------
    // Settings
    // ------------------------------------------------------------------

    public function testSettingsFormShowsEveryField(): void
    {
        $r = $this->admin()->get('admin/cms/settings');
        $r->assertStatus(200);
        foreach (SiteSchema::settingFields() as $key => $f) {
            $r->assertSee('name="' . $key . '"', null);
        }
        $r->assertSee('value="Franchise Enquiry"', null);
    }

    public function testSettingsValidationKeepsInput(): void
    {
        Cms::settings(); // warm the cache
        $cases = [
            ['nav_cta_url' => 'javascript:alert(1)', 'nav_cta_label' => 'Typed label'],
            ['footer_email' => 'not-an-email', 'nav_cta_label' => 'Typed label'],
            ['nav_cta_label' => str_repeat('x', 41)],
            ['footer_phone' => "123\n456"],
        ];
        foreach ($cases as $data) {
            $r = $this->adminPost('admin/cms/settings', $data);
            $r->assertStatus(200);
            $r->assertSee('Nothing was saved.');
        }
        $r = $this->adminPost('admin/cms/settings', ['nav_cta_url' => 'javascript:alert(1)', 'nav_cta_label' => 'Typed label']);
        $r->assertSee('value="Typed label"', null);
        $r->assertSee('value="javascript:alert(1)"', null);
        $this->assertSame('Franchise Enquiry', Cms::setting('nav_cta_label'));
        $this->assertSame('franchise-details#callback', (new CmsSettingsModel())->map()['nav_cta_url']);
    }

    public function testSettingsSave(): void
    {
        Cms::settings(); // warm the cache
        $data = [];
        foreach (SiteSchema::settingFields() as $key => $f) {
            $data[$key] = (new CmsSettingsModel())->map()[$key] ?? '';
        }
        $data = array_merge($data, [
            'nav_cta_label'  => '  Become a Franchisee ',
            'nav_cta_url'    => 'https://www.coldwellbanker.in/franchise-details',
            'footer_email'   => 'hello@coldwellbanker.in',
            'footer_address' => "Line 1\r\nLine 2",
            'evil_key'       => 'x',
        ]);
        $r = $this->adminPost('admin/cms/settings', $data);
        $r->assertRedirect();
        $this->assertStringEndsWith('admin/cms/settings', $r->getRedirectUrl());
        $map = (new CmsSettingsModel())->map();
        $this->assertSame('Become a Franchisee', $map['nav_cta_label']);
        $this->assertSame("Line 1\nLine 2", $map['footer_address']);
        $this->assertArrayNotHasKey('evil_key', $map);
        $this->assertSame('hello@coldwellbanker.in', Cms::setting('footer_email'), 'cache cleared after save');
        $this->assertSame('https://www.coldwellbanker.in/franchise-details', Cms::setting('nav_cta_url'));
    }

    public function testSidebarHasCmsEntry(): void
    {
        $r = $this->admin()->get('admin/cms/menus');
        foreach (['admin/cms/pages', 'admin/cms/pages?type=article', 'admin/cms/menus', 'admin/cms/settings'] as $href) {
            $r->assertSee('href="' . site_url(explode('?', $href)[0]), null);
        }
        $r->assertSee('?type=article', null);
        $r->assertDontSee('admin/home-page', null);
    }
}
