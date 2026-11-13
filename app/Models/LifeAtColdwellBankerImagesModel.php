<?php

namespace App\Models;

/**
 * Table `life_at_coldwell_banker_images` (InnoDB, latin1_swedish_ci).
 * Columns: id:int, image:varchar, created_date:timestamp
 *
 * Generated from the live schema (2026-11-13). Domain query methods are added
 * below this line by the step-4 port; keep $allowedFields in sync with the table.
 */
class LifeAtColdwellBankerImagesModel extends BaseModel
{
    protected $table         = 'life_at_coldwell_banker_images';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'image',
        'created_date',
    ];
}
