<?php

namespace App\Models;

/**
 * Table `real_estate_specialist` (InnoDB, latin1_swedish_ci).
 * Columns: id:int, franchise_id:int, name:varchar, title:text, description:text, image:varchar, created_date:timestamp
 *
 * Generated from the live schema (2026-11-13). Domain query methods are added
 * below this line by the step-4 port; keep $allowedFields in sync with the table.
 */
class RealEstateSpecialistModel extends BaseModel
{
    protected $table         = 'real_estate_specialist';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'franchise_id',
        'name',
        'title',
        'description',
        'image',
        'created_date',
    ];
}
