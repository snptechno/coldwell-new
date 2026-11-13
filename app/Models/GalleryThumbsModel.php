<?php

namespace App\Models;

/**
 * Table `gallery_thumbs` (InnoDB, latin1_swedish_ci).
 * Columns: id:int, title:text, date_time:date, image_name:varchar, group_id:int, status:int, created_date:timestamp
 *
 * Generated from the live schema (2026-11-13). Domain query methods are added
 * below this line by the step-4 port; keep $allowedFields in sync with the table.
 */
class GalleryThumbsModel extends BaseModel
{
    protected $table         = 'gallery_thumbs';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'title',
        'date_time',
        'image_name',
        'group_id',
        'status',
        'created_date',
    ];
}
