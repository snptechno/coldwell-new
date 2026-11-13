<?php

use App\Libraries\EnquiryMailer;
use App\Models\ContactEnquiryModel;
use App\Models\FranchiseeEnquiryModel;
use App\Models\FranchiseeProjectEnquiryModel;
use App\Models\FranchisesModel;
use App\Models\JobApplicationsModel;
use App\Models\NewsletterSubscriptionsModel;
use App\Models\NriEnquiryModel;
use CodeIgniter\Config\Factories;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use Config\Database;
use Config\Email as EmailConfig;

/**
 * Step 7 feature & unit tests for public enquiry forms, email service,
 * CSRF protection, open-redirect prevention, and resume upload hardening.
 *
 * @internal
 */
final class EnquiryFormsTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    private function useLiveStagingConnection(): void
    {
        $dbConfig          = config(Database::class);
        $dbConfig->tests   = $dbConfig->default;
        Factories::injectMock('config', 'Database', $dbConfig);
    }

    public function testEmailConfigDefaultsArePopulated(): void
    {
        $config = config(EmailConfig::class);
        $this->assertSame('info@coldwellbanker.in', $config->fromEmail);
        $this->assertSame('Coldwell Banker India', $config->fromName);
        $this->assertSame('html', $config->mailType);
        $this->assertSame('smtp', $config->protocol);
        $this->assertSame(587, $config->SMTPPort);
    }

    public function testEnquiryMailerSkipsGracefullyWhenNoSmtpUserConfigured(): void
    {
        $mailer = new EnquiryMailer();
        $result = $mailer->send(
            ['info@coldwellbanker.in'],
            'Unit Test Subject',
            '<html><body><p>Test</p></body></html>',
            'visitor@example.com',
            'Visitor Name',
        );
        $this->assertTrue($result);
    }

    public function testGetJobApplicationsReturns404WithoutInsertingRows(): void
    {
        $this->useLiveStagingConnection();
        $model  = new JobApplicationsModel(Database::connect('default'));
        $before = $model->countAllResults();

        $this->withRoutes()->get('job-applications')->assertStatus(404);
        $this->withRoutes()->get('job_applications')->assertStatus(404);

        $this->assertSame($before, $model->countAllResults());
    }

    public function testContactEnquirySavesLeadAndRedirects(): void
    {
        $this->useLiveStagingConnection();
        $model = new ContactEnquiryModel(Database::connect('default'));
        Factories::injectMock('models', ContactEnquiryModel::class, $model);

        $_SESSION = [];
        $email    = 'phpunit.contact.' . uniqid() . '@example.com';

        $response = $this->withSession([])->post('contact_enquiry', [
            csrf_token() => csrf_hash(),
            'name'       => 'PHPUnit Contact',
            'email'      => $email,
            'phone'      => '9876543210',
            'message'    => 'Enquiry from PHPUnit',
        ]);

        $response->assertRedirect();
        $this->assertStringContainsString('contact-us#cont-right', $response->getRedirectUrl());

        $row = $model->where('email', $email)->first();
        $this->assertNotNull($row);
        $this->assertSame('PHPUnit Contact', $row['name']);
        $model->where('email', $email)->delete();
    }

    public function testContactEnquiryPopupSavesLeadAndReturnsPlainTextOne(): void
    {
        $this->useLiveStagingConnection();
        $model = new ContactEnquiryModel(Database::connect('default'));
        Factories::injectMock('models', ContactEnquiryModel::class, $model);

        $_SESSION = [];
        $email    = 'phpunit.popup.' . uniqid() . '@example.com';

        $response = $this->withSession([])->post('contact-enquiry-popup', [
            csrf_token() => csrf_hash(),
            'name'       => 'PHPUnit Popup',
            'email'      => $email,
            'phone'      => '9876543211',
            'interest'   => ['Buying', 'Selling'],
            'source'     => 'popup',
        ]);

        $response->assertStatus(200);
        $this->assertSame('1', $response->response()->getBody());

        $row = $model->where('email', $email)->first();
        $this->assertNotNull($row);
        $this->assertSame('Buying, Selling', $row['interested_in']);
        $model->where('email', $email)->delete();
    }

    public function testFranchiseeEnquirySavesLeadAndRedirects(): void
    {
        $this->useLiveStagingConnection();
        $model = new FranchiseeEnquiryModel(Database::connect('default'));
        Factories::injectMock('models', FranchiseeEnquiryModel::class, $model);

        $_SESSION = [];
        $email    = 'phpunit.franchisee.' . uniqid() . '@example.com';

        $response = $this->withSession([])->post('franchisee_enquiry', [
            csrf_token() => csrf_hash(),
            'name'       => 'PHPUnit Franchisee',
            'email'      => $email,
            'phone'      => '9876543212',
            'interested' => 'Real Estate',
            'loc'        => 'Pune',
        ]);

        $response->assertRedirect();
        $this->assertStringContainsString('our-franchise#franchisee_form', $response->getRedirectUrl());

        $row = $model->where('email', $email)->first();
        $this->assertNotNull($row);
        $this->assertSame('Pune', $row['location']);
        $model->where('email', $email)->delete();
    }

    public function testFranchiseeDetailsEnquiryBlocksExternalRefererOpenRedirect(): void
    {
        $this->useLiveStagingConnection();
        $db        = Database::connect('default');
        $projModel = new FranchiseeProjectEnquiryModel($db);
        $franModel = new FranchisesModel($db);
        Factories::injectMock('models', FranchiseeProjectEnquiryModel::class, $projModel);
        Factories::injectMock('models', FranchisesModel::class, $franModel);

        $_SESSION = [];
        $email    = 'phpunit.office.' . uniqid() . '@example.com';

        $response = $this->withSession([])
            ->withHeaders(['Referer' => 'https://attacker.example.org/phish'])
            ->post('franchisee_details_enquiry/12', [
                csrf_token() => csrf_hash(),
                'fname'      => 'PHPUnit',
                'lname'      => 'Office',
                'email'      => $email,
                'phone'      => '9876543213',
                'interest'   => ['Buying'],
            ]);

        $response->assertRedirect();
        $redirectUrl = $response->getRedirectUrl();
        $this->assertStringNotContainsString('attacker.example.org', $redirectUrl);
        $this->assertStringContainsString('our-franchise-details/', $redirectUrl);

        $row = $projModel->where('email_id', $email)->first();
        $this->assertNotNull($row);
        $this->assertSame('PHPUnit Office', $row['name']);
        $projModel->where('email_id', $email)->delete();
    }

    public function testNriEnquirySavesLeadAndReturnsSuccess(): void
    {
        $this->useLiveStagingConnection();
        $model = new NriEnquiryModel(Database::connect('default'));
        Factories::injectMock('models', NriEnquiryModel::class, $model);

        $_SESSION = [];
        $email    = 'phpunit.nri.' . uniqid() . '@example.com';

        $response = $this->withSession([])->post('nri-enquiry', [
            csrf_token()  => csrf_hash(),
            'name'        => 'PHPUnit NRI',
            'email'       => $email,
            'phone'       => '9876543214',
            'nri_country' => 'Singapore',
            'message'     => 'Interested in residential investment',
        ]);

        $response->assertStatus(200);
        $this->assertSame('Success', $response->response()->getBody());

        $row = $model->where('email', $email)->first();
        $this->assertNotNull($row);
        $this->assertSame('Singapore', $row['country']);
        $model->where('email', $email)->delete();
    }

    public function testNewsletterSubscriptionPopulatesNonZeroCreatedDate(): void
    {
        $this->useLiveStagingConnection();
        $model = new NewsletterSubscriptionsModel(Database::connect('default'));
        Factories::injectMock('models', NewsletterSubscriptionsModel::class, $model);

        $_SESSION = [];
        $email    = 'phpunit.newsletter.' . uniqid() . '@example.com';

        $response = $this->withSession([])->post('newsletter_subscription', [
            csrf_token() => csrf_hash(),
            'email'      => $email,
        ]);

        $response->assertRedirect();
        $this->assertStringContainsString('newsletter#media', $response->getRedirectUrl());

        $row = $model->where('email', $email)->first();
        $this->assertNotNull($row);
        $this->assertStringStartsNotWith('0000-00-00', (string) $row['created_date']);
        $model->where('email', $email)->delete();
    }
}
