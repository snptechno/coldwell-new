<?php

namespace App\Models;

/**
 * Table `events` (InnoDB, latin1_swedish_ci).
 * Columns: id:int, title:text, start_date:varchar, end_date:varchar, duration:varchar, seats:varchar, status:int, created_date:timestamp
 *
 * Generated from the live schema (2026-11-13). Domain query methods are added
 * below this line by the step-4 port; keep $allowedFields in sync with the table.
 */
class EventsModel extends BaseModel
{
    protected $table         = 'events';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'title',
        'start_date',
        'end_date',
        'duration',
        'seats',
        'status',
        'created_date',
    ];
}
