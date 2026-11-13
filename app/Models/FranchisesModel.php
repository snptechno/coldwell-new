<?php

namespace App\Models;

/**
 * Table `franchises` (InnoDB, latin1_swedish_ci).
 * Columns: id:int, title:text, project_description:text, logo:varchar, mobile_no:varchar, email_id:varchar, website_url:text, video_url:text, city:int, location:varchar, address:text, latitude:varchar, longitude:varchar, speciality_market:varchar, description:text, speciality_markets:text, office_specialities:text, languages:text, location_description:text, franchise_status:varchar, external_link:varchar, is_active:int, created_date:timestamp
 *
 * Generated from the live schema (2026-11-13). Domain query methods are added
 * below this line by the step-4 port; keep $allowedFields in sync with the table.
 */
class FranchisesModel extends BaseModel
{
    protected $table         = 'franchises';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'title',
        'project_description',
        'logo',
        'mobile_no',
        'email_id',
        'website_url',
        'video_url',
        'city',
        'location',
        'address',
        'latitude',
        'longitude',
        'speciality_market',
        'description',
        'speciality_markets',
        'office_specialities',
        'languages',
        'location_description',
        'franchise_status',
        'external_link',
        'is_active',
        'created_date',
    ];
}
