<?php

use App\Database\Seeds\CmsSeeder;
use App\Database\Seeds\FranchisePageSeeder;
use App\Libraries\Cms\BlockTypes;
use App\Libraries\Cms\Cms;
use App\Models\FranchiseeEnquiryModel;
use CodeIgniter\Config\Factories;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Config\Database;

/**
 * Franchise as a CMS page (Figma 77:58): seeder, new block types (steps, office_finder,
 * testimonials, faq, enquiry_form, values grid3), the call-back form round trip,
 * redirects for the never-working legacy URLs, hidden unconfirmed content and escaping.
 *
 * @internal
 */
final class CmsFranchiseTest extends CIUnitTestCase
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
        $this->seed(FranchisePageSeeder::class);
        Cms::clearCache();
    }

    public function testFranchiseRendersDesignSectionsAndHidesUnconfirmedContent(): void
    {
        $r = $this->get('franchise-details');
        $r->assertOK();
        foreach (['Own a Coldwell Banker Franchise', 'Why partner with us', 'Everything You Need to Grow', 'Global Brand Equity',
            'Business Coaching', 'Your Journey to Ownership', 'Agreement &amp; Onboarding', 'Find a Franchise Office',
            'Request a Call Back', 'Send Request', 'id="callback"', 'cb-vcard--feature', 'cb-steps__row'] as $t) {
            $r->assertSee($t);
        }
        // FAQ answers and owner testimonials are not confirmed yet: never shown
        $r->assertDontSee('Frequently Asked');
        $r->assertDontSee('Partners Who Grew');
        $r->assertDontSee('to be confirmed');
        // the office list comes from the franchises table, which the test DB does not have: graceful fallback
        $r->assertSee('Office details are being updated');
        $this->assertMatchesRegularExpression('#<a[^>]+aria-current="page"[^>]*>Franchise</a>#', $r->getBody());
    }

    public function testDeadLegacyUrlsRedirectAndHeaderButtonPointsAtCallBack(): void
    {
        foreach (['partner-with-us', 'partner_with_us', 'franchise_details'] as $old) {
            $this->get($old)->assertRedirectTo(site_url('franchise-details'));
        }
        $this->assertSame('franchise-details#callback', Cms::setting('nav_cta_url'));
        $this->assertSame(0, db_connect()->table('cms_block_items')->where('cta_url', 'partner-with-us')->countAllResults());
        $this->seed(FranchisePageSeeder::class); // idempotent
        $this->assertSame(1, db_connect()->table('cms_pages')->where('slug', 'franchise-details')->countAllResults());
    }

    public function testNewBlockTypesRegistered(): void
    {
        foreach (['steps', 'office_finder', 'testimonials', 'faq', 'enquiry_form'] as $t) {
            $this->assertTrue(BlockTypes::has($t), $t);
        }
        $this->assertArrayHasKey('layout', BlockTypes::TYPES['values']['settings']);
    }

    public function testFaqAndTestimonialsRenderOnceShownAndEscapeInput(): void
    {
        $db  = db_connect();
        $pid = (int) $db->table('cms_pages')->select('id')->where('slug', 'franchise-details')->get()->getRow('id');
        $faq = (int) $db->table('cms_blocks')->select('id')->where(['page_id' => $pid, 'type' => 'faq'])->get()->getRow('id');
        $tst = (int) $db->table('cms_blocks')->select('id')->where(['page_id' => $pid, 'type' => 'testimonials'])->get()->getRow('id');
        $db->table('cms_blocks')->whereIn('id', [$faq, $tst])->update(['status' => 1]);
        $db->table('cms_block_items')->where('block_id', $faq)->update(['status' => 1, 'body' => "Line one <script>alert(1)</script>\n\nSecond para"]);
        $db->table('cms_block_items')->insert(['block_id' => $tst, 'item_group' => 'quote', 'title' => '<img src=x onerror=alert(1)>', 'subtitle' => 'KD Properties, Ahmedabad',
            'body' => 'Great support "><svg onload=alert(1)>', 'sort_order' => 10, 'status' => 1]);
        Cms::clearCache();

        $body = $this->get('franchise-details')->getBody();
        $this->assertStringContainsString('Frequently Asked', $body);
        $this->assertStringContainsString('<details class="cb-qa cb-reveal" open>', $body);
        $this->assertStringContainsString('Partners Who Grew', $body);
        $this->assertStringContainsString('KD Properties, Ahmedabad', $body);
        $this->assertStringNotContainsString('<script>alert(1)', $body);
        $this->assertStringNotContainsString('<img src=x', $body);
        $this->assertStringNotContainsString('<svg onload', $body);
    }

    public function testCallBackFormSavesEnquiryAndReturnsToTheFormAnchor(): void
    {
        // Same approach as EnquiryFormsTest: the enquiry table lives in the main DB; the row is removed again.
        $model = new FranchiseeEnquiryModel(Database::connect('default'));
        Factories::injectMock('models', FranchiseeEnquiryModel::class, $model);
        $email = 'phpunit.cms.franchise.' . uniqid() . '@example.com';

        $r = $this->withSession([])->withHeaders(['Referer' => site_url('franchise-details')])->post('franchisee-enquiry', [
            csrf_token() => csrf_hash(), 'source' => 'cms',
            'name' => 'PHPUnit CMS', 'email' => $email, 'phone' => '9876543210',
            'profession' => 'Broker', 'industry' => 'Real Estate', 'location' => 'Pune',
        ]);
        $r->assertRedirect();
        $this->assertSame(site_url('franchise-details') . '#callback', $r->getRedirectUrl());
        $row = $model->where('email', $email)->first();
        $this->assertNotNull($row);
        $this->assertSame(['Broker', 'Real Estate', 'Pune'], [$row['profession'], $row['industry'], $row['location']]);
        $model->where('email', $email)->delete();

        // invalid input: nothing saved, back to the form; an off-site Referer never becomes the target
        $r = $this->withSession([])->withHeaders(['Referer' => 'https://evil.example/franchise-details'])->post('franchisee-enquiry', [
            csrf_token() => csrf_hash(), 'source' => 'cms', 'name' => '', 'email' => 'nope', 'phone' => '',
        ]);
        $this->assertSame(site_url('/') . '#callback', $r->getRedirectUrl());
    }
}
