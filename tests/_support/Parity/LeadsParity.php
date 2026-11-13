<?php

namespace Tests\Support\Parity;

use App\Models\ContactEnquiryModel;
use App\Models\FranchiseeEnquiryModel;
use App\Models\FranchiseeProjectEnquiryModel;
use App\Models\NewsletterSubscriptionsModel;
use App\Models\NriEnquiryModel;
use App\Models\ProjectsContactsModel;

/**
 * Parity checks for the leads domain (enquiry tables, newsletter subscriptions,
 * project contact blocks). Read queries only; write methods are reviewed, not executed.
 */
class LeadsParity implements ParitySuite
{
    public function domain(): string
    {
        return 'leads';
    }

    public function checks(): array
    {
        $contact   = new ContactEnquiryModel();
        $franchise = new FranchiseeEnquiryModel();
        $fpe       = new FranchiseeProjectEnquiryModel();
        $news      = new NewsletterSubscriptionsModel();
        $nri       = new NriEnquiryModel();
        $pc        = new ProjectsContactsModel();

        $fpeSelect = 'select franchisee_project_enquiry.*, franchises.title as franchise_name from franchisee_project_enquiry'
            . ' inner join franchises on  franchisee_project_enquiry.franchisee_id = franchises.id';
        $homeProjects = "select p.id, p.small_image, p.name, c.location from projects p inner join projects_contacts c on p.id=c.project_id where p.status='1' and p.project_status='%s' and p.project_type='%s' order by p.name";

        $checks = [
            [
                'label'   => 'home_model::project_contact_enquiry (home_model.php:520)',
                'old_sql' => 'SELECT * FROM `contact_enquiry` ORDER BY `id` DESC',
                'new'     => static fn () => $contact->allNewestFirst(),
            ],
            [
                'label'   => 'home_model::show_contact_enquiry (home_model.php:545)',
                'old_sql' => 'SELECT * FROM `contact_enquiry` ORDER BY `id` DESC',
                'new'     => static fn () => $contact->allNewestFirst(),
            ],
            [
                'label'   => 'home_model::info_contact_enquiry_by_dates (home_model.php:551) 2026-11-01..2026-11-12',
                'old_sql' => " select * from contact_enquiry where created_on between '2026-11-01 00:00:00' and '2026-11-12 23:59:59' order by created_on desc",
                'new'     => static fn () => $contact->byCreatedDateRange('2026-11-01', '2026-11-12'),
            ],
            [
                'label'   => 'home_model::info_contact_enquiry_by_dates (home_model.php:551) empty range',
                'old_sql' => " select * from contact_enquiry where created_on between '2030-01-01 00:00:00' and '2030-01-31 23:59:59' order by created_on desc",
                'new'     => static fn () => $contact->byCreatedDateRange('2030-01-01', '2030-01-31'),
            ],
            [
                'label'   => 'home_model::show_franchisee_enquiry (home_model.php:526)',
                'old_sql' => 'SELECT * FROM `franchisee_enquiry` ORDER BY `id` DESC',
                'new'     => static fn () => $franchise->allNewestFirst(),
            ],
            [
                'label'   => 'home_model::info_franchisee_enquiries_by_dates (home_model.php:532) 2025-01-01..2026-08-08',
                'old_sql' => " select * from franchisee_enquiry where created_on between '2025-01-01 00:00:00' and '2026-08-08 23:59:59' order by created_on desc",
                'new'     => static fn () => $franchise->byCreatedDateRange('2025-01-01', '2026-08-08'),
            ],
            [
                'label'   => 'home_model::franchisee_project_enquiries (home_model.php:1796)',
                'old_sql' => $fpeSelect . ' order by franchisee_project_enquiry.id desc ',
                'new'     => static fn () => $fpe->allWithFranchiseName(),
            ],
            [
                'label'   => 'home_model::info_franchisee_project_enquiries_by_dates (home_model.php:1802) 2026-06-01..2026-10-21',
                'old_sql' => $fpeSelect . " where franchisee_project_enquiry.created_date between '2026-06-01 00:00:00' and '2026-10-21 23:59:59' order by franchisee_project_enquiry.id desc ",
                'new'     => static fn () => $fpe->byCreatedDateRangeWithFranchiseName('2026-06-01', '2026-10-21'),
                'note'    => 'created_date qualified with its table (unqualified it is ambiguous, MySQL error 1052)',
            ],
            [
                'label'   => 'home_model::newsletter_list (home_model.php:1934)',
                'old_sql' => 'SELECT * FROM `newsletter_subscriptions` ORDER BY `id` DESC',
                'new'     => static fn () => $news->allNewestFirst(),
            ],
            [
                'label'   => 'home_model::show_nri_list (home_model.php:2122)',
                'old_sql' => 'SELECT `n`.* FROM `nri_enquiry` `n` ORDER BY `n`.`id` DESC',
                'new'     => static fn () => $nri->allNewestFirst(),
            ],
            [
                'label'   => 'home_model::getSingle_project_contact (home_model.php:580) project_id=7',
                'old_sql' => "SELECT * FROM `projects_contacts` WHERE `project_id` = '7'",
                'new'     => static fn () => $pc->findByProjectId(7),
                'compare' => 'row',
            ],
            [
                'label'   => 'home_model::getSingle_project_contact (home_model.php:580) project_id=16',
                'old_sql' => "SELECT * FROM `projects_contacts` WHERE `project_id` = '16'",
                'new'     => static fn () => $pc->findByProjectId(16),
                'compare' => 'row',
            ],
            [
                'label'   => 'home_model::getSingle_project_contact (home_model.php:580) missing project_id',
                'old_sql' => "SELECT * FROM `projects_contacts` WHERE `project_id` = '999999'",
                'new'     => static fn () => $pc->findByProjectId(999999),
                'compare' => 'row',
                'note'    => 'no row: CI3 row_array() gave [], CI4 gives null',
            ],
            [
                'label'   => 'views/home1.php:49 home banner slides',
                'old_sql' => "select p.id, p.name, h.sub_title, c.location, h.image, p.is_luxurious from projects p, home_page_banners h, projects_contacts c where p.id=h.project_id and p.id=c.project_id and h.status='1' ",
                'new'     => static fn () => $pc->homeBannerSlides(),
                'compare' => 'set',
            ],
        ];

        $homeLines = [
            158 => ['Residential', 'ongoing_project'],
            173 => ['Residential', 'upcoming_project'],
            185 => ['Residential', 'completed_project'],
            205 => ['Commercial', 'ongoing_project'],
            217 => ['Commercial', 'upcoming_project'],
            229 => ['Commercial', 'completed_project'],
        ];

        foreach ($homeLines as $line => [$type, $status]) {
            $checks[] = [
                'label'   => "views/home1.php:{$line} {$type} {$status}",
                'old_sql' => sprintf($homeProjects, $status, $type),
                'new'     => static fn () => $pc->activeProjectsByTypeAndStatus($type, $status),
            ];
        }

        return $checks;
    }
}
