<?php

namespace App\Models;

/**
 * Table `featured_videos` (InnoDB, latin1_swedish_ci).
 * Columns: id:int, title:varchar, description:text, video_url:varchar, is_active:tinyint, created_date:timestamp
 *
 * Generated from the live schema (2026-11-13). Domain query methods are added
 * below this line by the step-4 port; keep $allowedFields in sync with the table.
 */
class FeaturedVideosModel extends BaseModel
{
    protected $table         = 'featured_videos';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'title',
        'description',
        'video_url',
        'is_active',
        'created_date',
    ];
}
