<?php

use App\Database\Seeds\CmsSeeder;
use App\Database\Seeds\NetworkPageSeeder;
use App\Libraries\Cms\Cms;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * Our Network as a CMS page (slug our-franchise): seeder, live {offices}/{cities} figures
 * (hidden, never "0", when the franchises table is unavailable as in the test DB), #offices
 * anchor, menu highlight and the underscore redirect.
 *
 * @internal
 */
final class CmsNetworkTest extends CIUnitTestCase
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
        $this->seed(NetworkPageSeeder::class);
        Cms::clearCache();
    }

    public function testPageRendersAndHidesUnavailableLiveCounts(): void
    {
        $r = $this->get('our-franchise');
        $r->assertOK();
        foreach (['Our Network', 'href="#offices"', 'id="offices"', 'Network at a glance', '1906', '40+', 'Open an Office in Your City', 'id="callback"'] as $t) {
            $r->assertSee($t);
        }
        // no franchises table in the test DB: live figures are dropped, never shown as 0 or a raw token
        $r->assertDontSee('{offices}');
        $r->assertDontSee('{cities}');
        $r->assertDontSee('>0<');
        $r->assertDontSee('Franchise offices</span>');
        $this->assertMatchesRegularExpression('#<a[^>]+aria-current="page"[^>]*>Our Network</a>#', $r->getBody());
    }

    public function testUnderscoreRedirectsAndMenuPointsAtPage(): void
    {
        $this->get('our_franchise')->assertRedirectTo(site_url('our-franchise'));
        $main = array_values(array_filter(Cms::menu('main'), static fn ($i) => $i['label'] === 'Our Network'))[0];
        $this->assertSame(site_url('our-franchise'), $main['href']);
        $this->seed(NetworkPageSeeder::class);
        $this->assertSame(1, db_connect()->table('cms_pages')->where('slug', 'our-franchise')->countAllResults());
    }
}
