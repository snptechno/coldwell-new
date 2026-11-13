<?php

namespace App\Models;

/**
 * Table `franchises_owners` (MyISAM, utf8mb3_general_ci).
 * Columns: id:int, franchise_id:int, name:varchar, description:text, gender:varchar, position_name:varchar, image:varchar, status:tinyint, created_date:datetime
 *
 * Generated from the live schema (2026-11-13). Domain query methods are added
 * below this line by the step-4 port; keep $allowedFields in sync with the table.
 */
class FranchisesOwnersModel extends BaseModel
{
    protected $table         = 'franchises_owners';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'franchise_id',
        'name',
        'description',
        'gender',
        'position_name',
        'image',
        'status',
        'created_date',
    ];
}
