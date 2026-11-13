<?php

namespace App\Models;

/**
 * Table `pages` (MyISAM, latin1_swedish_ci).
 * Columns: id:int, name:varchar, content:text, title:varchar, meta_description:text, meta_keywords:text, status:enum, created_on:timestamp
 *
 * Generated from the live schema (2026-11-13). Domain query methods are added
 * below this line by the step-4 port; keep $allowedFields in sync with the table.
 */
class PagesModel extends BaseModel
{
    protected $table         = 'pages';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'name',
        'content',
        'title',
        'meta_description',
        'meta_keywords',
        'status',
        'created_on',
    ];
}
