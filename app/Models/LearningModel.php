<?php

namespace App\Models;

/**
 * Table `learning` (InnoDB, latin1_swedish_ci).
 * Columns: id:int, overview:text, university:text, university_logo:varchar, last_updated_on:timestamp
 *
 * Generated from the live schema (2026-11-13). Domain query methods are added
 * below this line by the step-4 port; keep $allowedFields in sync with the table.
 */
class LearningModel extends BaseModel
{
    protected $table         = 'learning';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'overview',
        'university',
        'university_logo',
        'last_updated_on',
    ];
}
