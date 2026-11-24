<?php

namespace App\Controllers\Site;

use App\Controllers\BaseController;
use App\Libraries\EnquiryMailer;
use App\Models\ContactEnquiryModel;
use App\Models\FranchiseeEnquiryModel;
use App\Models\FranchiseeProjectEnquiryModel;
use App\Models\FranchisesModel;
use App\Models\JobApplicationsModel;
use App\Models\JobsModel;
use App\Models\NewsletterSubscriptionsModel;
use App\Models\NriEnquiryModel;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Public enquiry, subscription, and career application POST handlers.
 *
 * Ports CI3 `Home::contact_enquiry`, `Home::contact_enquiry_popup`,
 * `Home::franchisee_enquiry`, `Home::franchisee_details_enquiry`,
 * `Home::nri_enquiry`, `Home::newsletter_subscription`, and
 * `Home::job_applications` while resolving audit items:
 *   - PUB-01 / LC-02: Replaces PHP 8-fatal SwiftMailer 5.1.0 (`$line{3}`) with CI4 Email (`EnquiryMailer`).
 *   - SEC-01 / PUB-03 / DL-02: Strict MIME (`finfo`) + extension allowlist (`pdf`, `doc`, `docx`, `rtf`),
 *     2 MB cap, server-generated filename stored in `public/uploads/resume/`.
 *   - PUB-21 / DL-05: POST-only routes; validates fields and uploaded file *before* inserting a DB row.
 *   - PUB-04: Removes leftover `print_r($this->input->post);die;` in `franchisee_enquiry`.
 *   - PUB-05: Server-side validation + CSRF protection on all 7 forms.
 *   - PUB-11 / RS-10: Prevents open redirect in `franchisee_details_enquiry` by verifying same-origin referer.
 *   - PUB-14: Removes dead `isset($_POST['interest'])` branch in `contact_enquiry` that referenced undefined `$id`.
 *   - PUB-16 / SEC-16 / LC-20: Uses configured sender (`info@coldwellbanker.in`) with visitor `Reply-To`,
 *     escapes all visitor values in HTML emails, and removes wildcard CORS header.
 *   - PUB-17: Replaces deprecated `FILTER_SANITIZE_STRING` in `job_applications`.
 *   - DB-01: Populates `created_date` on `newsletter_subscriptions` inserts.
 */
class Enquiry extends BaseController
{
    /** Allowed resume MIME types mapped to canonical extensions (`SEC-01`). */
    private const RESUME_MIMES = [
        'application/pdf'                                                         => 'pdf',
        'application/msword'                                                      => 'doc',
        'application/vnd.ms-office'                                               => 'doc',
        'application/CDFV2'                                                       => 'doc',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
        'application/rtf'                                                         => 'rtf',
        'text/rtf'                                                                => 'rtf',
    ];

    /** Allowed resume client extensions (`SEC-01`). */
    private const RESUME_EXTENSIONS = ['pdf', 'doc', 'docx', 'rtf'];

    /** Maximum resume size in bytes (2 MB, matching CI3's `2000000`). */
    private const RESUME_MAX_BYTES = 2_000_000;

    protected EnquiryMailer $mailer;

    public function __construct()
    {
        $this->mailer = new EnquiryMailer();
    }

    /**
     * CI3: Home::contact_enquiry (controllers/Home.php:362).
     * Handles the Contact Us form (`#contact_us`) and legacy landing-page (`lp_*`) posts.
     */
    public function contactEnquiry(): ResponseInterface
    {
        $post = $this->request->getPost();

        if (isset($post['lp_name'])) {
            $name         = trim((string) ($post['lp_name'] ?? ''));
            $email        = trim((string) ($post['lp_email'] ?? ''));
            $phone        = trim((string) ($post['lp_mobile'] ?? ''));
            $interestedIn = $post['lp_interested'] ?? '';
            if (is_array($interestedIn)) {
                $interestedIn = implode(', ', array_map('trim', $interestedIn));
            } else {
                $interestedIn = trim((string) $interestedIn);
            }

            if ($name === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL) || $phone === '') {
                return $this->response->setContentType('text/plain')->setBody('0');
            }

            model(ContactEnquiryModel::class)->addEnquiry([
                'name'          => $name,
                'email'         => $email,
                'phone'         => $phone,
                'interested_in' => $interestedIn,
            ]);

            $html = '<html><body>'
                . '<p>Name : ' . esc($name) . '</p>'
                . '<p>Email : ' . esc($email) . '</p>'
                . '<p>Mobile No. : ' . esc($phone) . '</p>'
                . '<p>Interested in : ' . esc($interestedIn) . '</p>'
                . '</body></html>';

            $this->mailer->send(
                ['info@coldwellbanker.in'],
                'Lead for Coldwell Banker India (Landing Page)',
                $html,
                $email,
                $name,
            );

            return $this->response->setContentType('text/plain')->setBody('1');
        }

        $name    = trim((string) ($post['name'] ?? ''));
        $email   = trim((string) ($post['email'] ?? ''));
        $phone   = trim((string) ($post['phone'] ?? ''));
        $message = trim((string) ($post['message'] ?? ''));

        if ($name === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL) || $phone === '' || $message === '') {
            session()->setFlashdata('class', 'message_error');
            session()->setFlashdata('message', 'Please fill in all required fields with a valid email address.');

            return redirect()->to(site_url('contact-us') . '#cont-right');
        }

        model(ContactEnquiryModel::class)->addEnquiry([
            'name'    => $name,
            'email'   => $email,
            'phone'   => $phone,
            'message' => $message,
        ]);

        $html = '<html><body>'
            . '<p>Name : ' . esc($name) . '</p>'
            . '<p>Email : ' . esc($email) . '</p>'
            . '<p>Mobile No. : ' . esc($phone) . '</p>'
            . '<p>Message : ' . esc($message) . '</p>'
            . '</body></html>';

        $this->mailer->send(
            ['info@coldwellbanker.in'],
            'Contact Enquiry',
            $html,
            $email,
            $name,
        );

        session()->setFlashdata('class', 'message_success');
        session()->setFlashdata('message', 'Your enquiry has been successfully submitted!');

        return redirect()->to(site_url('contact-us') . '#cont-right');
    }

    /**
     * CI3: Home::contact_enquiry_popup (controllers/Home.php:457).
     * Handles the site-wide "Enquire Now" modal (`#enquire_popup` in `partials/sub_footer.php`).
     * Returns `'1'` on success as expected by `public/assets/js/form.js`.
     */
    public function contactEnquiryPopup(): ResponseInterface
    {
        $name     = trim($this->postString('name'));
        $email    = trim($this->postString('email'));
        $phone    = trim($this->postString('phone'));
        $interest = $this->request->getPost('interest');

        if (is_array($interest)) {
            $interestStr = implode(', ', array_filter(array_map('trim', $interest), static fn ($v) => $v !== ''));
        } else {
            $interestStr = trim((string) $interest);
        }

        if ($name === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL) || $phone === '' || $interestStr === '') {
            return $this->response->setContentType('text/plain')->setBody('0');
        }

        model(ContactEnquiryModel::class)->addEnquiry([
            'name'          => $name,
            'email'         => $email,
            'phone'         => $phone,
            'interested_in' => $interestStr,
        ]);

        $html = '<html><body>'
            . '<p>Name : ' . esc($name) . '</p>'
            . '<p>Email : ' . esc($email) . '</p>'
            . '<p>Mobile No. : ' . esc($phone) . '</p>'
            . '<p>Interest In : ' . esc($interestStr) . '</p>'
            . '</body></html>';

        $this->mailer->send(
            ['info@coldwellbanker.in'],
            'Lead details',
            $html,
            $email,
            $name,
        );

        return $this->response->setContentType('text/plain')->setBody('1');
    }

    /**
     * CI3: Home::franchisee_enquiry (controllers/Home.php:185).
     * Fixes `PUB-04`: removes the leftover `print_r($this->input->post);die;` at line 186
     * so submissions from `/our-franchise` (`#franchiseeForm`) are validated, stored in
     * `franchisee_enquiry`, emailed, and redirected back with the flash message.
     */
    public function franchiseeEnquiry(): ResponseInterface
    {
        $name     = trim($this->postString('name'));
        $email    = trim($this->postString('email'));
        $phone    = trim($this->postString('phone'));
        $industry = trim($this->postString('interested') ?: $this->postString('industry'));
        $location = trim($this->postString('loc') ?: $this->postString('location'));
        $profession = mb_substr(trim($this->postString('profession')), 0, 120);
        // CMS "Request a Call Back" block (source=cms): return to the same page's #callback form.
        $fromCms = $this->request->getPost('source') === 'cms';

        if ($name === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL) || $phone === '') {
            if ($fromCms) {
                session()->setFlashdata(['enquiry_msg' => 'Please fill in your name, a valid email address and phone number.', 'enquiry_ok' => false]);

                return redirect()->to($this->cmsNewsletterReturn() . '#callback');
            }
            if ($this->request->isAJAX()) {
                return $this->response->setContentType('text/plain')->setBody('Error');
            }
            session()->setFlashdata('class', 'message_error');
            session()->setFlashdata('message', 'Please fill in all required fields with a valid email address.');

            return redirect()->to(site_url('our-franchise') . '#franchisee_form');
        }

        model(FranchiseeEnquiryModel::class)->addEnquiry([
            'name'       => $name,
            'email'      => $email,
            'phone'      => $phone,
            'profession' => $profession,
            'industry'   => $industry,
            'location'   => $location,
        ]);

        $html = '<html><body>'
            . '<p>Name : ' . esc($name) . '</p>'
            . '<p>Email : ' . esc($email) . '</p>'
            . '<p>Mobile No. : ' . esc($phone) . '</p>'
            . ($profession !== '' ? '<p>Profession : ' . esc($profession) . '</p>' : '')
            . '<p>Interested in : ' . esc($industry) . '</p>'
            . '<p>Location : ' . esc($location) . '</p>'
            . '</body></html>';

        $this->mailer->send(
            ['info@coldwellbanker.in', 'pradeep.maurya@coldwellbanker.in'],
            'Lead for Coldwell Banker Franchisee',
            $html,
            $email,
            $name,
        );

        if ($fromCms) {
            session()->setFlashdata(['enquiry_msg' => 'Thank you — our franchise team will call you back shortly.', 'enquiry_ok' => true]);

            return redirect()->to($this->cmsNewsletterReturn() . '#callback');
        }
        if ($this->request->isAJAX()) {
            return $this->response->setContentType('text/plain')->setBody('Success');
        }

        session()->setFlashdata('class', 'message_success');
        session()->setFlashdata('message', 'Your enquiry has been successfully submitted!');

        return redirect()->to(site_url('our-franchise') . '#franchisee_form');
    }

    /**
     * CI3: Home::franchisee_details_enquiry($id) (controllers/Home.php:312).
     * Handles the office enquiry form (`#franchisee_contact`) on `/our-franchise-details/{slug}/{id}`.
     * Fixes `PUB-11` / `RS-10`: validates the redirect target so `HTTP_REFERER` cannot trigger an
     * external open redirect.
     */
    public function franchiseeDetailsEnquiry(string $id): ResponseInterface
    {
        $franchiseId = (int) $id;
        $franchise   = $franchiseId > 0 ? model(FranchisesModel::class)->find($franchiseId) : null;

        $fname    = trim($this->postString('fname'));
        $lname    = trim($this->postString('lname'));
        $name     = trim($fname . ' ' . $lname);
        $email    = trim($this->postString('email'));
        $phone    = trim($this->postString('phone'));
        $interest = $this->request->getPost('interest');

        if (is_array($interest)) {
            $interestStr = implode(', ', array_filter(array_map('trim', $interest), static fn ($v) => $v !== ''));
        } else {
            $interestStr = trim((string) $interest);
        }

        $redirectUrl = $this->safeFranchiseDetailsRedirect($franchiseId, $franchise);

        if ($fname === '' || $lname === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL) || $phone === '' || $interestStr === '') {
            session()->setFlashdata('class', 'message_error');
            session()->setFlashdata('message', 'Please fill in all required fields with a valid email address.');

            return redirect()->to($redirectUrl);
        }

        model(FranchiseeProjectEnquiryModel::class)->addEnquiry([
            'franchisee_id' => $franchiseId,
            'name'          => $name,
            'email_id'      => $email,
            'phone'         => $phone,
            'interested_in' => $interestStr,
            'created_date'  => date('Y-m-d H:i:s'),
        ]);

        $franchiseTitle = (string) ($franchise['title'] ?? 'Franchisee #' . $franchiseId);
        $html           = '<html><body>'
            . '<p>Name : ' . esc($name) . '</p>'
            . '<p>Email : ' . esc($email) . '</p>'
            . '<p>Mobile No. : ' . esc($phone) . '</p>'
            . '<p>Interested in : ' . esc($interestStr) . '</p>'
            . '</body></html>';

        $this->mailer->send(
            ['info@coldwellbanker.in'],
            'Enquiry For ' . $franchiseTitle,
            $html,
            $email,
            $name,
        );

        session()->setFlashdata('class', 'message_success');
        session()->setFlashdata('message', 'Your enquiry has been successfully submitted!');

        return redirect()->to($redirectUrl);
    }

    /**
     * CI3: Home::nri_enquiry (controllers/Home.php:267).
     * Handles the NRI desk form (`#nri_enquiry` on `/nri`) submitted via AJAX in `form.js`.
     * Returns `'Success'` on success.
     */
    public function nriEnquiry(): ResponseInterface
    {
        $name    = trim($this->postString('name'));
        $email   = trim($this->postString('email'));
        $phone   = trim($this->postString('phone'));
        $message = trim($this->postString('message'));
        $country = trim($this->postString('nri_country'));

        if ($name === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL) || $phone === '' || $message === '' || $country === '') {
            return $this->response->setContentType('text/plain')->setBody('Error');
        }

        model(NriEnquiryModel::class)->addEnquiry([
            'name'       => $name,
            'email'      => $email,
            'phone'      => $phone,
            'message'    => $message,
            'country'    => $country,
            'created_on' => date('Y-m-d H:i:s'),
        ]);

        $html = '<html><body>'
            . '<p>Name : ' . esc($name) . '</p>'
            . '<p>Email : ' . esc($email) . '</p>'
            . '<p>Mobile No. : ' . esc($phone) . '</p>'
            . '<p>Country : ' . esc($country) . '</p>'
            . '<p>Message : ' . esc($message) . '</p>'
            . '</body></html>';

        $this->mailer->send(
            ['info@coldwellbanker.in'],
            'NRI Lead details',
            $html,
            $email,
            $name,
        );

        return $this->response->setContentType('text/plain')->setBody('Success');
    }

    /**
     * CI3: Home::newsletter_subscription (controllers/Home.php:250).
     * Fixes `DB-01`: populates `created_date` so new rows never default to `0000-00-00 00:00:00`.
     */
    public function newsletterSubscription(): ResponseInterface
    {
        $email = trim($this->postString('email'));
        // The redesigned home page's newsletter band posts source=home and expects to return
        // to its #newsletter anchor (allow-list, never a free redirect target).
        $fromHome = $this->request->getPost('source') === 'home';
        $back     = site_url('/');
        // CMS pages post source=cms and return to the page they came from: only a
        // same-origin site path taken from the referer, otherwise the home page.
        if ($this->request->getPost('source') === 'cms') {
            $fromHome = true;
            $back     = $this->cmsNewsletterReturn();
        }

        if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            if ($fromHome) {
                session()->setFlashdata(['newsletter_msg' => 'Please enter a valid email address.', 'newsletter_ok' => false]);

                return redirect()->to($back . '#newsletter');
            }
            session()->setFlashdata('class', 'message_error');
            session()->setFlashdata('message', 'Please enter a valid email address.');

            return redirect()->to(site_url('newsletter') . '#media');
        }

        model(NewsletterSubscriptionsModel::class)->addSubscription([
            'email'        => $email,
            'created_date' => date('Y-m-d H:i:s'),
        ]);

        if ($fromHome) {
            session()->setFlashdata(['newsletter_msg' => 'Thank you — you are subscribed to Coldwell Banker India updates.', 'newsletter_ok' => true]);

            return redirect()->to($back . '#newsletter');
        }
        session()->setFlashdata('class', 'message_success');
        session()->setFlashdata('message', 'Your enquiry has been successfully submitted!');

        return redirect()->to(site_url('newsletter') . '#media');
    }

    /** Same-origin CMS page path from the referer (no query/fragment), else the home page URL. */
    private function cmsNewsletterReturn(): string
    {
        $referer = trim($this->request->getHeaderLine('Referer') ?: (string) $this->request->getServer('HTTP_REFERER'));
        $base    = rtrim(site_url('/'), '/') . '/';
        if ($referer === '' || ! str_starts_with($referer, $base)) {
            return site_url('/');
        }
        $rest = substr((string) parse_url($referer, PHP_URL_PATH), strlen((string) parse_url($base, PHP_URL_PATH)));

        return preg_match('#^[a-z0-9][a-z0-9-]{0,189}$#', $rest) ? site_url($rest) : site_url('/');
    }

    /**
     * CI3: Home::job_applications (controllers/Home.php:594) and Ui_model::add_job_application (Ui_model.php:185).
     *
     * Fixes:
     *   - `SEC-01` / `PUB-03` / `DL-02`: Validates resume file using `finfo` (`getMimeType()`) + client
     *     extension allowlist (`pdf`, `doc`, `docx`, `rtf`) + 2 MB size limit, and stores it under a
     *     server-generated filename (`time() . mt_rand() . '.' . $ext`) in `public/uploads/resume/`.
     *   - `DL-05` / `PUB-21`: Validates all form fields and the resume file *before* inserting into
     *     `job_applications`, so failed uploads or invalid requests never create orphan rows with `resume = ''`.
     *   - `PUB-17`: Replaces PHP 8.1-deprecated `FILTER_SANITIZE_STRING`.
     */
    public function jobApplications(): ResponseInterface
    {
        $firstName = trim($this->postString('applicant_fname'));
        $lastName  = trim($this->postString('applicant_lname'));
        $gender    = strtolower(trim($this->postString('gender')));
        $mobile    = trim($this->postString('applicant_mobile'));
        $phone     = trim($this->postString('applicant_phone'));
        $email     = trim($this->postString('applicant_email'));
        $address   = trim($this->postString('applicant_address'));
        $jobId     = (int) $this->request->getPost('job_id');

        if (
            $firstName === ''
            || $lastName === ''
            || ! in_array($gender, ['male', 'female'], true)
            || $mobile === ''
            || ! filter_var($email, FILTER_VALIDATE_EMAIL)
            || $address === ''
            || $jobId <= 0
        ) {
            return $this->response->setContentType('text/plain')->setBody('error');
        }

        $file = $this->request->getFile('resume_file');
        if ($file === null || ! $file->isValid() || $file->hasMoved()) {
            return $this->response->setContentType('text/plain')->setBody('error');
        }

        $size = $file->getSize();
        if ($size <= 0 || $size >= self::RESUME_MAX_BYTES) {
            return $this->response->setContentType('text/plain')->setBody('error');
        }

        $clientExt = strtolower($file->getClientExtension());
        $mimeType  = $file->getMimeType();
        if (
            ! in_array($clientExt, self::RESUME_EXTENSIONS, true)
            || ! isset(self::RESUME_MIMES[$mimeType])
        ) {
            return $this->response->setContentType('text/plain')->setBody('error');
        }

        $safeExt   = self::RESUME_MIMES[$mimeType];
        $uploadDir = FCPATH . 'uploads/resume';
        if (! is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $newFilename = time() . mt_rand(1000, 999999) . '.' . $safeExt;
        if (! $file->move($uploadDir, $newFilename)) {
            return $this->response->setContentType('text/plain')->setBody('error');
        }

        $applicationsModel = model(JobApplicationsModel::class);
        $insertId          = $applicationsModel->createApplication([
            'job_id'     => $jobId,
            'first_name' => $firstName,
            'last_name'  => $lastName,
            'gender'     => ucfirst($gender),
            'mobile_no'  => $mobile,
            'phone_no'   => $phone,
            'email_id'   => $email,
            'address'    => $address,
            'status'     => 1,
        ]);
        $applicationsModel->setResume($insertId, $newFilename);

        $job          = model(JobsModel::class)->findJob($jobId);
        $positionName = (string) ($job['position'] ?? 'Position #' . $jobId);
        $fullName     = trim($firstName . ' ' . $lastName);

        $html = '<html><body>'
            . '<p>Position : ' . esc($positionName) . '</p>'
            . '<p>Name : ' . esc($fullName) . '</p>'
            . '<p>Gender : ' . esc(ucfirst($gender)) . '</p>'
            . '<p>Mobile No. : ' . esc($mobile) . '</p>'
            . '<p>Phone No : ' . esc($phone) . '</p>'
            . '<p>Email : ' . esc($email) . '</p>'
            . '<p>Address : ' . esc($address) . '</p>'
            . '</body></html>';

        $this->mailer->send(
            ['pujan.majumdar@coldwellbanker.in'],
            'Job Application Details',
            $html,
            $email,
            $fullName,
            $uploadDir . DIRECTORY_SEPARATOR . $newFilename,
        );

        return $this->response->setContentType('text/plain')->setBody('success');
    }

    /**
     * Computes a safe internal redirect URL for `franchiseeDetailsEnquiry()` (`PUB-11` / `RS-10`).
     * Only honours `HTTP_REFERER` when it begins with our own `base_url()`.
     */
    private function safeFranchiseDetailsRedirect(int $franchiseId, ?array $franchise): string
    {
        $referer = trim((string) $this->request->getServer('HTTP_REFERER'));
        $base    = rtrim(base_url(), '/');

        if ($referer !== '' && str_starts_with($referer, $base . '/')) {
            return $referer;
        }

        $title = strtolower((string) ($franchise['title'] ?? ''));
        $slug  = preg_replace('/[^a-z0-9\-]/', '', str_replace(' ', '-', $title)) ?? '';
        $slug  = trim((string) preg_replace('/-+/', '-', $slug), '-');
        $seg   = $slug !== '' ? 'coldwell-banker-' . $slug : 'coldwell-banker';

        return site_url('our-franchise-details/' . $seg . '/' . $franchiseId);
    }

    /**
     * A POST field as a string; arrays (e.g. email[]=x) become '' instead of raising
     * "Array to string conversion" (HTTP 500) before validation runs.
     */
    private function postString(string $key): string
    {
        $v = $this->request->getPost($key);

        return is_string($v) ? $v : '';
    }
}
