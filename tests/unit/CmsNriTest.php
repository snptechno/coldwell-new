<?php

use App\Database\Seeds\CmsSeeder;
use App\Database\Seeds\NriPageSeeder;
use App\Libraries\Cms\BlockTypes;
use App\Libraries\Cms\Cms;
use App\Libraries\Cms\NriDesk;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Config\Email as EmailConfig;

/**
 * NRI Services as a CMS page (slug nri, Figma "NRI Services – v2" 134:600): seeder content and
 * Figma sections, admin-editable blocks (incl. escaping), the NRI desk form's CMS mode
 * (validation, allow-lists, honeypot, throttling, same-origin return, stored row), the legacy
 * AJAX reply, and a render budget.
 *
 * @internal
 */
final class CmsNriTest extends CIUnitTestCase
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
        $this->seed(NriPageSeeder::class);
        // nri_enquiry is a legacy table (no migration): build it in the test DB with Forge so this
        // works on SQLite and on a MySQL test group alike. $this->db is always the 'tests' group.
        $forge = \Config\Database::forge($this->db);
        $forge->dropTable('nri_enquiry', true);
        $forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name'       => ['type' => 'VARCHAR', 'constraint' => 100],
            'email'      => ['type' => 'VARCHAR', 'constraint' => 150],
            'phone'      => ['type' => 'VARCHAR', 'constraint' => 20],
            'country'    => ['type' => 'VARCHAR', 'constraint' => 60],
            'message'    => ['type' => 'TEXT'],
            'created_on' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $forge->addKey('id', true);
        $forge->createTable('nri_enquiry');
        // Never talk to a real SMTP server from tests: no login => EnquiryMailer logs and skips.
        $mail           = config(EmailConfig::class);
        $mail->protocol = 'smtp';
        $mail->SMTPHost = 'smtp.example.invalid';
        $mail->SMTPUser = '';
        cache()->clean(); // CMS cache (testing cache dir only)
        $this->resetThrottle();
        Cms::clearCache();
    }

    /** Each test starts with a full submission allowance for the test client's IP. */
    private function resetThrottle(): void
    {
        foreach (['0.0.0.0', '127.0.0.1', service('request')->getIPAddress()] as $ip) {
            \Config\Services::throttler()->remove(NriDesk::throttleKey($ip));
        }
    }

    protected function tearDown(): void
    {
        \Config\Database::forge($this->db)->dropTable('nri_enquiry', true);
        cache()->clean();
        parent::tearDown();
    }

    /** @return array<string, string> */
    private function validPost(array $over = []): array
    {
        return array_merge([
            csrf_token() => csrf_hash(), 'source' => 'cms', 'name' => 'Priya Nair', 'email' => 'priya@example.com',
            'phone' => '+971 50 123 4567', 'nri_country' => 'United Arab Emirates', 'interest' => 'Buying a property', 'city' => 'Other',
        ], $over);
    }

    private function postNri(array $post, string $referer = ''): \CodeIgniter\Test\TestResponse
    {
        return $this->withSession([])->withHeaders(['Referer' => $referer !== '' ? $referer : site_url('nri')])->post('nri-enquiry', $post);
    }

    public function testPageRendersEveryFigmaSection(): void
    {
        $r = $this->get('nri');
        $r->assertOK();
        foreach ([
            // hero
            'Home  ·  NRI Services', 'Think Real Estate, Think India', 'Talk to Our NRI Desk', 'href="#nri-enquiry"', 'city-mumbai',
            // statement + audience chips
            'Why India, why now', 'NRIs &amp; PIOs', 'OCI Card Holders', 'Returning Indians', 'Global Investors',
            // services: soft panel, featured first card, checklists
            'cb-values--panel', 'cb-vcard--feature', 'Services for Every Step', 'cb-vcard__list',
            'Legal Consultancy &amp; Documentation', 'Rent Collection &amp; Deposit', 'Repatriation',
            // how it works: plain steps
            'cb-steps--plain', 'How It Works', 'Share Your Goals', 'Manage &amp; Repatriate',
            // NRI desk form
            'id="nri-enquiry"', 'action="' . site_url('nri-enquiry') . '"', 'name="source" value="cms"', 'name="nri_country"',
            'name="interest"', 'name="city"', 'name="website"', '<option value="Other">', 'Send Request',
            // newsletter band
            'Let’s Build a Brighter',
        ] as $t) {
            $r->assertSee($t);
        }
        foreach (NriDesk::INTERESTS as $o) {
            $r->assertSee(esc($o));
        }
        $r->assertSee(csrf_token());
        $this->assertSame(3, substr_count($r->getBody(), 'class="cb-vcard '));
        $this->assertSame(16, substr_count($r->getBody(), '<li><span aria-hidden="true"><svg'));
        $this->assertMatchesRegularExpression('#<a[^>]+aria-current="page"[^>]*>NRI Corner</a>#', $r->getBody());
        $this->assertStringContainsString('NRI Real Estate Services in India', $r->getBody()); // SEO title
    }

    public function testBlockTypesExposeTheNewFields(): void
    {
        $this->assertArrayHasKey('nri_enquiry_form', BlockTypes::TYPES);
        $this->assertArrayHasKey('body', BlockTypes::TYPES['values']['groups']['value']['fields']);
        $this->assertSame('white', BlockTypes::settings('values', '{}')['background']);
        $this->assertSame('panel', BlockTypes::settings('steps', '{}')['style']);
        foreach (['home', 'tag', 'key'] as $icon) {
            $this->assertArrayHasKey($icon, BlockTypes::ICONS);
            helper('cb');
            $this->assertStringContainsString('<path', cb_icon($icon));
        }
    }

    public function testAdminCanManageEveryNriBlock(): void
    {
        $pageId = (int) $this->db->table('cms_pages')->select('id')->where('slug', 'nri')->get()->getRow('id');
        $admin  = ['admin_user' => 'tester'];

        $r = $this->withSession($admin)->get('admin/cms/pages/edit/' . $pageId);
        $r->assertOK();
        foreach (['NRI desk enquiry form', 'Icon cards (values)', 'Numbered steps (process)', 'Overview statement + audience chips'] as $t) {
            $r->assertSee($t);
        }

        $ids = fn (string $type): int => (int) $this->db->table('cms_blocks')->select('id')->where('page_id', $pageId)->where('type', $type)->get()->getRow('id');
        $r   = $this->withSession($admin)->get("admin/cms/pages/{$pageId}/blocks/" . $ids('values'));
        $r->assertOK();
        $r->assertSee('Soft panel');
        $item = (int) $this->db->table('cms_block_items')->select('id')->where('block_id', $ids('values'))->where('title', 'Sell')->get()->getRow('id');
        $this->withSession($admin)->get("admin/cms/pages/{$pageId}/blocks/" . $ids('values') . "/items/value/edit/{$item}")
            ->assertSee('Checklist (optional, one item per line)');
        $this->withSession($admin)->get("admin/cms/pages/{$pageId}/blocks/" . $ids('steps'))->assertSee('Plain (white)');
        $this->withSession($admin)->get("admin/cms/pages/{$pageId}/blocks/" . $ids('nri_enquiry_form'))->assertSee('#nri-enquiry');

        // Not logged in: the admin filter redirects away.
        $this->assertNotSame(200, $this->withSession([])->get('admin/cms/pages/edit/' . $pageId)->response()->getStatusCode());
    }

    public function testAdminEditsShowAndAreEscaped(): void
    {
        $block = $this->db->table('cms_blocks')->where('type', 'values')->get()->getRowArray();
        $this->db->table('cms_block_items')->where('block_id', $block['id'])->where('title', 'Buy')
            ->update(['title' => 'Buy <script>alert(1)</script>', 'body' => "Due diligence\n<img src=x onerror=alert(2)>\n- Home loans"]);
        Cms::clearCache();

        $r = $this->get('nri');
        $r->assertSee('Buy &lt;script&gt;alert(1)&lt;/script&gt;');
        $r->assertSee('&lt;img src=x onerror=alert(2)&gt;');
        $r->assertSee('Home loans'); // leading "- " is stripped
        $r->assertDontSee('<script>alert(1)</script>');
        $r->assertDontSee('<img src=x onerror');
    }

    public function testHiddenBlockAndUnpublishedPage(): void
    {
        $this->db->table('cms_blocks')->where('type', 'nri_enquiry_form')->update(['status' => 0]);
        Cms::clearCache();
        $this->get('nri')->assertDontSee('id="nri-enquiry"');

        $this->db->table('cms_pages')->where('slug', 'nri')->update(['status' => 0]);
        Cms::clearCache();
        $this->get('nri')->assertStatus(404);
    }

    public function testValidCmsEnquiryIsStoredWithInterestAndCity(): void
    {
        $r = $this->postNri($this->validPost(['message' => 'Looking at 2BHK options']));
        $this->assertSame(site_url('nri') . '#nri-enquiry', $r->getRedirectUrl());
        $this->assertTrue((bool) session()->getFlashdata('nri_ok'));

        $row = $this->db->table('nri_enquiry')->get()->getRowArray();
        $this->assertSame('Priya Nair', $row['name']);
        $this->assertSame('United Arab Emirates', $row['country']);
        $this->assertSame("Interested in: Buying a property\nPreferred city: Other\nLooking at 2BHK options", $row['message']);
    }

    public function testValuesOffTheAllowListAreDropped(): void
    {
        $this->postNri($this->validPost(['interest' => '<b>Hack</b>', 'city' => 'Atlantis', 'name' => str_repeat('x', 300)]));
        $row = $this->db->table('nri_enquiry')->get()->getRowArray();
        $this->assertSame('NRI desk enquiry (no details given)', $row['message']);
        $this->assertSame(100, mb_strlen($row['name']));
        $this->assertSame('', NriDesk::interest('buying a property')); // case-sensitive allow-list
        $this->assertSame('Other', NriDesk::city('Other'));
    }

    public function testInvalidCmsEnquiryReturnsToFormWithoutSaving(): void
    {
        foreach ([['email' => 'not-an-email'], ['phone' => 'call me'], ['nri_country' => ''], ['name' => ['array']]] as $bad) {
            $r = $this->postNri($this->validPost($bad));
            $this->assertSame(site_url('nri') . '#nri-enquiry', $r->getRedirectUrl());
            $this->assertStringContainsString('country of residence', (string) session()->getFlashdata('nri_msg'));
            $this->assertFalse((bool) session()->getFlashdata('nri_ok'));
        }
        $this->assertSame(0, $this->db->table('nri_enquiry')->countAllResults());
    }

    public function testHoneypotIsAcceptedQuietlyButNotStored(): void
    {
        $r = $this->postNri($this->validPost(['website' => 'http://spam.example']));
        $this->assertSame(site_url('nri') . '#nri-enquiry', $r->getRedirectUrl());
        $this->assertTrue((bool) session()->getFlashdata('nri_ok'));
        $this->assertSame(0, $this->db->table('nri_enquiry')->countAllResults());
    }

    public function testSubmissionsAreThrottledPerIp(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->postNri($this->validPost(['email' => "p{$i}@example.com"]));
        }
        $this->postNri($this->validPost(['email' => 'p6@example.com']));
        $this->assertStringContainsString('Too many requests', (string) session()->getFlashdata('nri_msg'));
        $this->assertSame(5, $this->db->table('nri_enquiry')->countAllResults());
    }

    public function testOffSiteRefererIsNeverFollowed(): void
    {
        $r = $this->postNri($this->validPost(), 'https://evil.example/nri');
        $this->assertStringStartsWith(site_url(''), (string) $r->getRedirectUrl());
        $this->assertStringNotContainsString('evil.example', (string) $r->getRedirectUrl());
    }

    public function testLegacyAjaxReplyIsUnchanged(): void
    {
        $post = [csrf_token() => csrf_hash(), 'name' => 'A', 'email' => 'a@example.com', 'phone' => '9876543210', 'nri_country' => 'UK'];
        $r    = $this->withSession([])->post('nri-enquiry', $post); // no message => legacy validation error
        $this->assertStringStartsWith('text/plain', $r->response()->getHeaderLine('Content-Type'));
        $this->assertSame('Error', $r->response()->getBody());

        $r = $this->withSession([])->post('nri-enquiry', $post + ['message' => 'Hi']);
        $this->assertSame('Success', $r->response()->getBody());
    }

    public function testRenderBudget(): void
    {
        $this->get('nri'); // warm the CMS cache
        $t0   = hrtime(true);
        $r    = $this->get('nri');
        $ms   = (hrtime(true) - $t0) / 1e6;
        $html = $r->getBody();

        $this->assertLessThan(400, $ms, 'NRI page should render in under 400 ms from the CMS cache');
        $this->assertLessThan(120 * 1024, strlen($html), 'HTML should stay under 120 KB');
        // Hero image is the LCP element: eager + high priority; everything below the fold is lazy.
        $this->assertMatchesRegularExpression('#<img[^>]+city-mumbai[^>]+loading="eager"[^>]+fetchpriority="high"#', $html);
        $this->assertStringContainsString('<source type="image/webp"', $html);
        // Every image in the page's own blocks (statement → newsletter band) declares loading.
        // (Header logos are above the fold; footer/tracking come from shared partials.)
        $start = strpos($html, 'class="cb-overview"');
        $end   = strpos($html, '<footer');
        $this->assertNotFalse($start);
        $this->assertNotFalse($end);
        $this->assertSame(0, preg_match_all('#<img(?![^>]*loading=)[^>]*>#', substr($html, $start, $end - $start)), 'every block <img> declares loading');
    }
}
