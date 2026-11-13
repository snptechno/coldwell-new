<?php

namespace App\Models;

/**
 * Table `learning` (InnoDB, latin1_swedish_ci).
 * Columns: id:int, overview:text, university:text, university_logo:varchar, last_updated_on:timestamp
 *
 * Generated from the live schema (2026-11-13). Domain query methods are added
 * below this line by the step-4 port; keep $allowedFields in sync with the table.
 */
class LearningModel extends BaseModel
{
    protected $table         = 'learning';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'overview',
        'university',
        'university_logo',
        'last_updated_on',
    ];

    /**
     * All overview rows, newest first (CI3 home_model::show_overview_list).
     *
     * @return list<array<string, mixed>>
     */
    public function overviewList(): array
    {
        return $this->orderBy('id', 'DESC')->findAll();
    }

    /**
     * One overview row (CI3 home_model::get_overview_data, and the
     * learning_development.php view, which reads id 2).
     */
    public function findOverview(int $id): ?array
    {
        return $this->builder()
            ->where('id', $id)
            ->get()
            ->getRowArray();
    }

    /**
     * Update an overview row (CI3 home_model::update_overview, DB part only).
     * $data: overview, university and, when a new logo was stored, university_logo
     * (the stored file name). Returns the affected row count, like CI3.
     *
     * @param array<string, mixed> $data
     */
    public function updateOverview(int $id, array $data): int
    {
        $this->update($id, $data);

        return $this->db->affectedRows();
    }
}
