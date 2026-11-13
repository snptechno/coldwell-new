<?php

namespace App\Models;

/**
 * Table `schedule_visit` (InnoDB, latin1_swedish_ci).
 * Columns: id:int, name:varchar, location:varchar, project:varchar, email_id:varchar, date:varchar, time:varchar, mobile_no:varchar, created_date:timestamp
 *
 * Generated from the live schema (2026-11-13). Domain query methods are added
 * below this line by the step-4 port; keep $allowedFields in sync with the table.
 */
class ScheduleVisitModel extends BaseModel
{
    protected $table         = 'schedule_visit';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'name',
        'location',
        'project',
        'email_id',
        'date',
        'time',
        'mobile_no',
        'created_date',
    ];
}
