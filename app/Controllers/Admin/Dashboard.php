<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index(?string $action = null): string
    {
        $data = [
            'title' => 'Dashboard',
        ];
        $data['subview'] = view('admin/main_content', $data);

        return view('admin/_layout_main', $data);
    }
}
