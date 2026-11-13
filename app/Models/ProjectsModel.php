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
}
