<?php

namespace Tests\Support\Parity;

use App\Models\ProjectInformationModel;
use App\Models\ProjectsGalleryModel;
use App\Models\ProjectsModel;

/**
 * Parity checks for the "projects" domain (project gallery, project information,
 * project names for the enquiry form). Read queries only.
 */
class ProjectsParity implements ParitySuite
{
    public function domain(): string
    {
        return 'projects';
    }

    public function checks(): array
    {
        return [
            [
                'label'   => 'home_model::show_projects_gallery_list (home_model.php:587)',
                'old_sql' => "SELECT * FROM `projects_gallery` WHERE `project_id` = '16' ORDER BY `id` DESC",
                'new'     => static fn () => model(ProjectsGalleryModel::class)->listByProject(16),
            ],
            [
                'label'   => 'home_model::show_projects_gallery_list, project without images (home_model.php:587)',
                'old_sql' => "SELECT * FROM `projects_gallery` WHERE `project_id` = '999999' ORDER BY `id` DESC",
                'new'     => static fn () => model(ProjectsGalleryModel::class)->listByProject(999999),
            ],
            [
                'label'   => 'home_model::getSingle_project_gallery_image (home_model.php:650)',
                'old_sql' => "SELECT * FROM `projects_gallery` WHERE `project_id` = '7' AND `id` = '79'",
                'new'     => static fn () => model(ProjectsGalleryModel::class)->findForProject(7, 79),
                'compare' => 'row',
            ],
            [
                'label'   => 'home_model::getSingle_project_gallery_image, id of another project (home_model.php:650)',
                'old_sql' => "SELECT * FROM `projects_gallery` WHERE `project_id` = '12' AND `id` = '79'",
                'new'     => static fn () => model(ProjectsGalleryModel::class)->findForProject(12, 79),
                'compare' => 'row',
            ],
            [
                'label'   => 'home_model::getSingle_projects_information (home_model.php:880)',
                'old_sql' => "SELECT * FROM `project_information` WHERE `project_id` = '7'",
                'new'     => static fn () => model(ProjectInformationModel::class)->findByProject(7),
                'compare' => 'row',
            ],
            [
                'label'   => 'home_model::getSingle_projects_information, no row (home_model.php:880)',
                'old_sql' => "SELECT * FROM `project_information` WHERE `project_id` = '12'",
                'new'     => static fn () => model(ProjectInformationModel::class)->findByProject(12),
                'compare' => 'row',
            ],
            [
                'label'   => 'views/home1.php:291 enabled project names',
                'old_sql' => "SELECT name FROM projects where status='1' ORDER BY `projects`.`name` ASC",
                'new'     => static fn () => model(ProjectsModel::class)->activeProjectNames(),
            ],
        ];
    }
}
