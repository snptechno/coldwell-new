<?php

namespace App\Models;

/**
 * Table `project_information` (InnoDB, latin1_swedish_ci).
 * Columns: id:int, project_id:int, bedrooms:text, saleable_area:text, possession:date, developer:text, rating:varchar, towers:varchar, created_date:timestamp
 *
 * Generated from the live schema (2026-11-13). Domain query methods are added
 * below this line by the step-4 port; keep $allowedFields in sync with the table.
 */
class ProjectInformationModel extends BaseModel
{
    protected $table         = 'project_information';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'project_id',
        'bedrooms',
        'saleable_area',
        'possession',
        'developer',
        'rating',
        'towers',
        'created_date',
    ];
}
