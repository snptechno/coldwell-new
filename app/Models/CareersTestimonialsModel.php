<?php

namespace App\Models;

/**
 * Table `careers_testimonials` (InnoDB, latin1_swedish_ci).
 * Columns: id:int, title:text, name:varchar, position:varchar, image_name:varchar, status:tinyint, created_date:timestamp
 *
 * Generated from the live schema (2026-11-13). Domain query methods are added
 * below this line by the step-4 port; keep $allowedFields in sync with the table.
 */
class CareersTestimonialsModel extends BaseModel
{
    protected $table         = 'careers_testimonials';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'title',
        'name',
        'position',
        'image_name',
        'status',
        'created_date',
    ];
}
