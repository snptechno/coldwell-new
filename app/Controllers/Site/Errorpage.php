<?php

namespace App\Controllers\Site;

use App\Controllers\BaseController;

/**
 * Site-wide "page not found" page, registered as the 404 override
 * (app/Config/Routing.php). Used for unknown URLs AND for every
 * PageNotFoundException a controller throws (e.g. a missing franchise id).
 *
 * CI3: controllers/Errorpage.php + views/errorpage/index.php. CI3 sent this
 * designed page with HTTP 200 ("soft 404", audit RS-02: its 404 status line
 * was commented out); it now goes out with a real 404 status.
 */
class Errorpage extends BaseController
{
    public function index(): string
    {
        $this->response->setStatusCode(404);

        return view('site/errorpage');
    }
}
