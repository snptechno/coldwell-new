<?php

namespace App\Controllers\Site;

use App\Controllers\BaseController;
use App\Models\CitiesModel;
use App\Models\FranchisesModel;
use App\Models\FranchisesOwnersModel;
use App\Models\FranchisesTestimonialsModel;
use App\Models\PagesModel;
use App\Models\RealEstateSpecialistModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Franchise (our offices) pages and their AJAX endpoints.
 * CI3: controllers/Home.php (our_franchise, map, our_franchise_details,
 * get_owner_info, get_specialist_info, get_franchisee_location_by_city,
 * search_franchisee_by_location, get_franchisee_name, get_all_franchisee_location,
 * partner_with_us, franchise_details, clean).
 */
class Franchise extends BaseController
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
     * Flash message of the enquiry forms (set by the POST handlers, ported later),
     * read as the CI3 views did with $this->session->flashdata().
     *
     * @return array{flash_class: string, flash_message: string}
     */
    protected function flash(): array
    {
        $session = session();

        return [
            'flash_class'   => (string) $session->getFlashdata('class'),
            'flash_message' => (string) $session->getFlashdata('message'),
        ];
    }

    /** CI3: Home::our_franchise (controllers/Home.php:62), view our_franchisee */
    public function ourFranchise(): string
    {
        $cities     = model(CitiesModel::class)->withActiveFranchises();
        $franchises = model(FranchisesModel::class);
        $byCity     = [];
        foreach ($cities as $city) {
            $byCity[$city['id']] = $franchises->activeByCity((int) $city['id']);
        }

        return view('site/our_franchisee', [
            'page_info'          => $this->pageInfo(PagesModel::PAGE_OUR_FRANCHISEE),
            'page_active'        => 'our franchisee',
            'rz1'                => $cities, // city dropdown (views/our_franchisee.php:38)
            'rz2'                => $cities, // city grid (views/our_franchisee.php:143), same rows
            'franchises_by_city' => $byCity, // views/our_franchisee.php:152
            'clean'              => $this->clean(...),
        ] + $this->flash());
    }

    /**
     * CI3: Home::map (controllers/Home.php:68).
     * Broken in CI3 (HTTP 500): it loads the missing view "map". Kept as a 404.
     */
    public function map(): never
    {
        throw PageNotFoundException::forPageNotFound();
    }

    /**
     * CI3: Home::partner_with_us (controllers/Home.php:771).
     * Broken in CI3 (HTTP 500): it loads the missing view "partner_with_us". Kept as a 404.
     */
    public function partnerWithUs(): never
    {
        throw PageNotFoundException::forPageNotFound();
    }

    /**
     * CI3: Home::franchise_details (controllers/Home.php:777).
     * Broken in CI3 (HTTP 500): it loads the missing view "franchise_details". Kept as a 404.
     */
    public function franchiseDetails(): never
    {
        throw PageNotFoundException::forPageNotFound();
    }

    /**
     * CI3: Home::our_franchise_details($name, $id) (controllers/Home.php:75),
     * view our_franchisee_details. The name segment is ignored, as before;
     * an unknown id is a 404 (CI3: show_404()).
     */
    public function ourFranchiseDetails(string $name, string $id): string
    {
        $rz1 = model(FranchisesModel::class)->detailsWithOwner((int) $id);
        if ($rz1 === null) {
            throw PageNotFoundException::forPageNotFound();
        }
        $franchiseId = (int) $rz1['id'];

        return view('site/our_franchisee_details', [
            'page_info'    => $this->pageInfo(PagesModel::PAGE_OUR_FRANCHISEE_DETAILS),
            'page_active'  => 'our franchisee',
            'id'           => $franchiseId,
            'rz1'          => $rz1,
            'testimonials' => model(FranchisesTestimonialsModel::class)->activeByFranchise($franchiseId),
            'rz2'          => model(FranchisesOwnersModel::class)->byFranchise($franchiseId),
            'rz4'          => model(RealEstateSpecialistModel::class)->byFranchiseOrderedByName($franchiseId),
        ] + $this->flash());
    }

    /**
     * CI3's controllers/Home.php is saved with CRLF line endings, and the line breaks
     * inside its HTML string literals are part of the AJAX responses. Literal template
     * text goes through this so the responses stay byte-identical (DB values do not).
     */
    private static function crlf(string $literal): string
    {
        return str_replace("
", "
", $literal);
    }

    /**
     * Lightbox HTML of one owner / specialist (CI3: get_owner_info, get_specialist_info).
     * description is admin-authored rich HTML (WYSIWYG), output raw as in CI3.
     */
    private function memberHtml(string $folder, array $results, string $subtitleKey): string
    {
        return sprintf(self::crlf('
		<div class="team_mem"><img src="%s"></div>
		<div class="member_details">
			<h2>%s<br>
			<span>%s</span></h2>
			<div class="light_divider"></div>
			%s
			<div class="light_divider"></div>
		</div>'),
            base_url() . 'uploads/' . $folder . '/' . esc($results['image'], 'url'),
            esc($results['name']),
            esc($results[$subtitleKey]),
            $results['description'],
        );
    }

    /**
     * CI3: Home::get_owner_info (controllers/Home.php:548), AJAX (POST user_id)
     * from views/our_franchisee_details.php. Returns the lightbox HTML for one owner.
     * An unknown id is a 404 (CI3 printed PHP warnings around empty markup).
     */
    public function getOwnerInfo(): ResponseInterface
    {
        $id      = (int) trim((string) $this->request->getPost('user_id'));
        $results = $id > 0 ? model(FranchisesOwnersModel::class)->find($id) : null;
        if ($results === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        return $this->response->setBody($this->memberHtml('franchise_owners', $results, 'position_name'));
    }

    /**
     * CI3: Home::get_specialist_info (controllers/Home.php:565), AJAX (POST user_id)
     * from views/our_franchisee_details.php. Returns the lightbox HTML for one specialist.
     * An unknown id is a 404 (CI3 printed PHP warnings around empty markup).
     */
    public function getSpecialistInfo(): ResponseInterface
    {
        $id      = (int) trim((string) $this->request->getPost('user_id'));
        $results = $id > 0 ? model(RealEstateSpecialistModel::class)->find($id) : null;
        if ($results === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        return $this->response->setBody($this->memberHtml('specialist', $results, 'title'));
    }

    /**
     * CI3: Home::get_franchisee_location_by_city (controllers/Home.php:789),
     * AJAX (POST city_id) from views/our_franchisee.php. Returns the <option> list.
     */
    public function getFranchiseeLocationByCity(): ResponseInterface
    {
        $cityId = (int) $this->request->getPost('city_id');
        $result = model(FranchisesModel::class)->activeLocationsByCity($cityId);

        $str = '<option value="">Location</option>';
        foreach ($result as $row) {
            $str .= '<option value="' . esc($row['location']) . '">' . esc($row['location']) . '</option>';
        }

        return $this->response->setBody($str);
    }

    /**
     * Slug helper (CI3: Home::clean, controllers/Home.php:802, which was public and so
     * reachable as /clean/<x>; also re-declared inside views/our_franchisee.php).
     */
    private function clean(string $string): string
    {
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
        $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.

        return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
    }

    /**
     * Map marker entry "['title', lat, long, 'address', 'slug', id]". This is a data
     * protocol, not HTML: the page JS turns ' into " and JSON.parse()s it, so the
     * values are kept exactly as CI3 wrote them.
     */
    private function mapMarker(array $line, string $franName): string
    {
        $address = trim(strip_tags((string) $line['address']));

        return "['" . $line['title'] . "', " . $line['latitude'] . ', ' . $line['longitude'] . ", '" . $address . "', '" . $franName . "', " . $line['id'] . ']';
    }

    /** "coldwell-banker-<slug>" part of the details URL, as CI3 built it. */
    private function franchiseSlug(string $title): string
    {
        return str_replace(['\'', ' '], '-', $this->clean(strtolower($title)));
    }

    /**
     * CI3: Home::search_franchisee_by_location (controllers/Home.php:809), AJAX from
     * views/our_franchisee.php: POST search_id=1&city_id=..&location_name=.. (city search)
     * or search_id=2&search_by=.. (name search). Returns JSON {map_arr, info, action}
     * (sent as text/html like CI3: the page JS calls $.parseJSON on the raw text), or []
     * when nothing matches. Any other search_id built no query in CI3 (error); it now
     * returns the empty result.
     */
    public function searchFranchiseeByLocation(): ResponseInterface
    {
        $post     = $this->request->getPost();
        $cityId   = null;
        $location = null;
        $title    = '';

        if (isset($post['city_id'])) {
            $cityId   = $post['city_id'];
            $location = $post['location_name'] ?? null;
        }
        if (isset($post['search_by'])) {
            $cityId   = null;
            $location = null;
            $title    = $post['search_by'];
        }
        $searchId = $post['search_id'] ?? null;

        $franchises = model(FranchisesModel::class);
        $result     = [];
        if ($searchId == 1) {
            $result = $franchises->searchByCity((int) $cityId, $location === null ? null : (string) $location);
        } elseif ($searchId == 2) {
            $result = $franchises->searchByTitleOrLocation((string) $title);
        }

        if (empty($result)) {
            return $this->response->setBody(json_encode([]));
        }

        $mapLoc     = [];
        $str        = '';
        $arrayPart  = array_chunk($result, 4);

        if ($searchId == 1) {
            $str .= self::crlf('<div class="our-frnch-main" id="list_fra">
			<div class="ahm-box " city-name="" id="') . esc($this->clean($arrayPart[0][0]['city_name'])) . self::crlf('">
			<h3><span class="">') . esc($arrayPart[0][0]['city_name']) . '</span></h3>';
        }
        $j = 0;
        foreach ($arrayPart as $row) {
            $str .= '<div class="itms-row">';
            if ($j == 0) {
                $str .= '<div class="top-rod"></div>';
            } else {
                $str .= '<div class="top-rod m-t-40"></div>';
            }

            foreach ($row as $line) {
                $str .= self::crlf('<div class="frch-item">
						<div class="item-tit">
						 	<img src="') . base_url() . self::crlf('assets/images/cb-white-icon.png">
							<span>') . esc($line['title']) . self::crlf('</span>
							<small>') . esc($line['location']) . '</small>';

                if ($line['franchise_status'] == 'Coming Soon') {
                    $str .= ' <span class="cm-soon">Coming Soon</span>';
                }

                if ($line['franchise_status'] == 'External Link') {
                    $str .= '<a href="' . esc($line['external_link']) . '" target="_blank"></a>';
                }

                $franName = $this->franchiseSlug((string) $line['title']);
                if ($line['franchise_status'] == 'Exits') {
                    $str .= '<a href="' . base_url() . 'our-franchise-details/coldwell-banker-' . esc($franName, 'url') . '/' . esc($line['id'], 'url') . '"></a>';
                }
                $str .= self::crlf('</div>
					</div>');
                if ($line['latitude'] != '' && $line['longitude'] != '') {
                    $mapLoc[] = $this->mapMarker($line, $franName);
                }
            }
            $str .= '</div>';
            $j++;
        }
        if ($searchId == 1) {
            $str .= '</div>';
            $str .= '</div>';
        }

        return $this->response->setBody(json_encode([
            'map_arr' => $mapLoc,
            'info'    => $str,
            'action'  => ['search'],
        ]));
    }

    /**
     * CI3: Home::get_franchisee_name (controllers/Home.php:904), AJAX (POST search_by)
     * from views/our_franchisee.php (name autocomplete). Returns a <ul> list.
     */
    public function getFranchiseeName(): ResponseInterface
    {
        $title  = (string) $this->request->getPost('search_by');
        $result = model(FranchisesModel::class)->activeTitlesLike($title);

        $str = '<ul>';
        foreach ($result as $row) {
            $str .= '<li value="' . esc($row['location']) . '">' . esc($row['title']) . ', ' . esc($row['location']) . '</li>';
        }
        $str .= '</ul>';

        return $this->response->setBody($str);
    }

    /**
     * CI3: Home::get_all_franchisee_location (controllers/Home.php:918), AJAX (POST, no
     * parameters) run on load by views/our_franchisee.php to fill the map view.
     * Returns JSON {map_arr, action: ['onload']} as text/html (the JS $.parseJSON()s it);
     * an empty body when there is no active franchise, as before.
     */
    public function getAllFranchiseeLocation(): ResponseInterface
    {
        $result = model(FranchisesModel::class)->allActive();
        if (empty($result)) {
            return $this->response->setBody('');
        }

        $mapLoc = [];
        foreach ($result as $line) {
            if ($line['latitude'] != '' && $line['longitude'] != '') {
                $mapLoc[] = $this->mapMarker($line, $this->franchiseSlug((string) $line['title']));
            }
        }

        return $this->response->setBody(json_encode([
            'map_arr' => $mapLoc,
            'action'  => ['onload'],
        ]));
    }
}
