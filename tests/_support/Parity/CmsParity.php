<?php

namespace Tests\Support\Parity;

use App\Models\CorporateTeamsModel;
use App\Models\PagesModel;
use App\Models\UserModel;

/**
 * Parity checks for the cms domain (tables `pages`, `corporate_teams`, `user`).
 * Read queries only; write methods are reviewed, not executed.
 */
class CmsParity implements ParitySuite
{
    public function domain(): string
    {
        return 'cms';
    }

    public function checks(): array
    {
        $pages = new PagesModel();
        $teams = new CorporateTeamsModel();
        $users = new UserModel();

        $checks = [
            [
                'label'   => 'home_model::show_corporate_teams (home_model.php:367)',
                'old_sql' => 'SELECT * FROM `corporate_teams` ORDER BY `order_no` ASC',
                'new'     => static fn () => $teams->listForAdmin(),
            ],
            [
                'label'   => 'home_model::show_corporate_team_by_id (home_model.php:420) id=25',
                'old_sql' => "SELECT * FROM `corporate_teams` WHERE `id` = '25'",
                'new'     => static fn () => $teams->memberById(25),
                'compare' => 'row',
            ],
            [
                'label'   => 'home_model::show_corporate_team_by_id (home_model.php:420) missing id',
                'old_sql' => "SELECT * FROM `corporate_teams` WHERE `id` = '999999'",
                'new'     => static fn () => $teams->memberById(999999),
                'compare' => 'row',
            ],
            [
                'label'   => 'Ui_model::get_team_information (Ui_model.php:279) id=19',
                'old_sql' => "SELECT * FROM `corporate_teams` WHERE `id` = '19'",
                'new'     => static fn () => $teams->memberById(19),
                'compare' => 'row',
            ],
            [
                'label'   => 'views/corporate_team.php:47 (inline query)',
                'old_sql' => 'select * from corporate_teams where status=1 order by order_no asc ',
                'new'     => static fn () => $teams->activeOrdered(),
            ],
            [
                'label'   => 'home_model::show_pages_list (home_model.php:437)',
                'old_sql' => 'SELECT * FROM `pages` ORDER BY `id` DESC',
                'new'     => static fn () => $pages->listForAdmin(),
            ],
            [
                'label'   => 'home_model::getSingle_page (home_model.php:443) id=12',
                'old_sql' => "SELECT * FROM `pages` WHERE `id` = '12'",
                'new'     => static fn () => $pages->pageById(12),
                'compare' => 'row',
            ],
            [
                'label'   => 'home_model::getSingle_page (home_model.php:443) missing id',
                'old_sql' => "SELECT * FROM `pages` WHERE `id` = '999999'",
                'new'     => static fn () => $pages->pageById(999999),
                'compare' => 'row',
            ],
            [
                'label'   => 'login_model::login (login_model.php:6) unknown credentials',
                'old_sql' => "SELECT * FROM user WHERE username = 'parity-no-such-user' AND password = '"
                    . md5('parity-sample') . "'",
                'new'     => static fn () => $users->findByCredentials('parity-no-such-user', 'parity-sample'),
                'compare' => 'row',
                'note'    => 'no stored credentials are used in this public suite; the null case is compared',
            ],
        ];

        // Ui_model page methods: each is `where('id', '<n>')->get('pages')->row()`.
        $uiPages = [
            ['page_info', 9, PagesModel::PAGE_HOME],
            ['finding_a_home', 15, PagesModel::PAGE_FINDING_A_HOME],
            ['selling_a_home', 33, PagesModel::PAGE_VIDEOS],
            ['testimonials', 39, PagesModel::PAGE_VIDEOS],
            ['our_franchisee', 45, PagesModel::PAGE_OUR_FRANCHISEE],
            ['our_franchisee_details', 51, PagesModel::PAGE_OUR_FRANCHISEE_DETAILS],
            ['learning_development', 57, PagesModel::PAGE_LEARNING_DEVELOPMENT],
            ['media_center', 63, PagesModel::PAGE_MEDIA_CENTER],
            ['industry_news', 69, PagesModel::PAGE_MEDIA_CENTER],
            ['knowledge_series', 75, PagesModel::PAGE_MEDIA_CENTER],
            ['newsletter', 81, PagesModel::PAGE_MEDIA_CENTER],
            ['corporate_team', 93, PagesModel::PAGE_CORPORATE_TEAM],
            ['career', 105, PagesModel::PAGE_CAREER],
            ['area_insights', 111, PagesModel::PAGE_RESEARCH],
            ['coldwell_banker_usa', 117, PagesModel::PAGE_ABOUT_US],
            ['nri', 129, PagesModel::PAGE_NRI],
            ['contact_us', 141, PagesModel::PAGE_CONTACT_US],
            ['privacy_policy', 487, PagesModel::PAGE_PRIVACY_POLICY],
            ['terms_and_conditions', 492, PagesModel::PAGE_TERMS_AND_CONDITIONS],
            ['disclaimer', 497, PagesModel::PAGE_DISCLAIMER],
            ['faq', 502, PagesModel::PAGE_FAQ],
            ['refund', 507, PagesModel::PAGE_REFUND],
            ['quick_pay', 512, PagesModel::PAGE_FINDING_A_HOME],
        ];

        foreach ($uiPages as [$method, $line, $id]) {
            $checks[] = [
                'label'   => "Ui_model::{$method} (Ui_model.php:{$line}) id={$id}",
                'old_sql' => "SELECT * FROM `pages` WHERE `id` = '{$id}'",
                'new'     => static fn () => $pages->pageById($id),
                'compare' => 'row',
                'note'    => 'CI3 row() object is now an array (views read $page_info[\'title\'])',
            ];
        }

        return $checks;
    }
}
