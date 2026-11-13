<?php

namespace Tests\Support\Parity;

use App\Models\JobApplicationsModel;
use App\Models\JobsModel;

/**
 * Parity checks for the careers domain (tables `jobs`, `job_applications`).
 * Read queries only; write methods are reviewed, not executed.
 */
class CareersParity implements ParitySuite
{
    public function domain(): string
    {
        return 'careers';
    }

    public function checks(): array
    {
        $jobs = new JobsModel();
        $apps = new JobApplicationsModel();

        return [
            [
                'label'   => 'home_model::show_job_openings (home_model.php:814)',
                'old_sql' => 'SELECT * FROM `jobs` ORDER BY `id` DESC',
                'new'     => static fn () => $jobs->listAll(),
            ],
            [
                'label'   => 'home_model::getSingle_job (home_model.php:820) id=17',
                'old_sql' => "SELECT * FROM `jobs` WHERE `id` = '17'",
                'new'     => static fn () => $jobs->findJob(17),
                'compare' => 'row',
            ],
            [
                'label'   => 'home_model::getSingle_job (home_model.php:820) missing id',
                'old_sql' => "SELECT * FROM `jobs` WHERE `id` = '999999'",
                'new'     => static fn () => $jobs->findJob(999999),
                'compare' => 'row',
            ],
            [
                'label'   => 'Ui_model::current_openings_description (Ui_model.php:209) id=15',
                'old_sql' => "SELECT * FROM `jobs` WHERE `id` = '15'",
                'new'     => static fn () => $jobs->findJob(15),
                'compare' => 'row',
            ],
            [
                'label'   => 'Ui_model::get_position_name (Ui_model.php:297) id=18',
                'old_sql' => "SELECT * FROM `jobs` WHERE `id` = '18'",
                'new'     => static fn () => $jobs->findJob(18),
                'compare' => 'row',
            ],
            [
                'label'   => 'home_model::checkDuplicate_job (home_model.php:826) existing position',
                'old_sql' => "SELECT * FROM `jobs` WHERE `position` = 'general manager - franchise acquisition'",
                'new'     => static fn () => $jobs->findIdByPosition('General Manager - Franchise Acquisition'),
                'compare' => 'scalar', // first column of SELECT * is `id`, which CI3 returned
                'note'    => 'returns ?int (null when no match) instead of the raw id string / null offset access',
            ],
            [
                'label'   => 'home_model::checkDuplicate_job (home_model.php:826) new position',
                'old_sql' => "SELECT * FROM `jobs` WHERE `position` = 'no such position'",
                'new'     => static fn () => $jobs->findIdByPosition('No Such Position'),
                'compare' => 'scalar',
            ],
            [
                'label'   => 'views/career.php:56 (also career-old.php:56)',
                'old_sql' => "select * from jobs where type='COLDWELL BANKER INDIA OPENINGS' and status= '1' order by id desc ",
                'new'     => static fn () => $jobs->activeByType(JobsModel::TYPE_COLDWELL),
            ],
            [
                'label'   => 'views/franchisees_openings.php:57',
                'old_sql' => "select * from jobs where type='FRANCHISEES OPENINGS' and status= '1' order by id desc",
                'new'     => static fn () => $jobs->activeByType(JobsModel::TYPE_FRANCHISEE),
            ],
            [
                'label'   => 'home_model::show_job_application (home_model.php:860)',
                'old_sql' => 'select job_applications.*, jobs.position, jobs.type as position_type from job_applications inner join jobs on job_applications.job_id = jobs.id order by job_applications.id desc',
                'new'     => static fn () => $apps->listWithPosition(),
            ],
            [
                'label'   => 'home_model::show_job_candidate_details (home_model.php:867) id=1836',
                'old_sql' => 'select job_applications.*, jobs.position as position_name from job_applications inner join jobs on  job_applications.job_id = jobs.id where job_applications.id=1836',
                'new'     => static fn () => $apps->detailsWithPosition(1836),
                'compare' => 'row',
                'note'    => 'id is cast to int and bound instead of concatenated',
            ],
            [
                'label'   => 'home_model::show_job_candidate_details (home_model.php:867) id=1769 (job missing)',
                'old_sql' => 'select job_applications.*, jobs.position as position_name from job_applications inner join jobs on  job_applications.job_id = jobs.id where job_applications.id=1769',
                'new'     => static fn () => $apps->detailsWithPosition(1769),
                'compare' => 'row',
                'note'    => 'id is cast to int and bound instead of concatenated',
            ],
            [
                'label'   => 'home_model::get_candidate_resume (home_model.php:873) id=1835',
                'old_sql' => "SELECT * FROM `job_applications` WHERE `id` = '1835' ORDER BY `id` DESC",
                'new'     => static fn () => $apps->findApplication(1835),
                'compare' => 'row',
            ],
            [
                'label'   => 'Ui_model::get_candidate_resume (Ui_model.php:253) id=1833',
                'old_sql' => 'select job_applications.*, jobs.* from job_applications inner join jobs on  job_applications.job_id = jobs.id where job_applications.id=1833',
                'new'     => static fn () => $apps->findWithJob(1833),
                'compare' => 'row',
                'note'    => 'id is cast to int and bound instead of concatenated',
            ],
            [
                'label'   => 'Ui_model::get_candidate_resume (Ui_model.php:253) id=1768 (job missing)',
                'old_sql' => 'select job_applications.*, jobs.* from job_applications inner join jobs on  job_applications.job_id = jobs.id where job_applications.id=1768',
                'new'     => static fn () => $apps->findWithJob(1768),
                'compare' => 'row',
                'note'    => 'id is cast to int and bound instead of concatenated',
            ],
        ];
    }
}
