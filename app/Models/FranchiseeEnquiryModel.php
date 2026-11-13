<?php

namespace App\Models;

/**
 * Table `franchisee_enquiry` (InnoDB, latin1_swedish_ci).
 * Columns: id:int, name:varchar, email:varchar, phone:varchar, profession:varchar, industry:varchar, location:varchar, created_on:timestamp
 *
 * Generated from the live schema (2026-11-13). Domain query methods are added
 * below this line by the step-4 port; keep $allowedFields in sync with the table.
 */
class FranchiseeEnquiryModel extends BaseModel
{
    protected $table         = 'franchisee_enquiry';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'name',
        'email',
        'phone',
        'profession',
        'industry',
        'location',
        'created_on',
    ];
}
