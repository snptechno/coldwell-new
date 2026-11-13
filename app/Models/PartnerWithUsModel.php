<?php

namespace App\Models;

/**
 * Table `partner_with_us` (InnoDB, latin1_swedish_ci).
 * Columns: id:int, name:varchar, property_state:varchar, identity:varchar, property_city:varchar, email_id:varchar, locality:varchar, mobile_no:varchar, fsi_potential:varchar, fsi_potential_measurement:varchar, telephone:varchar, size_of_property:varchar, size_of_property_measurement:varchar, property_title:varchar, transaction:varchar, country:varchar, proposal:varchar, attachment_file:varchar, created_date:timestamp
 *
 * Generated from the live schema (2026-11-13). Domain query methods are added
 * below this line by the step-4 port; keep $allowedFields in sync with the table.
 */
class PartnerWithUsModel extends BaseModel
{
    protected $table         = 'partner_with_us';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'name',
        'property_state',
        'identity',
        'property_city',
        'email_id',
        'locality',
        'mobile_no',
        'fsi_potential',
        'fsi_potential_measurement',
        'telephone',
        'size_of_property',
        'size_of_property_measurement',
        'property_title',
        'transaction',
        'country',
        'proposal',
        'attachment_file',
        'created_date',
    ];
}
