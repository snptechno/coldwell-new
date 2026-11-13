<?php

namespace App\Models;

/**
 * Table `awards` (MyISAM, utf8mb3_general_ci).
 * Columns: id:int, name:varchar, image:varchar, created_date:datetime
 *
 * Generated from the live schema (2026-11-13). Domain query methods are added
 * below this line by the step-4 port; keep $allowedFields in sync with the table.
 */
class AwardsModel extends BaseModel
{
    protected $table         = 'awards';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'name',
        'image',
        'created_date',
    ];
}
