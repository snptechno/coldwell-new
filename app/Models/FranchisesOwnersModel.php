<?php

namespace App\Models;

/**
 * Table `franchises_owners` (MyISAM, utf8mb3_general_ci).
 * Columns: id:int, franchise_id:int, name:varchar, description:text, gender:varchar, position_name:varchar, image:varchar, status:tinyint, created_date:datetime
 *
 * Generated from the live schema (2026-11-13). Domain query methods are added
 * below this line by the step-4 port; keep $allowedFields in sync with the table.
 */
class FranchisesOwnersModel extends BaseModel
{
    protected $table         = 'franchises_owners';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'franchise_id',
        'name',
        'description',
        'gender',
        'position_name',
        'image',
        'status',
        'created_date',
    ];

    /**
     * Owners of one franchise with the franchise title, newest first (admin list).
     * CI3: home_model::show_franchise_owners_list
     */
    public function listWithFranchiseTitle(int $franchiseId): array
    {
        return $this->db->table('franchises_owners fo')
            ->select('fo.*, f.title as franchise')
            ->join('franchises f', 'f.id = fo.franchise_id')
            ->where('f.id', $franchiseId)
            ->orderBy('fo.id', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Owners of one franchise, oldest first (public franchise details page).
     * CI3: views/our_franchisee_details.php:318-319 (inline query)
     */
    public function byFranchise(int $franchiseId): array
    {
        return $this->builder()
            ->where('franchise_id', $franchiseId)
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Insert an owner; returns the affected-row count (CI3 contract).
     * The `image` file name, if any, is set by the caller after the upload.
     * CI3: home_model::add_franchise_owner (DB part)
     */
    public function addOwner(array $data): int
    {
        $this->insert($data, false);

        return $this->db->affectedRows();
    }

    /**
     * Update an owner; returns the affected-row count (CI3 contract).
     * CI3: home_model::update_franchise_owner (DB part)
     */
    public function updateOwner(int $id, array $data): int
    {
        $this->update($id, $data);

        return $this->db->affectedRows();
    }

    /**
     * Delete an owner. Returns the row(s) as they were before the delete so the
     * caller can remove the stored image.
     * CI3: home_model::delete_franchise_owner (DB part)
     */
    public function deleteOwner(int $id): array
    {
        $rows = $this->builder()->where('id', $id)->get()->getResultArray();
        $this->builder()->where('id', $id)->delete();

        return $rows;
    }
}
