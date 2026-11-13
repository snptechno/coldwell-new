<?php

namespace App\Models;

/**
 * Table `projects_highlights` (InnoDB, latin1_swedish_ci).
 * Columns: id:int, project_id:varchar, name:varchar, created_date:timestamp
 *
 * Generated from the live schema (2026-11-13). Domain query methods are added
 * below this line by the step-4 port; keep $allowedFields in sync with the table.
 */
class ProjectsHighlightsModel extends BaseModel
{
    protected $table         = 'projects_highlights';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'project_id',
        'name',
        'created_date',
    ];
}
