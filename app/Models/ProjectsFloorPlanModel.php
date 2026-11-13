<?php

namespace App\Models;

/**
 * Table `projects_floor_plan` (InnoDB, latin1_swedish_ci).
 * Columns: id:int, project_id:int, name:varchar, image:varchar, created_date:timestamp
 *
 * Generated from the live schema (2026-11-13). Domain query methods are added
 * below this line by the step-4 port; keep $allowedFields in sync with the table.
 */
class ProjectsFloorPlanModel extends BaseModel
{
    protected $table         = 'projects_floor_plan';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'project_id',
        'name',
        'image',
        'created_date',
    ];
}
