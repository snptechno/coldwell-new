<?php

namespace App\Models;

/**
 * Table `cities` (InnoDB, latin1_swedish_ci).
 * Columns: id:int, city_name:varchar, created_date:timestamp
 *
 * Generated from the live schema (2026-11-13). Domain query methods are added
 * below this line by the step-4 port; keep $allowedFields in sync with the table.
 */
class CitiesModel extends BaseModel
{
    protected $table         = 'cities';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'city_name',
        'created_date',
    ];
}
