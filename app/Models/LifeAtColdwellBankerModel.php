<?php

namespace App\Models;

/**
 * Table `life_at_coldwell_banker` (InnoDB, latin1_swedish_ci).
 * Columns: id:int, title:varchar, description:text, created_date:timestamp
 *
 * Generated from the live schema (2026-11-13). Domain query methods are added
 * below this line by the step-4 port; keep $allowedFields in sync with the table.
 */
class LifeAtColdwellBankerModel extends BaseModel
{
    protected $table         = 'life_at_coldwell_banker';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'title',
        'description',
        'created_date',
    ];

    /**
     * All rows, newest first (CI3 home_model::show_life_at_coldwell_banker).
     *
     * @return list<array<string, mixed>>
     */
    public function entryList(): array
    {
        return $this->orderBy('id', 'DESC')->findAll();
    }

    /**
     * One row (CI3 home_model::show_life_at_coldwell_banker_by_id).
     */
    public function findEntry(int $id): ?array
    {
        return $this->builder()
            ->where('id', $id)
            ->get()
            ->getRowArray();
    }

    /**
     * The first row of the table, as the career pages read it
     * (career.php / career-old.php / franchisees_openings.php view query:
     * SELECT * without ORDER BY, first row taken).
     */
    public function firstEntry(): ?array
    {
        return $this->builder()->get()->getRowArray();
    }

    /**
     * Update a row (CI3 home_model::update_life_at_coldwell_banker).
     *
     * @param array<string, mixed> $data title, description
     */
    public function updateEntry(int $id, array $data): int
    {
        $this->update($id, $data);

        return $this->db->affectedRows();
    }
}
