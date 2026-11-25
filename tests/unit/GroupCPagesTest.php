<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;

final class GroupCPagesTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    public function testQuickPayPageRendersWithRazorpayAndCsrf(): void
    {
        $r = $this->get('quick-pay');
        $r->assertOK();
        $r->assertSee('cb-page');
        $r->assertSee('Quick Pay');
        $r->assertSee('Online Payment Details');
        $r->assertSee('checkout.razorpay.com/v1/checkout.js');
    }

    public function testError404ViewRendersRedesignedLayout(): void
    {
        $html = view('errors/html/error_404', ['message' => 'Test missing route'], ['saveData' => false]);
        $this->assertStringContainsString('cb-err404', $html);
        $this->assertStringContainsString('Page Not Found', $html);
        $this->assertStringContainsString('Back to Home', $html);
        $this->assertStringContainsString('Explore Our Network', $html);
    }

    public function testFranchiseDetailsViewRendersAndEscapes(): void
    {
        $html = view('site/our_franchisee_details', [
            'id'           => 42,
            'page_info'    => (object) ['meta_description' => 'Office details'],
            'rz1'          => [
                'id'          => 42,
                'title'       => 'Mi Kasa <script>alert(1)</script>',
                'location'    => 'Noida, Delhi NCR',
                'address'     => 'Sector 62, Noida',
                'mobile_no'   => '0120 4359135',
                'email'       => 'noida@coldwellbanker.in',
                'description' => '<p>Trusted office in Noida.</p>',
            ],
            'rz2'          => [['name' => 'Amit Sharma', 'designation' => 'Managing Partner', 'image_name' => '']],
            'rz4'          => [['name' => 'Priya Verma', 'designation' => 'Senior Advisor', 'image_name' => '']],
            'testimonials' => [['name' => 'Rahul', 'description' => 'Great service!']],
        ], ['saveData' => false]);

        $this->assertStringContainsString('cb-page', $html);
        $this->assertStringContainsString('Enquire With This Office', $html);
        $this->assertStringContainsString('Franchise Owners', $html);
        $this->assertStringContainsString('Real Estate Specialists', $html);
        $this->assertStringContainsString('Testimonials', $html);
        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
    }
}
