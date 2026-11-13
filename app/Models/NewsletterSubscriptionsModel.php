<?php

namespace App\Models;

/**
 * Table `newsletter_subscriptions` (MyISAM, utf8mb3_general_ci).
 * Columns: id:int, email:varchar, created_date:datetime
 *
 * Generated from the live schema (2026-11-13). Domain query methods are added
 * below this line by the step-4 port; keep $allowedFields in sync with the table.
 */
class NewsletterSubscriptionsModel extends BaseModel
{
    protected $table         = 'newsletter_subscriptions';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'email',
        'created_date',
    ];
}
