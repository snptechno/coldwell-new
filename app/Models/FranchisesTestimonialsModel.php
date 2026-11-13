<?php

namespace App\Models;

/**
 * Table `franchises_testimonials` (InnoDB, utf8mb3_general_ci).
 * Columns: id:int, franchise_id:int, name:varchar, designation:varchar, description:text, is_active:tinyint, created_date:datetime
 *
 * Generated from the live schema (2026-11-13). Domain query methods are added
 * below this line by the step-4 port; keep $allowedFields in sync with the table.
 */
class FranchisesTestimonialsModel extends BaseModel
{
    protected $table         = 'franchises_testimonials';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'franchise_id',
        'name',
        'designation',
        'description',
        'is_active',
        'created_date',
    ];
}
