<?php

namespace App\Models;

/**
 * Table `research` (InnoDB, latin1_swedish_ci).
 * Columns: id:int, type:varchar, title:varchar, description:varchar, image_name:varchar, pdf_name:varchar, is_active:tinyint, created_date:timestamp
 *
 * Generated from the live schema (2026-11-13). Domain query methods are added
 * below this line by the step-4 port; keep $allowedFields in sync with the table.
 */
class ResearchModel extends BaseModel
{
    protected $table         = 'research';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'type',
        'title',
        'description',
        'image_name',
        'pdf_name',
        'is_active',
        'created_date',
    ];
}
