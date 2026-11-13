<?php

namespace App\Models;

/**
 * Table `jobs` (InnoDB, latin1_swedish_ci).
 * Columns: id:int, type:varchar, position:varchar, qualification:text, max_experience:varchar, min_experience:varchar, location:varchar, description:text, status:tinyint, created_date:timestamp
 *
 * Generated from the live schema (2026-11-13). Domain query methods are added
 * below this line by the step-4 port; keep $allowedFields in sync with the table.
 */
class JobsModel extends BaseModel
{
    protected $table         = 'jobs';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'type',
        'position',
        'qualification',
        'max_experience',
        'min_experience',
        'location',
        'description',
        'status',
        'created_date',
    ];

    /** Opening type values stored in `jobs.type`. */
    public const TYPE_COLDWELL   = 'COLDWELL BANKER INDIA OPENINGS';
    public const TYPE_FRANCHISEE = 'FRANCHISEES OPENINGS';

    /**
     * All job openings, newest first (admin list).
     * CI3: home_model::show_job_openings
     */
    public function listAll(): array
    {
        return $this->builder()
            ->orderBy('id', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * One job opening by id, or null.
     * CI3: home_model::getSingle_job, Ui_model::current_openings_description,
     *      Ui_model::get_position_name (identical queries)
     */
    public function findJob(int $id): ?array
    {
        return $this->builder()
            ->where('id', $id)
            ->get()
            ->getRowArray();
    }

    /**
     * Id of the first job whose position matches (column collation is
     * case-insensitive; the value is lower-cased as in CI3), or null.
     * CI3: home_model::checkDuplicate_job
     */
    public function findIdByPosition(string $position): ?int
    {
        $row = $this->builder()
            ->where('position', strtolower($position))
            ->get()
            ->getRowArray();

        return $row === null ? null : (int) $row['id'];
    }

    /**
     * Active openings of one type, newest first (public career pages).
     * CI3: inline queries in views/career.php:56, views/career-old.php:56,
     *      views/franchisees_openings.php:57
     */
    public function activeByType(string $type): array
    {
        return $this->builder()
            ->where('type', $type)
            ->where('status', '1')
            ->orderBy('id', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Insert a job opening; returns the new id.
     * CI3: home_model::add_job
     */
    public function createJob(array $data): int
    {
        $this->insert($data);

        return (int) $this->getInsertID();
    }

    /**
     * Update a job opening; returns the number of affected rows.
     * CI3: home_model::update_job
     */
    public function updateJob(int $id, array $data): int
    {
        $this->update($id, $data);

        return $this->db->affectedRows();
    }

    /**
     * Delete a job opening.
     * CI3: home_model::delete_job_opening
     */
    public function deleteJob(int $id): bool
    {
        return (bool) $this->builder()
            ->where('id', $id)
            ->delete();
    }
}
