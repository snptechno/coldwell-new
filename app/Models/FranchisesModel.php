<?php

namespace App\Models;

/**
 * Table `franchises` (InnoDB, latin1_swedish_ci).
 * Columns: id:int, title:text, project_description:text, logo:varchar, mobile_no:varchar, email_id:varchar, website_url:text, video_url:text, city:int, location:varchar, address:text, latitude:varchar, longitude:varchar, speciality_market:varchar, description:text, speciality_markets:text, office_specialities:text, languages:text, location_description:text, franchise_status:varchar, external_link:varchar, is_active:int, created_date:timestamp
 *
 * Generated from the live schema (2026-11-13). Domain query methods are added
 * below this line by the step-4 port; keep $allowedFields in sync with the table.
 */
class FranchisesModel extends BaseModel
{
    protected $table         = 'franchises';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'title',
        'project_description',
        'logo',
        'mobile_no',
        'email_id',
        'website_url',
        'video_url',
        'city',
        'location',
        'address',
        'latitude',
        'longitude',
        'speciality_market',
        'description',
        'speciality_markets',
        'office_specialities',
        'languages',
        'location_description',
        'franchise_status',
        'external_link',
        'is_active',
        'created_date',
    ];

    /**
     * Admin franchise list with the city name, newest first.
     * Inner join: a franchise whose city row is missing is not listed (as before).
     * CI3: home_model::show_franchises_list
     */
    public function listWithCity(): array
    {
        return $this->db->table('franchises f')
            ->select('f.*, c.city_name')
            ->join('cities c', 'f.city = c.id')
            ->orderBy('f.id', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * One franchise with its city name (admin edit form).
     * CI3: home_model::show_franchise_by_id
     */
    public function findWithCity(int $id): ?array
    {
        return $this->db->table('franchises f')
            ->select('f.*, c.city_name')
            ->join('cities c', 'f.city = c.id')
            ->where('f.id', $id)
            ->get()
            ->getRowArray();
    }

    /**
     * Insert a franchise; returns the affected-row count (CI3 contract).
     * `languages` / `office_specialities` arrive as arrays from the form and are
     * stored comma-separated; when absent they are not written. The `logo` file
     * name, if any, is set by the caller after the upload.
     * CI3: home_model::add_franchise (DB part)
     */
    public function addFranchise(array $data): int
    {
        $this->insert($this->normaliseMultiValues($data), false);

        return $this->db->affectedRows();
    }

    /**
     * Update a franchise; returns the affected-row count (CI3 contract).
     * CI3: home_model::update_franchise (DB part)
     */
    public function updateFranchise(int $id, array $data): int
    {
        $this->update($id, $this->normaliseMultiValues($data));

        return $this->db->affectedRows();
    }

    /**
     * Delete a franchise. Returns the row(s) as they were before the delete
     * (CI3 returned result_array() of that id) so the caller can clean up files.
     * Owners, specialists and testimonials of the franchise are left untouched,
     * as before.
     * CI3: home_model::delete_franchise (DB part)
     */
    public function deleteFranchise(int $id): array
    {
        $rows = $this->builder()->where('id', $id)->get()->getResultArray();
        $this->builder()->where('id', $id)->delete();

        return $rows;
    }

    /**
     * ['id' => ...] when the franchise exists, null otherwise
     * (guard of the public franchise details page).
     * CI3: Ui_model::check_franchisee
     */
    public function findIdOnly(int $id): ?array
    {
        return $this->builder()
            ->select('id')
            ->where('id', $id)
            ->get()
            ->getRowArray();
    }

    /**
     * Distinct locations of the active franchises in a city (AJAX dropdown).
     * No ORDER BY, as before.
     * CI3: Ui_model::get_franchisee_location_by_city
     */
    public function activeLocationsByCity(int $cityId): array
    {
        return $this->builder()
            ->select('location')
            ->where('city', $cityId)
            ->where('is_active', 1)
            ->groupBy('location')
            ->get()
            ->getResultArray();
    }

    /**
     * Franchise search by city (search_id 1): active franchises of the city with
     * the city name, optionally limited to one location (no location filter when
     * it is null or ''). No ORDER BY, as before.
     * CI3: Ui_model::search_franchisee_by_location, $search_id == 1
     */
    public function searchByCity(int $cityId, ?string $location = null): array
    {
        $builder = $this->db->table('franchises f')
            ->select('f.*, c.city_name')
            ->join('cities c', 'f.city = c.id')
            ->where('f.is_active', 1)
            ->where('f.city', $cityId);

        if ($location !== null && $location !== '') {
            $builder->where('f.location', $location);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Franchise search by the "title, location" autocomplete text (search_id 2).
     * The text is split on commas: part 0 is matched anywhere in the title,
     * part 1 ('' when missing) is a LIKE pattern on location. The original
     * operator precedence is kept:
     *   (is_active = 1 AND title LIKE ...) OR location LIKE ...
     * Values are bound as-is, so LIKE wildcards behave as before. No ORDER BY.
     * CI3: Ui_model::search_franchisee_by_location, $search_id == 2
     */
    public function searchByTitleOrLocation(string $search): array
    {
        $parts = explode(',', $search);

        return $this->db->query(
            'SELECT * FROM franchises WHERE is_active = 1 AND title LIKE ? OR location LIKE ?',
            ['%' . $parts[0] . '%', $parts[1] ?? '']
        )->getResultArray();
    }

    /**
     * Title and location of active franchises whose title contains the text
     * (autocomplete). No ORDER BY, as before.
     * CI3: Ui_model::get_franchisee_name
     */
    public function activeTitlesLike(string $title): array
    {
        return $this->db->query(
            'SELECT title, location FROM franchises WHERE is_active = 1 AND title LIKE ?',
            ['%' . $title . '%']
        )->getResultArray();
    }

    /**
     * All active franchises (map markers). No ORDER BY, as before.
     * CI3: Ui_model::get_all_franchisee_location
     */
    public function allActive(): array
    {
        return $this->builder()
            ->where('is_active', 1)
            ->get()
            ->getResultArray();
    }

    /**
     * Active franchises of one city, by title (public "our franchisee" grid).
     * CI3: views/our_franchisee.php:152-154 (inline query)
     */
    public function activeByCity(int $cityId): array
    {
        return $this->builder()
            ->select('id, title, location, external_link, franchise_status')
            ->where('is_active', 1)
            ->where('city', $cityId)
            ->orderBy('title', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Franchise row merged with the columns of one of its owners (LEFT JOIN,
     * LIMIT 1, no ORDER BY - as before). franchises.* is selected last, so on
     * shared column names (id, description, created_date, ...) the franchise
     * value wins, exactly like the CI3 row_array().
     * CI3: views/our_franchisee_details.php:2-3 (inline query)
     */
    public function detailsWithOwner(int $id): ?array
    {
        return $this->db->table('franchises')
            ->select('franchises_owners.*, franchises.*')
            ->join('franchises_owners', 'franchises_owners.franchise_id = franchises.id', 'left')
            ->where('franchises.id', $id)
            ->limit(1)
            ->get()
            ->getRowArray();
    }

    /**
     * Multi-select fields are stored comma-separated and dropped when absent
     * (CI3 used isset(), so a null value is dropped too).
     */
    private function normaliseMultiValues(array $data): array
    {
        foreach (['languages', 'office_specialities'] as $field) {
            if (isset($data[$field])) {
                $data[$field] = is_array($data[$field]) ? implode(',', $data[$field]) : (string) $data[$field];
            } else {
                unset($data[$field]);
            }
        }

        return $data;
    }
}
