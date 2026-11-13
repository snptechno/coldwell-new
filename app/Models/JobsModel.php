<?php

namespace App\Models;

/**
 * Table `jobs` (InnoDB, latin1_swedish_ci).
 * Columns: id:int, type:varchar, position:varchar, qualification:text, max_experience:varchar, min_experience:varchar, location:varchar, description:text, status:tinyint, created_date:timestamp
 *
 * Generated from the live schema (2026-11-13). Domain query methods are added
 * below this line by the step-4 port; keep $allowedFields in sync with the table.
 */
class JobsModel extends BaseModel
{
    protected $table         = 'jobs';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'type',
        'position',
        'qualification',
        'max_experience',
        'min_experience',
        'location',
        'description',
        'status',
        'created_date',
    ];
}
