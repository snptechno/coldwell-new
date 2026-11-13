<?php

namespace App\Models;

/**
 * Table `gallery` (InnoDB, latin1_swedish_ci).
 * Columns: id:int, title:text, image_name:varchar, date_time:date, group_id:int, status:int, created_date:timestamp
 *
 * Generated from the live schema (2026-11-13). Domain query methods are added
 * below this line by the step-4 port; keep $allowedFields in sync with the table.
 */
class GalleryModel extends BaseModel
{
    protected $table         = 'gallery';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'title',
        'image_name',
        'date_time',
        'group_id',
        'status',
        'created_date',
    ];

    /**
     * All gallery images, newest first (CI3 home_model::show_gallery_list).
     *
     * @return list<array<string, mixed>>
     */
    public function galleryList(): array
    {
        return $this->orderBy('id', 'DESC')->findAll();
    }

    /**
     * One gallery image (CI3 home_model::show_image_by_id).
     */
    public function findImage(int $id): ?array
    {
        return $this->builder()
            ->where('id', $id)
            ->get()
            ->getRowArray();
    }

    /**
     * Insert a gallery image (CI3 home_model::add_learning_gallery, DB part only).
     * $data: title and image_name (stored file name). The add form does not post
     * group_id; it is written as 0 explicitly (the implicit default a non-strict
     * server used) so the insert is accepted under STRICT_TRANS_TABLES.
     *
     * @param array<string, mixed> $data
     */
    public function addImage(array $data): int
    {
        $data += ['group_id' => 0, 'image_name' => ''];
        $this->insert($data);

        return $this->db->affectedRows();
    }

    /**
     * Update a gallery image (CI3 home_model::update_learning_gallery, DB part only).
     *
     * @param array<string, mixed> $data
     */
    public function updateImage(int $id, array $data): int
    {
        $this->update($id, $data);

        return $this->db->affectedRows();
    }

    /**
     * Delete a gallery image (CI3 home_model::delete_learning_gallery_image, DB part).
     * Returns the rows read before the delete (CI3 result_array() shape), so the
     * caller can remove uploads/learning_gallery/<image_name>.
     *
     * @return list<array<string, mixed>>
     */
    public function deleteImage(int $id): array
    {
        $rows = $this->builder()
            ->where('id', $id)
            ->get()
            ->getResultArray();

        $this->builder()->where('id', $id)->delete();

        return $rows;
    }

    /**
     * Assign an image to a gallery group
     * (CI3 home_model::update_group_id_life_at_coldwell_gallery).
     */
    public function updateGroupId(int $id, int $groupId): int
    {
        $this->builder()
            ->where('id', $id)
            ->update(['group_id' => $groupId]);

        return $this->db->affectedRows();
    }

    /**
     * All images of one group, newest first, any status
     * (CI3 Ui_model::load_group_images_gallery).
     *
     * @return list<array<string, mixed>>
     */
    public function imagesByGroup(int $groupId): array
    {
        return $this->where('group_id', $groupId)
            ->orderBy('id', 'DESC')
            ->findAll();
    }

    /**
     * Active images for the lightbox on the learning page, by group then date
     * (learning_development.php view query).
     *
     * @return list<array<string, mixed>>
     */
    public function activeLightboxImages(): array
    {
        return $this->where('status', '1')
            ->orderBy('group_id', 'ASC')
            ->orderBy('date_time', 'ASC')
            ->findAll();
    }
}
