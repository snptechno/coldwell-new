<?php

namespace App\Models;

/**
 * Table `franchisee_project_enquiry` (MyISAM, utf8mb3_general_ci).
 * Columns: id:int, franchisee_id:int, name:varchar, email_id:varchar, phone:varchar, interested_in:varchar, created_date:datetime
 *
 * Generated from the live schema (2026-11-13). Domain query methods are added
 * below this line by the step-4 port; keep $allowedFields in sync with the table.
 */
class FranchiseeProjectEnquiryModel extends BaseModel
{
    protected $table         = 'franchisee_project_enquiry';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'franchisee_id',
        'name',
        'email_id',
        'phone',
        'interested_in',
        'created_date',
    ];
}
