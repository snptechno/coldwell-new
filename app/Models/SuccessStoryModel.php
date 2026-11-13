<?php

namespace App\Models;

/**
 * Table `success_story` (MyISAM, utf8mb3_general_ci).
 * Columns: id:int, title:varchar, description:text, video_url:varchar, is_active:tinyint, created_date:datetime
 *
 * Generated from the live schema (2026-11-13). Domain query methods are added
 * below this line by the step-4 port; keep $allowedFields in sync with the table.
 */
class SuccessStoryModel extends BaseModel
{
    protected $table         = 'success_story';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'title',
        'description',
        'video_url',
        'is_active',
        'created_date',
    ];

    // ------------------------------------------------------------------
    // Step-4 port of home_model.php (success_story). See docs/port-map/videos.md.
    // ------------------------------------------------------------------

    /**
     * Admin list, newest first.
     * CI3: home_model::success_stories_videos() (home_model.php:2043).
     */
    public function allLatestFirst(): array
    {
        return $this->orderBy('id', 'DESC')->findAll();
    }

    /**
     * One row for the admin edit form, or null.
     * CI3: home_model::show_success_story_video_by_id($id).
     */
    public function byId(int $id): ?array
    {
        return $this->builder()->where('id', $id)->get()->getRowArray();
    }

    /**
     * Insert a story video (title, description, video_url, created_date).
     * CI3: home_model::add_success_story_video($data) - returned the insert() boolean.
     */
    public function addVideo(array $data): bool
    {
        return (bool) $this->insert($data, false);
    }

    /**
     * Update a story video; returns the affected row count like CI3.
     * CI3: home_model::update_success_story_video($id, $data).
     */
    public function updateVideo(int $id, array $data): int
    {
        $this->update($id, $data);

        return $this->db->affectedRows();
    }

    /**
     * Delete a story video by id.
     * CI3: home_model::delete_success_story_videos($id) - returned an undefined variable; now returns the delete result.
     */
    public function deleteVideo(int $id): bool
    {
        return (bool) $this->builder()->where('id', $id)->delete();
    }

    /**
     * Enable (1) / disable (0) a story; returns the affected row count like CI3.
     * CI3: home_model::change_status_success_story($id, $status).
     */
    public function changeStatus(int $id, int $status): int
    {
        $this->setStatus($id, $status, 'is_active');

        return $this->db->affectedRows();
    }

    /**
     * Active success stories for the public carousel, newest first.
     * CI3 in-view query: views/career.php:130, views/career-old.php:130,
     * views/franchisees_openings.php:124.
     */
    public function activeLatestFirst(): array
    {
        return $this->builder()
            ->where('is_active', 1)
            ->orderBy('id', 'DESC')
            ->get()
            ->getResultArray();
    }
}
