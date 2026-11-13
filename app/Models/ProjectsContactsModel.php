<?php

namespace App\Models;

/**
 * Table `projects_contacts` (InnoDB, latin1_swedish_ci).
 * Columns: id:int, project_id:varchar, address:text, location:varchar, email_id:varchar, phone_no:varchar, fax:varchar, latitude:varchar, longitude:varchar, created_date:timestamp
 *
 * Generated from the live schema (2026-11-13). Domain query methods are added
 * below this line by the step-4 port; keep $allowedFields in sync with the table.
 */
class ProjectsContactsModel extends BaseModel
{
    protected $table         = 'projects_contacts';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'project_id',
        'address',
        'location',
        'email_id',
        'phone_no',
        'fax',
        'latitude',
        'longitude',
        'created_date',
    ];
}
