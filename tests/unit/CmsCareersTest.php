<?php

use App\Database\Seeds\CareersPageSeeder;
use App\Database\Seeds\CmsSeeder;
use App\Libraries\Cms\Cms;
use App\Libraries\Cms\Jobs;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * Careers as a CMS page (slug career, Figma 90:58): seeder, live openings from the jobs table
 * (tabs, duties, Apply pre-select), the closed state when there are no jobs, hidden employee
 * stories, and the job form's CMS mode (validation redirects back to #apply, legacy AJAX reply
 * unchanged).
 *
 * @internal
 */
final class CmsCareersTest extends CIUnitTestCase
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
        $this->seed(CareersPageSeeder::class);
        $this->db->query('DROP TABLE IF EXISTS ' . $this->db->prefixTable('jobs'));
        Cms::clearCache();
    }

    protected function tearDown(): void
    {
        $this->db->query('DROP TABLE IF EXISTS ' . $this->db->prefixTable('jobs'));
        cache()->delete('cms_jobs_open');
        parent::tearDown();
    }

    private function makeJobs(): void
    {
        $this->db->query('CREATE TABLE ' . $this->db->prefixTable('jobs') . ' (id INT PRIMARY KEY, type VARCHAR(60), position VARCHAR(200), qualification TEXT,
            min_experience INT, max_experience INT, location VARCHAR(120), description TEXT, status TINYINT)');
        $this->db->table('jobs')->insertBatch([
            ['id' => 19, 'type' => 'COLDWELL BANKER INDIA OPENINGS', 'position' => 'Senior Manager - Franchise Acquisition',
                'qualification' => '<p>Graduation: Any degree</p><p>MBA preferred</p>', 'min_experience' => 4, 'max_experience' => 0,
                'location' => 'Khar west Mumbai', 'status' => 1,
                'description' => "<p><strong>Business Development:</strong></p><p>·&nbsp;&nbsp;Identifying potential\n business partners</p><p>ok</p><p>Performing catchment analysis for new markets</p>"],
            ['id' => 16, 'type' => 'FRANCHISEES OPENINGS', 'position' => 'Sales Associate', 'qualification' => 'Graduate',
                'min_experience' => 2, 'max_experience' => 6, 'location' => 'Pune', 'description' => '<p>Meeting clients for site visits</p>', 'status' => 1],
            ['id' => 5, 'type' => 'FRANCHISEES OPENINGS', 'position' => 'Closed Role', 'qualification' => '', 'min_experience' => 0,
                'max_experience' => 0, 'location' => '', 'description' => '', 'status' => 0],
        ]);
        cache()->delete('cms_jobs_open');
    }

    public function testPageRendersOpeningsTabsAndApplyForm(): void
    {
        $this->makeJobs();
        $r = $this->get('career');
        $r->assertOK();
        foreach (['Be Part of the Most Respected Brand in Real Estate', 'href="#openings"', 'id="openings"', 'Why Build Your Career With Us',
            'cb-values--grid4', 'Coldwell Banker India (1)', 'Franchise Openings (1)', 'Senior Manager – Franchise Acquisition',
            'Graduation (any degree)', '4+ yrs', '2–6 yrs', 'Khar West, Mumbai', 'Identifying potential business partners',
            'data-apply-job="19"', 'id="apply"', 'enctype="multipart/form-data"', 'name="resume_file"', '<option value="16">',
            'Life at Coldwell Banker', 'href="' . site_url('learning-development') . '"'] as $t) {
            $r->assertSee($t);
        }
        $r->assertDontSee('Closed Role');
        $r->assertDontSee('Business Development:');
        $r->assertDontSee('Inspiring Stories of Success'); // hidden until real stories exist
        $this->assertMatchesRegularExpression('#<a[^>]+aria-current="page"[^>]*>Careers</a>#', $r->getBody());
    }

    public function testNoJobsShowsClosedStateWithoutForm(): void
    {
        $r = $this->get('career');
        $r->assertOK();
        $r->assertSee('There are no open positions right now');
        $r->assertSee('Applications open as soon as a new position is posted.');
        $r->assertDontSee('name="resume_file"');
    }

    public function testDutiesExtractionIsClean(): void
    {
        $d = Jobs::duties("<p>ESSENTIAL DUTIES:</p><p>·&nbsp;Talking to\n   clients every day</p><li>short</li><br>• Preparing monthly reports", 8);
        $this->assertSame(['Talking to clients every day', 'Preparing monthly reports'], $d);
    }

    public function testCmsJobFormValidationReturnsToApplyAndLegacyReplyIsUnchanged(): void
    {
        $this->makeJobs();
        $post = [csrf_token() => csrf_hash(), 'applicant_fname' => 'A', 'applicant_lname' => ['x'], 'gender' => 'male',
            'applicant_mobile' => '9876543210', 'applicant_email' => 'a@example.com', 'applicant_address' => 'Mumbai', 'job_id' => '19'];

        $r = $this->withSession([])->withHeaders(['Referer' => site_url('career')])->post('job-applications', $post + ['source' => 'cms']);
        $this->assertSame(site_url('career') . '#apply', $r->getRedirectUrl());
        $this->assertStringContainsString('fill in all required fields', (string) session()->getFlashdata('job_msg'));

        // valid fields, unknown job: back to the form with the position message
        $post['applicant_lname'] = 'B';
        $post['job_id']          = '999';
        $r = $this->withSession([])->withHeaders(['Referer' => site_url('career')])->post('job-applications', $post + ['source' => 'cms']);
        $this->assertSame(site_url('career') . '#apply', $r->getRedirectUrl());
        $this->assertStringContainsString('no longer open', (string) session()->getFlashdata('job_msg'));

        // off-site referer is never followed
        $r = $this->withSession([])->withHeaders(['Referer' => 'https://evil.example/career'])->post('job-applications', $post + ['source' => 'cms']);
        $this->assertStringStartsWith(site_url(''), (string) $r->getRedirectUrl());

        // legacy AJAX form (no source): plain-text reply as before
        $r = $this->withSession([])->post('job-applications', $post);
        $this->assertStringStartsWith('text/plain', $r->response()->getHeaderLine('Content-Type'));
        $this->assertSame('error', $r->response()->getBody());
    }
}
