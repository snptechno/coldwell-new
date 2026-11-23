<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use CodeIgniter\HTTP\RedirectResponse;

class Login extends BaseController
{
    public function index(): string|RedirectResponse
    {
        if (session()->get('admin_user')) {
            return redirect()->to(site_url('admin/dashboard/index'));
        }

        $username = $this->request->getPost('user_name');
        $password = $this->request->getPost('password');

        if ($username !== null && $password !== null) {
            // Brute-force throttle: at most 5 login attempts per minute per IP.
            $throttler = \Config\Services::throttler();
            if ($throttler->check(md5('admin-login-' . $this->request->getIPAddress()), 5, MINUTE) === false) {
                session()->setFlashdata('error', ' Too many login attempts. Please wait '
                    . $throttler->getTokenTime() . ' seconds and try again.');

                return redirect()->to(site_url('admin/login/'));
            }

            $user = (new UserModel())->findByCredentials((string) $username, (string) $password);
            if ($user !== null && isset($user['id']) && (int) $user['id'] > 0) {
                // ADM-05: Regenerate session ID on login to prevent session fixation.
                session()->regenerate();
                session()->set([
                    'admin_user' => $user['username'],
                    'username'   => $user['username'],
                ]);

                return redirect()->to(site_url('admin/dashboard/index'));
            }

            session()->setFlashdata('error', ' Incorrect Username Or Password');

            return redirect()->to(site_url('admin/login/'));
        }

        $data = [
            'title' => 'Coldwell Banker - Admin',
        ];
        $data['subview'] = view('login/login_form', $data);

        return view('admin/login', $data);
    }

    public function logout(): RedirectResponse
    {
        session()->destroy();

        return redirect()->to(site_url('admin/login/index'));
    }
}
