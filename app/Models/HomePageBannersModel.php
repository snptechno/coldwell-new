<?php

namespace App\Models;

/**
 * Table `home_page_banners` (InnoDB, latin1_swedish_ci).
 * Columns: id:int, name:text, sub_title:text, image:varchar, project_id:int, project_type:varchar, status:tinyint, created_date:timestamp
 *
 * Generated from the live schema (2026-11-13). Domain query methods are added
 * below this line by the step-4 port; keep $allowedFields in sync with the table.
 */
class HomePageBannersModel extends BaseModel
{
    protected $table         = 'home_page_banners';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'name',
        'sub_title',
        'image',
        'project_id',
        'project_type',
        'status',
        'created_date',
    ];
}
