<?php

namespace App\Models;

/**
 * Table `amenities` (InnoDB, latin1_swedish_ci).
 * Columns: id:int, name:varchar, image:varchar, status:tinyint, order_id:int, created_date:timestamp
 *
 * Generated from the live schema (2026-11-13). Domain query methods are added
 * below this line by the step-4 port; keep $allowedFields in sync with the table.
 */
class AmenitiesModel extends BaseModel
{
    protected $table         = 'amenities';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'name',
        'image',
        'status',
        'order_id',
        'created_date',
    ];
}
