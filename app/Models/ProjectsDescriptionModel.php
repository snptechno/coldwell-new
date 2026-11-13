<?php

namespace App\Models;

/**
 * Table `projects_description` (InnoDB, latin1_swedish_ci).
 * Columns: id:int, project_id:int, description:text, created_date:timestamp
 *
 * Generated from the live schema (2026-11-13). Domain query methods are added
 * below this line by the step-4 port; keep $allowedFields in sync with the table.
 */
class ProjectsDescriptionModel extends BaseModel
{
    protected $table         = 'projects_description';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'project_id',
        'description',
        'created_date',
    ];
}
