<?php

namespace App\Models;

/**
 * Table `projects_banners` (InnoDB, latin1_swedish_ci).
 * Columns: id:int, project_id:int, name:varchar, image:varchar, status:tinyint, created_date:timestamp
 *
 * Generated from the live schema (2026-11-13). Domain query methods are added
 * below this line by the step-4 port; keep $allowedFields in sync with the table.
 */
class ProjectsBannersModel extends BaseModel
{
    protected $table         = 'projects_banners';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'project_id',
        'name',
        'image',
        'status',
        'created_date',
    ];
}
