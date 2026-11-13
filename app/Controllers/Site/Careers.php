<?php

namespace App\Controllers\Site;

use App\Controllers\BaseController;
use App\Models\CareersTestimonialsModel;
use App\Models\CorporateTeamsModel;
use App\Models\JobsModel;
use App\Models\LifeAtColdwellBankerImagesModel;
use App\Models\LifeAtColdwellBankerModel;
use App\Models\PagesModel;
use App\Models\SuccessStoryModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Career / corporate team pages.
 * CI3: controllers/Home.php (career, franchisees_openings, apply_job_opening,
 * campus_recruitment, corporate_team, get_team_information).
 */
class Careers extends BaseController
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
     * Data shared by the two openings pages (CI3 in-view queries of
     * views/career.php and views/franchisees_openings.php).
     *
     * @return array<string, mixed>
     */
    protected function openingsData(string $jobType): array
    {
        return [
            'page_info'        => $this->pageInfo(PagesModel::PAGE_CAREER),
            'life_at_coldwell' => model(LifeAtColdwellBankerModel::class)->firstEntry(),
            'array_collection' => model(JobsModel::class)->activeByType($jobType),
            'array_success'    => model(SuccessStoryModel::class)->activeLatestFirst(),
            'life_at_images'   => model(LifeAtColdwellBankerImagesModel::class)->allImages(),
        ];
    }

    /** CI3: Home::career (controllers/Home.php:146) */
    public function career(): string
    {
        $data                = $this->openingsData(JobsModel::TYPE_COLDWELL);
        $data['page_active'] = 'career';
        // CI3 views passed $data1['act'] = $page_active to the navigation partial,
        // which uses it to mark the "Careers" menu item active.
        $data['act']         = $data['page_active'];

        return view('site/career', $data);
    }

    /** CI3: Home::franchisees_openings (controllers/Home.php:582) */
    public function franchiseesOpenings(): string
    {
        $data                       = $this->openingsData(JobsModel::TYPE_FRANCHISEE);
        $data['array_testimonials'] = model(CareersTestimonialsModel::class)->allTestimonials();
        $data['page_active']        = 'Franchisees Openings';
        $data['act']                = $data['page_active']; // navigation partial ("Careers" active)

        return view('site/franchisees_openings', $data);
    }

    /**
     * CI3: Home::apply_job_opening($id) (controllers/Home.php:507).
     * Broken in CI3 for every id (HTTP 500): it calls the undefined
     * Ui_model::current_openings() and loads the missing view "apply".
     * Kept as a 404 instead of being turned into a new page.
     */
    public function applyJobOpening(string $id = ''): never
    {
        throw PageNotFoundException::forPageNotFound();
    }

    /**
     * CI3: Home::campus_recruitment (controllers/Home.php:520).
     * Broken in CI3 (HTTP 500): it calls the undefined Ui_model::campus_recruitment()
     * and loads the missing view "campus_recruitment". Kept as a 404.
     */
    public function campusRecruitment(): never
    {
        throw PageNotFoundException::forPageNotFound();
    }

    /** CI3: Home::corporate_team (controllers/Home.php:132) */
    public function corporateTeam(): string
    {
        return view('site/corporate_team', [
            'page_info'        => $this->pageInfo(PagesModel::PAGE_CORPORATE_TEAM),
            'page_active'      => 'corporate team',
            'act'              => 'corporate team', // navigation partial ("Corporate Team" active)
            'array_collection' => model(CorporateTeamsModel::class)->activeOrdered(),
        ]);
    }

    /**
     * CI3: Home::get_team_information (controllers/Home.php:531), AJAX (POST user_id)
     * from views/corporate_team.php. Returns the lightbox HTML for one member.
     * An unknown id is a 404 (CI3 printed PHP warnings around empty markup).
     */
    public function getTeamInformation(): ResponseInterface
    {
        $id      = (int) trim((string) $this->request->getPost('user_id'));
        $results = model(CorporateTeamsModel::class)->memberById($id);
        if ($results === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        // Same markup as CI3, including its CRLF line breaks (Home.php is a CRLF file and
        // the template was a multi-line string literal). description is admin-authored
        // rich HTML (WYSIWYG), output raw as in CI3; name/title are plain text.
        $html = implode("\r\n", [
            '',
            "\t\t" . '<div class="team_mem"><img src="' . base_url() . 'uploads/corporate_team/' . esc($results['image'], 'url') . '"></div>',
            "\t\t" . '<div class="member_details">',
            "\t\t\t" . '<h2>' . esc($results['name']) . '<br>',
            "\t\t\t" . '<span>' . esc($results['title']) . '</span></h2>',
            "\t\t\t" . '<div class="light_divider"></div>',
            "\t\t\t" . $results['description'],
            "\t\t\t" . '<div class="light_divider"></div>',
            "\t\t" . '</div>',
        ]);

        return $this->response->setBody($html);
    }
}
