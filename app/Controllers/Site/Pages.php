<?php

namespace App\Controllers\Site;

use App\Controllers\BaseController;
use App\Models\PagesModel;
use App\Models\ResearchModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Static/CMS pages whose content comes from the `pages` table.
 * CI3: controllers/Home.php (privacy, terms, disclaimer, faq, refund, ...).
 */
class Pages extends BaseController
{
    /**
     * Loads a `pages` row as an object, as the CI3 views expect ($page_info->title),
     * and 404s if it is missing (CI3 would have crashed on a null row).
     */
    protected function pageInfo(int $id): object
    {
        $row = model(PagesModel::class)->pageById($id);
        if ($row === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        return (object) $row;
    }

    /**
     * The enquiry flash message the CI3 views printed ($this->session->flashdata('class'/'message')),
     * set by the enquiry POST handlers.
     *
     * @return array{flash_class: string, flash_message: string}
     */
    protected function enquiryFlash(): array
    {
        $session = session();

        return [
            'flash_class'   => (string) ($session->getFlashdata('class') ?? ''),
            'flash_message' => (string) ($session->getFlashdata('message') ?? ''),
        ];
    }

    /** CI3: Home::faq (controllers/Home.php:974) */
    public function faq(): string
    {
        return view('site/faq', [
            'page_info'   => $this->pageInfo(PagesModel::PAGE_FAQ),
            'page_active' => 'faq',
        ]);
    }

    /** CI3: Home::privacy_policy (Home.php:943) and Home::privacy (Home.php:955), same page. */
    public function privacyPolicy(): string
    {
        return view('site/privacy_policy', [
            'page_info'   => $this->pageInfo(PagesModel::PAGE_PRIVACY_POLICY),
            'page_active' => 'privacy_policy',
        ]);
    }

    /** CI3: Home::terms_and_conditions (Home.php:949) and Home::terms (Home.php:961), same page. */
    public function termsAndConditions(): string
    {
        return view('site/terms_and_conditions', [
            'page_info'   => $this->pageInfo(PagesModel::PAGE_TERMS_AND_CONDITIONS),
            'page_active' => 'terms_and_conditions',
        ]);
    }

    /** CI3: Home::disclaimer (Home.php:967) */
    public function disclaimer(): string
    {
        return view('site/disclaimer', [
            'page_info'   => $this->pageInfo(PagesModel::PAGE_DISCLAIMER),
            'page_active' => 'disclaimer',
        ]);
    }

    /** CI3: Home::refund (Home.php:981) */
    public function refund(): string
    {
        return view('site/refund', [
            'page_info'   => $this->pageInfo(PagesModel::PAGE_REFUND),
            'page_active' => 'refund',
        ]);
    }

    /**
     * CI3: Home::quick_pay (Home.php:987). CI3 loaded Ui_model::quick_pay() (pages id 24) but the
     * view has its title/meta echoes commented out, so the row was never used; it is not read here.
     */
    public function quickPay(): string
    {
        return view('site/quick_pay', [
            'page_active' => 'quick_pay',
        ]);
    }

    /** CI3: Home::about_coldwell_banker (Home.php:166) */
    public function aboutColdwellBanker(): string
    {
        return view('site/about_coldwell_banker', [
            'page_info'   => $this->pageInfo(PagesModel::PAGE_ABOUT_US),
            'page_active' => 'about us',
        ]);
    }

    /** CI3: Home::area_insights (Home.php:153); the in-view query (views/area_insights.php:50) moved to ResearchModel. */
    public function areaInsights(): string
    {
        return view('site/area_insights', [
            'page_info'   => $this->pageInfo(PagesModel::PAGE_RESEARCH),
            'page_active' => 'research',
            'rz1'         => model(ResearchModel::class)->activeByType('Area Insights'),
        ]);
    }

    /** CI3: Home::nri (Home.php:174) */
    public function nri(): string
    {
        return view('site/nri', [
            'page_info'   => $this->pageInfo(PagesModel::PAGE_NRI),
            'page_active' => 'nri',
        ] + $this->enquiryFlash());
    }

    /** CI3: Home::contact_us (Home.php:241), GET page only (the form posts to contact_enquiry, a later step). */
    public function contactUs(): string
    {
        return view('site/contact_us', [
            'page_info'   => $this->pageInfo(PagesModel::PAGE_CONTACT_US),
            'page_active' => 'contact us',
        ] + $this->enquiryFlash());
    }

    /** CI3: Home::blog (Home.php:765): redirect(base_url().'blogs/articles/'), which CI3 sends as 307 for GET. */
    public function blog(): ResponseInterface
    {
        return redirect()->to(base_url() . 'blogs/articles/', 307);
    }

    /**
     * CI3: Home::test_page (Home.php:783) loaded views/test-page.php, which does not exist,
     * so the URL answered HTTP 500. It is a proper 404 now.
     */
    public function testPage(): string
    {
        throw PageNotFoundException::forPageNotFound();
    }
}
