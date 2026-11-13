<?php

namespace App\Models;

/**
 * Table `real_estate_specialist` (InnoDB, latin1_swedish_ci).
 * Columns: id:int, franchise_id:int, name:varchar, title:text, description:text, image:varchar, created_date:timestamp
 *
 * Generated from the live schema (2026-11-13). Domain query methods are added
 * below this line by the step-4 port; keep $allowedFields in sync with the table.
 */
class RealEstateSpecialistModel extends BaseModel
{
    protected $table         = 'real_estate_specialist';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'franchise_id',
        'name',
        'title',
        'description',
        'image',
        'created_date',
    ];

    /**
     * Specialists of one franchise with the franchise title (admin list).
     * No ORDER BY, as before.
     * CI3: home_model::show_specialist_list
     */
    public function listWithFranchiseTitle(int $franchiseId): array
    {
        return $this->db->table('real_estate_specialist r')
            ->select('r.*, f.title as franchise')
            ->join('franchises f', 'f.id = r.franchise_id')
            ->where('f.id', $franchiseId)
            ->get()
            ->getResultArray();
    }

    /**
     * Specialists of one franchise by name (public franchise details page).
     * CI3: views/our_franchisee_details.php:347-348 (inline query)
     */
    public function byFranchiseOrderedByName(int $franchiseId): array
    {
        return $this->builder()
            ->select('id, name, title, description, image')
            ->where('franchise_id', $franchiseId)
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Insert a specialist; returns the affected-row count (CI3 contract).
     * The `image` file name, if any, is set by the caller after the upload.
     * CI3: home_model::add_specialist (DB part)
     */
    public function addSpecialist(array $data): int
    {
        $this->insert($data, false);

        return $this->db->affectedRows();
    }

    /**
     * Update a specialist; returns the affected-row count (CI3 contract).
     * CI3: home_model::update_specialist (DB part)
     */
    public function updateSpecialist(int $id, array $data): int
    {
        $this->update($id, $data);

        return $this->db->affectedRows();
    }

    /**
     * Delete a specialist. Returns the row(s) as they were before the delete so
     * the caller can remove the stored image.
     * CI3: home_model::delete_specialist (DB part)
     */
    public function deleteSpecialist(int $id): array
    {
        $rows = $this->builder()->where('id', $id)->get()->getResultArray();
        $this->builder()->where('id', $id)->delete();

        return $rows;
    }
}
