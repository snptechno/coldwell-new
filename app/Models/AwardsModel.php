<?php

namespace App\Models;

/**
 * Table `awards` (MyISAM, utf8mb3_general_ci).
 * Columns: id:int, name:varchar, image:varchar, created_date:datetime
 *
 * Generated from the live schema (2026-11-13). Domain query methods are added
 * below this line by the step-4 port; keep $allowedFields in sync with the table.
 */
class AwardsModel extends BaseModel
{
    protected $table         = 'awards';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'name',
        'image',
        'created_date',
    ];

    /**
     * Row id of the CMS page (table `pages`) that heads the public awards page.
     * CI3 used id 6, the same page as the newsletter page; kept as is.
     */
    public const PAGE_ID = 6;

    /**
     * All awards, newest first.
     * CI3: home_model::show_coldwell_banker_awards (admin list) and the
     * inline query in views/awards.php:47 (public page).
     */
    public function listNewestFirst(): array
    {
        return $this->builder()
            ->orderBy('id', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * One award by id, or null.
     * CI3: home_model::show_coldwell_banker_award_by_id (admin edit form) and
     * Ui_model::iframe_awards (public lightbox).
     */
    public function findById(int $id): ?array
    {
        return $this->builder()
            ->where('id', $id)
            ->get()
            ->getRowArray();
    }

    /**
     * Header content of the public awards page (CMS page id 6).
     * CI3: Ui_model::awards (returned row(); now an array).
     */
    public function pageInfo(): ?array
    {
        return $this->db->table('pages')
            ->where('id', self::PAGE_ID)
            ->get()
            ->getRowArray();
    }

    /**
     * Insert an award. $data already holds the stored image file name in
     * `image` (the upload itself is done by the controller).
     * Returns the affected row count, as CI3 did.
     * CI3: home_model::add_coldwell_banker_award (DB part).
     */
    public function createItem(array $data): int
    {
        $this->insert($data, false);

        return $this->db->affectedRows();
    }

    /**
     * Update an award; when a new image was stored, $newImage is written in a
     * second update, as in CI3. Returns the affected row count of the last update.
     * CI3: home_model::update_coldwell_banker_award (DB part).
     */
    public function updateItem(int $id, array $data, ?string $newImage = null): int
    {
        $this->update($id, $data);

        if ($newImage !== null) {
            $this->update($id, ['image' => $newImage]);
        }

        return $this->db->affectedRows();
    }

    /**
     * Delete an award and return the deleted row (null when it did not exist),
     * so the caller can remove the image file named in `image`.
     * CI3: home_model::delete_coldwell_banker_award (DB part).
     */
    public function deleteItem(int $id): ?array
    {
        $row = $this->findById($id);

        $this->builder()
            ->where('id', $id)
            ->delete();

        return $row;
    }
}
