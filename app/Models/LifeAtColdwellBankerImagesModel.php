<?php

namespace App\Models;

/**
 * Table `life_at_coldwell_banker_images` (InnoDB, latin1_swedish_ci).
 * Columns: id:int, image:varchar, created_date:timestamp
 *
 * Generated from the live schema (2026-11-13). Domain query methods are added
 * below this line by the step-4 port; keep $allowedFields in sync with the table.
 */
class LifeAtColdwellBankerImagesModel extends BaseModel
{
    protected $table         = 'life_at_coldwell_banker_images';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'image',
        'created_date',
    ];

    /**
     * All images, newest first (CI3 home_model::show_life_at_coldwell_banker_images).
     *
     * @return list<array<string, mixed>>
     */
    public function imageList(): array
    {
        return $this->orderBy('id', 'DESC')->findAll();
    }

    /**
     * All images in table order, for the career page carousels
     * (career.php / career-old.php / franchisees_openings.php view query, no ORDER BY).
     *
     * @return list<array<string, mixed>>
     */
    public function allImages(): array
    {
        return $this->findAll();
    }

    /**
     * One image (CI3 home_model::show_life_at_coldwell_banker_image_by_id).
     */
    public function findImage(int $id): ?array
    {
        return $this->builder()
            ->where('id', $id)
            ->get()
            ->getRowArray();
    }

    /**
     * Insert an image row once the file is stored
     * (CI3 home_model::add_life_at_coldwell_banker_image, DB part only).
     */
    public function addImage(string $filename): bool
    {
        return $this->insert(['image' => $filename]) !== false;
    }

    /**
     * Point a row at a newly stored file
     * (CI3 home_model::update_life_at_coldwell_banker_image, DB part only).
     */
    public function updateImage(int $id, string $filename): int
    {
        $this->builder()
            ->where('id', $id)
            ->update(['image' => $filename]);

        return $this->db->affectedRows();
    }

    /**
     * Delete an image row (CI3 home_model::delete_image_life_at_coldwell, DB part).
     * Returns the row read before the delete (CI3 row_array() shape), so the
     * caller can remove uploads/life_at_coldwell_banker/<image>.
     */
    public function deleteImage(int $id): ?array
    {
        $row = $this->findImage($id);

        $this->builder()->where('id', $id)->delete();

        return $row;
    }
}
