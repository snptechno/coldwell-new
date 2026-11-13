<?php

namespace App\Models;

/**
 * Table `gallery` (InnoDB, latin1_swedish_ci).
 * Columns: id:int, title:text, image_name:varchar, date_time:date, group_id:int, status:int, created_date:timestamp
 *
 * Generated from the live schema (2026-11-13). Domain query methods are added
 * below this line by the step-4 port; keep $allowedFields in sync with the table.
 */
class GalleryModel extends BaseModel
{
    protected $table         = 'gallery';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'title',
        'image_name',
        'date_time',
        'group_id',
        'status',
        'created_date',
    ];
}
