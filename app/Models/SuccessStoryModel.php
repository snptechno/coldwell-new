<?php

namespace App\Models;

/**
 * Table `success_story` (MyISAM, utf8mb3_general_ci).
 * Columns: id:int, title:varchar, description:text, video_url:varchar, is_active:tinyint, created_date:datetime
 *
 * Generated from the live schema (2026-11-13). Domain query methods are added
 * below this line by the step-4 port; keep $allowedFields in sync with the table.
 */
class SuccessStoryModel extends BaseModel
{
    protected $table         = 'success_story';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'title',
        'description',
        'video_url',
        'is_active',
        'created_date',
    ];
}
