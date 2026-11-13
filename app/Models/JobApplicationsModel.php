<?php

namespace App\Models;

/**
 * Table `job_applications` (InnoDB, latin1_swedish_ci).
 * Columns: id:int, job_id:int, fname:varchar, lname:varchar, gender:varchar, email_id:varchar, mobile_no:varchar, phone_no:varchar, address:text, resume:varchar, created_date:timestamp
 *
 * Generated from the live schema (2026-11-13). Domain query methods are added
 * below this line by the step-4 port; keep $allowedFields in sync with the table.
 */
class JobApplicationsModel extends BaseModel
{
    protected $table         = 'job_applications';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'job_id',
        'fname',
        'lname',
        'gender',
        'email_id',
        'mobile_no',
        'phone_no',
        'address',
        'resume',
        'created_date',
    ];
}
