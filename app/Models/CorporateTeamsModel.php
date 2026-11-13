<?php

namespace App\Models;

/**
 * Table `corporate_teams` (InnoDB, latin1_swedish_ci).
 * Columns: id:int, name:varchar, title:text, description:text, image:varchar, status:tinyint, order_no:int, created_date:timestamp
 *
 * Generated from the live schema (2026-11-13). Domain query methods are added
 * below this line by the step-4 port; keep $allowedFields in sync with the table.
 */
class CorporateTeamsModel extends BaseModel
{
    protected $table         = 'corporate_teams';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'name',
        'title',
        'description',
        'image',
        'status',
        'order_no',
        'created_date',
    ];
}
