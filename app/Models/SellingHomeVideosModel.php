<?php

namespace App\Models;

/**
 * Table `selling_home_videos` (InnoDB, latin1_swedish_ci).
 * Columns: id:int, title:varchar, description:text, video_url:varchar, is_active:tinyint, created_date:timestamp
 *
 * Generated from the live schema (2026-11-13). Domain query methods are added
 * below this line by the step-4 port; keep $allowedFields in sync with the table.
 */
class SellingHomeVideosModel extends BaseModel
{
    protected $table         = 'selling_home_videos';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'title',
        'description',
        'video_url',
        'is_active',
        'created_date',
    ];

    // ------------------------------------------------------------------
    // Step-4 port of home_model.php (selling_home_videos). See docs/port-map/videos.md.
    // ------------------------------------------------------------------

    /**
     * Admin list, newest first.
     * CI3: home_model::show_selling_home_videos() (home_model.php:927).
     */
    public function allLatestFirst(): array
    {
        return $this->orderBy('id', 'DESC')->findAll();
    }

    /**
     * One row for the admin edit form, or null.
     * CI3: home_model::show_selling_home_video_by_id($id).
     */
    public function byId(int $id): ?array
    {
        return $this->builder()->where('id', $id)->get()->getRowArray();
    }

    /**
     * Insert a video (title, description, video_url).
     * CI3: home_model::add_selling_home_video($data) - returned the insert() boolean.
     */
    public function addVideo(array $data): bool
    {
        return (bool) $this->insert($data, false);
    }

    /**
     * Update a video; returns the affected row count like CI3.
     * CI3: home_model::update_selling_home_video($id, $data).
     */
    public function updateVideo(int $id, array $data): int
    {
        $this->update($id, $data);

        return $this->db->affectedRows();
    }

    /**
     * Delete a video by id.
     * CI3: home_model::delete_selling_videos($id) - returned an undefined variable; now returns the delete result.
     */
    public function deleteVideo(int $id): bool
    {
        return (bool) $this->builder()->where('id', $id)->delete();
    }

    /**
     * Enable (1) / disable (0) a video; returns the affected row count like CI3.
     * CI3: home_model::change_status_selling_videos($id, $status).
     */
    public function changeStatus(int $id, int $status): int
    {
        $this->setStatus($id, $status, 'is_active');

        return $this->db->affectedRows();
    }

    /**
     * Active videos for the public page (no ORDER BY, as in CI3).
     * CI3 in-view query: views/selling_a_home.php:61.
     */
    public function activeVideos(): array
    {
        return $this->builder()
            ->select('id, title, description, video_url')
            ->where('is_active', 1)
            ->get()
            ->getResultArray();
    }
}
