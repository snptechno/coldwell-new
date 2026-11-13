<?php

namespace App\Models;

/**
 * Table `project_amenities` (InnoDB, latin1_swedish_ci).
 * Columns: id:int, project_id:int, amenites:varchar, created_date:timestamp
 *
 * Generated from the live schema (2026-11-13). Domain query methods are added
 * below this line by the step-4 port; keep $allowedFields in sync with the table.
 */
class ProjectAmenitiesModel extends BaseModel
{
    protected $table         = 'project_amenities';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'project_id',
        'amenites',
        'created_date',
    ];
}
