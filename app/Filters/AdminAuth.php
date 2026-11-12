<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Protects every route in the "admin" group.
 *
 * CI3 did this with a session check in each admin controller's constructor
 * (e.g. controllers/admin/Section.php:21-23). CI4 forbids redirects from
 * constructors, and a per-controller check is easy to forget (audit LC-12),
 * so one filter on the route group replaces all of them.
 */
class AdminAuth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! session()->get('admin_user')) {
            // 303 after a POST, 302 for GET - never let an unauthenticated
            // request reach an admin controller.
            return redirect()->to(site_url('admin/login'));
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Admin pages must never be cached by browsers or proxies.
        $response->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');

        return null;
    }
}
