<?php

namespace App\Models;

/**
 * Table `cities` (InnoDB, latin1_swedish_ci).
 * Columns: id:int, city_name:varchar, created_date:timestamp
 *
 * Generated from the live schema (2026-11-13). Domain query methods are added
 * below this line by the step-4 port; keep $allowedFields in sync with the table.
 */
class CitiesModel extends BaseModel
{
    protected $table         = 'cities';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'city_name',
        'created_date',
    ];

    /**
     * All cities, newest first (admin city list and franchise form dropdown).
     * CI3: home_model::get_cities_list and home_model::show_cities_list (identical)
     */
    public function listNewestFirst(): array
    {
        return $this->builder()
            ->orderBy('id', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Insert a city; returns the affected-row count (CI3 contract).
     * CI3: home_model::add_city
     */
    public function addCity(array $data): int
    {
        $this->insert($data, false);

        return $this->db->affectedRows();
    }

    /**
     * Update a city; returns the affected-row count (CI3 contract).
     * CI3: home_model::update_city
     */
    public function updateCity(int $id, array $data): int
    {
        $this->update($id, $data);

        return $this->db->affectedRows();
    }

    /**
     * Delete a city. Franchises pointing at it are left untouched, as before.
     * CI3: home_model::delete_city
     */
    public function deleteCity(int $id): bool
    {
        return (bool) $this->builder()->where('id', $id)->delete();
    }

    /**
     * Distinct cities that have at least one active franchise, by name
     * (public "our franchisee" page: city dropdown and city grid).
     * CI3: views/our_franchisee.php:143-144 (and the same query at :38-39,
     * which selected the two columns in the order city_name, id)
     */
    public function withActiveFranchises(): array
    {
        return $this->db->table('franchises f')
            ->distinct()
            ->select('c.id, c.city_name')
            ->join('cities c', 'f.city = c.id')
            ->where('f.is_active', 1)
            ->orderBy('c.city_name', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * ['city_name' => ...] for one city, null when it does not exist
     * (public franchise details page).
     * CI3: views/our_franchisee_details.php:377-378 (inline query)
     */
    public function findName(int $id): ?array
    {
        return $this->builder()
            ->select('city_name')
            ->where('id', $id)
            ->get()
            ->getRowArray();
    }
}
