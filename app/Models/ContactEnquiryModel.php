<?php

namespace App\Models;

/**
 * Table `contact_enquiry` (MyISAM, latin1_swedish_ci).
 * Columns: id:int, name:varchar, email:varchar, phone:varchar, message:text, interested_in:varchar, created_on:timestamp
 *
 * Generated from the live schema (2026-11-13). Domain query methods are added
 * below this line by the step-4 port; keep $allowedFields in sync with the table.
 */
class ContactEnquiryModel extends BaseModel
{
    protected $table         = 'contact_enquiry';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'name',
        'email',
        'phone',
        'message',
        'interested_in',
        'created_on',
    ];
}
