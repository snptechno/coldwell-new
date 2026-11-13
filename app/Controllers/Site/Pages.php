<?php

namespace App\Controllers\Site;

use App\Controllers\BaseController;
use App\Models\PagesModel;
use CodeIgniter\Exceptions\PageNotFoundException;

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

    /** CI3: Home::faq (controllers/Home.php:974) */
    public function faq(): string
    {
        return view('site/faq', [
            'page_info'   => $this->pageInfo(PagesModel::PAGE_FAQ),
            'page_active' => 'faq',
        ]);
    }
}
