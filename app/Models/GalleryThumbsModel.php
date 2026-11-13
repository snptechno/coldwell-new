<?php

namespace App\Models;

/**
 * Table `gallery_thumbs` (InnoDB, latin1_swedish_ci).
 * Columns: id:int, title:text, date_time:date, image_name:varchar, group_id:int, status:int, created_date:timestamp
 *
 * Generated from the live schema (2026-11-13). Domain query methods are added
 * below this line by the step-4 port; keep $allowedFields in sync with the table.
 */
class GalleryThumbsModel extends BaseModel
{
    protected $table         = 'gallery_thumbs';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'title',
        'date_time',
        'image_name',
        'group_id',
        'status',
        'created_date',
    ];

    /**
     * All gallery thumbnails, newest first (CI3 home_model::show_gallery_thumbnails_list).
     *
     * @return list<array<string, mixed>>
     */
    public function thumbnailList(): array
    {
        return $this->orderBy('id', 'DESC')->findAll();
    }

    /**
     * One thumbnail (CI3 home_model::show_gallery_thumbnail_by_id).
     */
    public function findThumbnail(int $id): ?array
    {
        return $this->builder()
            ->where('id', $id)
            ->get()
            ->getRowArray();
    }

    /**
     * Insert a thumbnail (CI3 home_model::add_gallery_thumbnail, DB part only).
     * $data: title, date_time and image_name (stored file name). The add form does
     * not post group_id; it is written as 0 explicitly (the implicit default a
     * non-strict server used) so the insert is accepted under STRICT_TRANS_TABLES.
     *
     * @param array<string, mixed> $data
     */
    public function addThumbnail(array $data): int
    {
        $data += ['group_id' => 0, 'image_name' => ''];
        $this->insert($data);

        return $this->db->affectedRows();
    }

    /**
     * Update a thumbnail (CI3 home_model::update_gallery_thumbnail, DB part only).
     *
     * @param array<string, mixed> $data
     */
    public function updateThumbnail(int $id, array $data): int
    {
        $this->update($id, $data);

        return $this->db->affectedRows();
    }

    /**
     * Delete a thumbnail (CI3 home_model::delete_gallery_thumbnail_image, DB part).
     * Returns the rows read before the delete (CI3 result_array() shape), so the
     * caller can remove uploads/learning_gallery_thumb/<image_name>.
     *
     * @return list<array<string, mixed>>
     */
    public function deleteThumbnail(int $id): array
    {
        $rows = $this->builder()
            ->where('id', $id)
            ->get()
            ->getResultArray();

        $this->builder()->where('id', $id)->delete();

        return $rows;
    }

    /**
     * Assign a thumbnail to a gallery group
     * (CI3 home_model::update_group_id_life_at_coldwell_gallery_thumb).
     */
    public function updateGroupId(int $id, int $groupId): int
    {
        $this->builder()
            ->where('id', $id)
            ->update(['group_id' => $groupId]);

        return $this->db->affectedRows();
    }

    /**
     * One active thumbnail per gallery group (the lowest id of each group),
     * newest date first (learning_development.php view query).
     *
     * @return list<array<string, mixed>>
     */
    public function firstActivePerGroup(): array
    {
        $sql = 'SELECT t.* FROM gallery_thumbs t'
            . ' INNER JOIN (SELECT MIN(id) AS id FROM gallery_thumbs WHERE status = ? GROUP BY group_id) firsts'
            . ' ON firsts.id = t.id'
            . ' ORDER BY t.date_time DESC';

        return $this->db->query($sql, ['1'])->getResultArray();
    }
}
