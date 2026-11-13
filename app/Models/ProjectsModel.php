<?php

namespace App\Models;

/**
 * Table `projects` (InnoDB, latin1_swedish_ci).
 * Columns: id:int, name:varchar, sub_title:text, meta_title:text, meta_keywords:text, meta_description:text, small_image:varchar, project_type:varchar, project_status:varchar, status:tinyint, is_luxurious:tinyint, created_date:timestamp
 *
 * Generated from the live schema (2026-11-13). Domain query methods are added
 * below this line by the step-4 port; keep $allowedFields in sync with the table.
 */
class ProjectsModel extends BaseModel
{
    protected $table         = 'projects';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'name',
        'sub_title',
        'meta_title',
        'meta_keywords',
        'meta_description',
        'small_image',
        'project_type',
        'project_status',
        'status',
        'is_luxurious',
        'created_date',
    ];

    /**
     * Names of the enabled projects, alphabetical, for the "I'm interested in"
     * select of the contact enquiry form.
     * CI3: inline query in views/home1.php:291.
     *
     * @return list<array{name: string}>
     */
    public function activeProjectNames(): array
    {
        return $this->select('name')
            ->where('status', 1)
            ->orderBy('name', 'ASC')
            ->findAll();
    }
}
