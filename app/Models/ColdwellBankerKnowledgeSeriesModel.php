<?php

namespace App\Models;

/**
 * Table `coldwell_banker_knowledge_series` (InnoDB, latin1_swedish_ci).
 * Columns: id:int, title:text, description:text, external_link:varchar, image:varchar, created_date:timestamp
 *
 * Generated from the live schema (2026-11-13). Domain query methods are added
 * below this line by the step-4 port; keep $allowedFields in sync with the table.
 */
class ColdwellBankerKnowledgeSeriesModel extends BaseModel
{
    protected $table         = 'coldwell_banker_knowledge_series';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'title',
        'description',
        'external_link',
        'image',
        'created_date',
    ];
}
