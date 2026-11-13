<?php

namespace App\Controllers\Site;

use App\Controllers\BaseController;
use App\Models\PagesModel;
use CodeIgniter\Exceptions\PageNotFoundException;

/**
 * Public home page.
 * CI3: controllers/Home.php Home::index (view views/home.php).
 */
class Home extends BaseController
{
    /**
     * CI3: Home::index($id = '') (controllers/Home.php:20).
     * The optional segment was accepted and ignored in CI3 (/index/anything); it is
     * accepted and ignored here as well.
     */
    public function index(?string $ignored = null): string
    {
        // CI3: Ui_model::page_info() -> pages row id 1, as an object ($page_info->title).
        $row = model(PagesModel::class)->pageById(PagesModel::PAGE_HOME);
        if ($row === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        return view('site/home', [
            'page_info'   => (object) $row,
            'page_active' => 'home',
        ]);
    }
}
