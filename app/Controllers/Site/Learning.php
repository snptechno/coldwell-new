<?php

namespace App\Controllers\Site;

use App\Controllers\BaseController;
use App\Models\EventsModel;
use App\Models\GalleryModel;
use App\Models\GalleryThumbsModel;
use App\Models\LearningModel;
use App\Models\PagesModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Learning & Development page and its gallery AJAX endpoint.
 * CI3: controllers/Home.php (learning_development, load_group_images_gallery).
 */
class Learning extends BaseController
{
    /**
     * The `learning` row the page shows (CI3 view hard-coded id 2: views/learning_development.php:51).
     */
    private const OVERVIEW_ID = 2;

    /** CI3: Home::learning_development (controllers/Home.php:88, re-enabled 2026-11-12) */
    public function learningDevelopment(): string
    {
        $page = model(PagesModel::class)->pageById(PagesModel::PAGE_LEARNING_DEVELOPMENT);
        if ($page === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        return view('site/learning_development', [
            'page_info'   => (object) $page,
            'page_active' => 'learning development',
            // views/learning_development.php:51 (row_array, may be empty)
            'rz2'         => model(LearningModel::class)->findOverview(self::OVERVIEW_ID) ?? [],
            // views/learning_development.php:110
            'events'      => model(EventsModel::class)->activeByStartDate(),
            // views/learning_development.php:159 (one thumbnail per gallery group)
            'rz3'         => model(GalleryThumbsModel::class)->firstActivePerGroup(),
            // views/learning_development.php:196 (lightbox images)
            'rz4'         => model(GalleryModel::class)->activeLightboxImages(),
        ]);
    }

    /**
     * CI3: Home::load_group_images_gallery (controllers/Home.php:753).
     * POST group_id -> one fancybox anchor per image of that group, as an HTML fragment.
     * CI3 concatenated group_id into the SQL, so a missing or non-numeric value was an
     * SQL error (HTTP 500); it is now a 404.
     */
    public function loadGroupImagesGallery(): ResponseInterface
    {
        $groupId = trim((string) $this->request->getPost('group_id'));
        if (preg_match('/^-?\d+$/', $groupId) !== 1) {
            throw PageNotFoundException::forPageNotFound();
        }

        $str = '';
        foreach (model(GalleryModel::class)->imagesByGroup((int) $groupId) as $row) {
            $str .= '<a class="fancybox" data-fancybox-group="gallery' . esc((string) $row['group_id'], 'attr')
                . '"  href="' . base_url() . 'uploads/learning_gallery/' . esc((string) $row['image_name'], 'url') . '" ></a>';
        }

        return $this->response->setBody($str);
    }
}
