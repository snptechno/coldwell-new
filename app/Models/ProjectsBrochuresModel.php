<?php

namespace App\Models;

/**
 * Table `projects_brochures` (InnoDB, latin1_swedish_ci).
 * Columns: id:int, project_id:varchar, name:varchar, file_name:varchar, created_date:timestamp
 *
 * Generated from the live schema (2026-11-13). Domain query methods are added
 * below this line by the step-4 port; keep $allowedFields in sync with the table.
 */
class ProjectsBrochuresModel extends BaseModel
{
    protected $table         = 'projects_brochures';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'project_id',
        'name',
        'file_name',
        'created_date',
    ];
}
