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

    /**
     * Insert one subscription and return its id (false on failure).
     * CI3: Ui_model::newsletter_subscription
     *
     * @param array<string, mixed> $data
     */
    public function addSubscription(array $data): int|false
    {
        $id = $this->insert($data, true);

        return $id === false ? false : (int) $id;
    }

    /**
     * All rows, newest id first.
     * CI3: home_model::newsletter_list
     *
     * @return list<array<string, mixed>>
     */
    public function allNewestFirst(): array
    {
        return $this->orderBy('id', 'desc')->findAll();
    }

    /**
     * Delete one row by id.
     * CI3: home_model::delete_newslettes_subscriber
     */
    public function deleteById(int $id): bool
    {
        return (bool) $this->builder()->where('id', $id)->delete();
    }
}
