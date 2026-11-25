<?php

use App\Database\Seeds\CmsSeeder;
use App\Database\Seeds\ContactPageSeeder;
use App\Libraries\Cms\BlockTypes;
use App\Libraries\Cms\Cms;
use App\Models\ContactEnquiryModel;
use CodeIgniter\Config\Factories;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Config\Database;

/**
 * Contact Us as a CMS page (Figma 85:58): seeder, contact_panel + location blocks, office
 * directory layout, values buttons, the message form round trip (incl. interested_in),
 * map-embed allow-list, redirect and escaping.
 *
 * @internal
 */
final class CmsContactTest extends CIUnitTestCase
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
        $this->seed(ContactPageSeeder::class);
        Cms::clearCache();
    }

    private function blockId(string $type): int
    {
        $db  = db_connect();
        $pid = (int) $db->table('cms_pages')->select('id')->where('slug', 'contact-us')->get()->getRow('id');

        return (int) $db->table('cms_blocks')->select('id')->where(['page_id' => $pid, 'type' => $type])->get()->getRow('id');
    }

    public function testContactRendersAllSections(): void
    {
        $r = $this->get('contact-us');
        $r->assertOK();
        foreach (['Let’s Talk', 'Get in Touch', 'Corporate Office', 'info@coldwellbanker.in', 'href="mailto:info@coldwellbanker.in"',
            'href="tel:+918587819200"', 'Send Us a Message', 'name="interest[]"', 'value="Join as Agent"', 'id="contact-form"',
            'Corporate Office, Mumbai', 'Get Directions', 'google.com/maps/search', 'Our Franchise Offices', 'How Can We Help?',
            'Explore Careers', 'Visit Newsroom'] as $t) {
            $r->assertSee($t);
        }
        // no unconfirmed facts: no hours placeholder, no map iframe without an embed link
        $r->assertDontSee('[Mon');
        $r->assertDontSee('<iframe');
        $this->assertMatchesRegularExpression('#<a[^>]+aria-current="page"[^>]*>Contact</a>#', $r->getBody());
    }

    public function testUnderscoreUrlRedirects(): void
    {
        $this->get('contact_us')->assertRedirectTo(site_url('contact-us'));
        $this->assertTrue(BlockTypes::has('contact_panel'));
        $this->assertTrue(BlockTypes::has('location'));
    }

    public function testMapEmbedAllowsOnlyGoogleMapsEmbedLinks(): void
    {
        $db  = db_connect();
        $id  = $this->blockId('location');
        $set = json_decode((string) $db->table('cms_blocks')->select('settings')->where('id', $id)->get()->getRow('settings'), true);

        $set['map_embed'] = 'https://evil.example/maps/embed?pb=x';
        $db->table('cms_blocks')->where('id', $id)->update(['settings' => json_encode($set)]);
        Cms::clearCache();
        $this->assertStringNotContainsString('<iframe', $this->get('contact-us')->getBody());

        $set['map_embed'] = 'https://www.google.com/maps/embed?pb=!1m18!1m12';
        $db->table('cms_blocks')->where('id', $id)->update(['settings' => json_encode($set)]);
        Cms::clearCache();
        $b = $this->get('contact-us')->getBody();
        $this->assertStringContainsString('<iframe src="https://www.google.com/maps/embed?pb=', $b);
        $this->assertStringContainsString('loading="lazy"', $b);
    }

    public function testChannelAndHoursInputIsEscaped(): void
    {
        $db = db_connect();
        $db->table('cms_block_items')->where('block_id', $this->blockId('contact_panel'))
            ->update(['title' => '<script>alert(1)</script>', 'body' => '"><img src=x onerror=alert(1)>', 'cta_url' => 'javascript:alert(1)']);
        $id  = $this->blockId('location');
        $set = json_decode((string) $db->table('cms_blocks')->select('settings')->where('id', $id)->get()->getRow('settings'), true);
        $set['hours'] = '<svg onload=alert(1)>';
        $db->table('cms_blocks')->where('id', $id)->update(['settings' => json_encode($set)]);
        Cms::clearCache();

        $body = $this->get('contact-us')->getBody();
        $this->assertStringNotContainsString('<script>alert(1)', $body);
        $this->assertStringNotContainsString('<img src=x', $body);
        $this->assertStringNotContainsString('<svg onload', $body);
        $this->assertStringNotContainsString('javascript:alert', $body);
    }

    public function testMessageFormSavesInterestsAndReturnsToForm(): void
    {
        // Same approach as EnquiryFormsTest: contact_enquiry lives in the main DB; the row is removed again.
        $model = new ContactEnquiryModel(Database::connect('default'));
        Factories::injectMock('models', ContactEnquiryModel::class, $model);
        $email = 'phpunit.cms.contact.' . uniqid() . '@example.com';

        $r = $this->withSession([])->withHeaders(['Referer' => site_url('contact-us')])->post('contact-enquiry', [
            csrf_token() => csrf_hash(), 'source' => 'cms', 'name' => 'PHPUnit Contact', 'email' => $email, 'phone' => '9876543210',
            'message' => 'Hello', 'interest' => ['Franchise', 'Selling', '<script>x</script>', 'Hacked'],
        ]);
        $this->assertSame(site_url('contact-us') . '#contact-form', $r->getRedirectUrl());
        $row = $model->where('email', $email)->first();
        $this->assertNotNull($row);
        $this->assertSame('Franchise, Selling', $row['interested_in'], 'only allow-listed interests are saved');
        $model->where('email', $email)->delete();

        // missing message / array injection: nothing saved, back to the form, no 500
        $r = $this->withSession([])->withHeaders(['Referer' => site_url('contact-us')])->post('contact-enquiry', [
            csrf_token() => csrf_hash(), 'source' => 'cms', 'name' => ['x'], 'email' => $email, 'phone' => '1', 'message' => '',
        ]);
        $this->assertSame(site_url('contact-us') . '#contact-form', $r->getRedirectUrl());
        $this->assertNull($model->where('email', $email)->first());
    }
}
