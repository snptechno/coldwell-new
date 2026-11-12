<?php

namespace App\Controllers;

use CodeIgniter\CodeIgniter;
use Throwable;

/**
 * Step 3 placeholder: proves the CI4 + PHP 8.3 + MySQL 8 stack works end to end.
 * Replaced by the real home page when the public site is ported (step 5).
 */
class Home extends BaseController
{
    public function index(): string
    {
        $check = null;

        // Diagnostics only outside production - never expose versions on live.
        if (ENVIRONMENT !== 'production') {
            try {
                $db    = db_connect();
                $check = [
                    'php'      => PHP_VERSION,
                    'ci'       => CodeIgniter::CI_VERSION,
                    'db'       => $db->getVersion(),
                    'database' => $db->getDatabase(),
                    'tables'   => count($db->listTables()),
                    'cities'   => $db->table('cities')->countAllResults(),
                ];
            } catch (Throwable $e) {
                $check = ['error' => $e->getMessage()];
            }
        }

        return view('setup_check', ['title' => 'Coldwell Banker India - CI4 setup', 'check' => $check]);
    }
}
