<?php

namespace App\Models;

/**
 * Table `nri_enquiry` (InnoDB, latin1_swedish_ci).
 * Columns: id:int, name:varchar, email:varchar, phone:varchar, country:varchar, message:text, created_on:timestamp
 *
 * Generated from the live schema (2026-11-13). Domain query methods are added
 * below this line by the step-4 port; keep $allowedFields in sync with the table.
 */
class NriEnquiryModel extends BaseModel
{
    protected $table         = 'nri_enquiry';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'name',
        'email',
        'phone',
        'country',
        'message',
        'created_on',
    ];
}
