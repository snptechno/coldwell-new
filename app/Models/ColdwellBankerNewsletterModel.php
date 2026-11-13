<?php

namespace App\Models;

/**
 * Table `coldwell_banker_newsletter` (InnoDB, latin1_swedish_ci).
 * Columns: id:int, title:text, description:text, external_link:varchar, image:varchar, created_date:timestamp
 *
 * Generated from the live schema (2026-11-13). Domain query methods are added
 * below this line by the step-4 port; keep $allowedFields in sync with the table.
 */
class ColdwellBankerNewsletterModel extends BaseModel
{
    protected $table         = 'coldwell_banker_newsletter';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'title',
        'description',
        'external_link',
        'image',
        'created_date',
    ];

    /** Page size of the public listing and of each "load more" request. */
    public const PAGE_SIZE = 8;

    /**
     * All rows, newest first (admin list).
     * CI3: home_model::show_coldwell_banker_newsletter.
     */
    public function listNewestFirst(): array
    {
        return $this->builder()
            ->orderBy('id', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * First page of the public listing, newest first.
     * CI3: inline query in views/newsletter.php:59 (LIMIT 0,8).
     */
    public function latest(int $limit = self::PAGE_SIZE): array
    {
        if ($limit <= 0) {
            return [];
        }

        return $this->builder()
            ->orderBy('id', 'DESC')
            ->limit($limit, 0)
            ->get()
            ->getResultArray();
    }

    /**
     * One row by id, or null.
     * CI3: home_model::show_coldwell_banker_newsletter_id (admin edit form) and Ui_model::iframe_newsletter (public lightbox).
     */
    public function findById(int $id): ?array
    {
        return $this->builder()
            ->where('id', $id)
            ->get()
            ->getRowArray();
    }

    /**
     * Total number of rows, in the CI3 shape ['count' => n].
     * CI3: Ui_model::count_total_newletter.
     */
    public function countTotal(): ?array
    {
        return $this->builder()
            ->select('COUNT(*) AS count', false)
            ->get()
            ->getRowArray();
    }

    /**
     * Next page of the public listing for the "load more" button: newest first,
     * starting at $startNo, at most PAGE_SIZE rows and never past $totalNo.
     * CI3: Ui_model::load_more_newsletter.
     */
    public function loadMore(int $totalNo, int $startNo): array
    {
        $countRow = $totalNo - $startNo;
        $limit    = $countRow < self::PAGE_SIZE ? $countRow : self::PAGE_SIZE;

        // CI3 sent "LIMIT start,0" (empty result) or a negative LIMIT (SQL error) here.
        if ($startNo < 0 || $limit <= 0) {
            return [];
        }

        return $this->builder()
            ->orderBy('id', 'DESC')
            ->limit($limit, $startNo)
            ->get()
            ->getResultArray();
    }

    /**
     * Insert a newsletter. $data already holds the stored image file name in
     * `image` (the upload itself is done by the controller).
     * Returns the affected row count, as CI3 did.
     * CI3: home_model::add_coldwell_banker_newsletter (DB part).
     */
    public function createItem(array $data): int
    {
        $this->insert($data, false);

        return $this->db->affectedRows();
    }

    /**
     * Update a newsletter; when a new image was stored, $newImage is written in a
     * second update, as in CI3. Returns the affected row count of the last update.
     * CI3: home_model::update_coldwell_banker_newsletter (DB part).
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
     * Delete a newsletter and return the deleted row (null when it did not exist),
     * so the caller can remove the image file named in `image`.
     * CI3: home_model::delete_coldwell_banker_newsletter (DB part).
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
