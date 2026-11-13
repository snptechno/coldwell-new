<?php

namespace App\Controllers\Site;

use App\Controllers\BaseController;
use App\Models\CbIndiaVideosModel;
use App\Models\FeaturedVideosModel;
use App\Models\FindingHomeVideosModel;
use App\Models\PagesModel;
use App\Models\SellingHomeVideosModel;
use App\Models\TestimonialVideosModel;
use CodeIgniter\Exceptions\PageNotFoundException;

/**
 * Public video gallery pages (finding / selling a home, testimonials, CB India, featured).
 * CI3: controllers/Home.php finding_a_home .. featured_videos (Home.php:27-60).
 */
class Videos extends BaseController
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
     * Renders one video page. $videos: the active rows (arrays) of the page's table,
     * as the CI3 view's in-view result_array() query returned them.
     *
     * @param list<array<string, mixed>> $videos
     */
    protected function render(string $view, int $pageId, array $videos): string
    {
        return view('site/' . $view, [
            'page_info'   => $this->pageInfo($pageId),
            'page_active' => 'videos',
            // CI3 views passed $data1['act'] = $page_active to the navigation partial,
            // which uses it to mark the "Videos" menu item active.
            'act'         => 'videos',
            'videos'      => $videos,
        ]);
    }

    /** CI3: Home::finding_a_home (controllers/Home.php:27), view finding_a_home.php:59 query */
    public function findingAHome(): string
    {
        return $this->render('finding_a_home', PagesModel::PAGE_FINDING_A_HOME, model(FindingHomeVideosModel::class)->activeVideos());
    }

    /** CI3: Home::selling_a_home (controllers/Home.php:34), view selling_a_home.php:61 query */
    public function sellingAHome(): string
    {
        return $this->render('selling_a_home', PagesModel::PAGE_VIDEOS, model(SellingHomeVideosModel::class)->activeVideos());
    }

    /** CI3: Home::testimonials (controllers/Home.php:41), view testimonials.php:60 query */
    public function testimonials(): string
    {
        return $this->render('testimonials', PagesModel::PAGE_VIDEOS, model(TestimonialVideosModel::class)->activeVideosLatestFirst());
    }

    /** CI3: Home::cb_india_videos (controllers/Home.php:48), view cb_india_videos.php:60 query */
    public function cbIndiaVideos(): string
    {
        return $this->render('cb_india_videos', PagesModel::PAGE_VIDEOS, model(CbIndiaVideosModel::class)->activeVideos());
    }

    /** CI3: Home::featured_videos (controllers/Home.php:55), view featured_videos.php:60 query */
    public function featuredVideos(): string
    {
        return $this->render('featured_videos', PagesModel::PAGE_VIDEOS, model(FeaturedVideosModel::class)->activeVideos());
    }
}
