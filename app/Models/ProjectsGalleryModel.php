<?php

namespace App\Models;

/**
 * Table `projects_gallery` (InnoDB, latin1_swedish_ci).
 * Columns: id:int, project_id:int, name:varchar, image:varchar, status:tinyint, created_date:timestamp
 *
 * Generated from the live schema (2026-11-13). Domain query methods are added
 * below this line by the step-4 port; keep $allowedFields in sync with the table.
 */
class ProjectsGalleryModel extends BaseModel
{
    protected $table         = 'projects_gallery';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'project_id',
        'name',
        'image',
        'status',
        'created_date',
    ];

    /**
     * All gallery images of one project, newest first (admin gallery list).
     * CI3: home_model::show_projects_gallery_list().
     */
    public function listByProject(int $projectId): array
    {
        return $this->where('project_id', $projectId)
            ->orderBy('id', 'DESC')
            ->findAll();
    }

    /**
     * One gallery image, only if it belongs to the given project.
     * CI3: home_model::getSingle_project_gallery_image().
     */
    public function findForProject(int $projectId, int $id): ?array
    {
        return $this->where('project_id', $projectId)
            ->where('id', $id)
            ->first();
    }

    /**
     * Inserts a gallery row (name, project_id, created_date) and returns its id.
     * The uploaded files are stored afterwards with setImageFile().
     * CI3: home_model::add_project_gallery_image() (DB part).
     */
    public function addImage(array $data): int
    {
        $this->insert($data);

        return (int) $this->getInsertID();
    }

    /**
     * Updates name/project_id of a gallery row; returns the affected row count.
     * CI3: home_model::update_project_gallery_image() (DB part).
     */
    public function updateImage(int $id, array $data): int
    {
        $this->builder()
            ->where('id', $id)
            ->update(array_intersect_key($data, array_flip($this->allowedFields)));

        return $this->db->affectedRows();
    }

    /**
     * Records the stored file name of the large image of a gallery row.
     * CI3: second UPDATE in add_/update_project_gallery_image().
     */
    public function setImageFile(int $id, string $image): bool
    {
        return $this->builder()
            ->where('id', $id)
            ->update(['image' => $image]);
    }

    /**
     * Deletes a gallery row and returns it as it was (null when not found), so the
     * caller can remove the stored file.
     * CI3: home_model::delete_project_gallery_image() (DB part).
     */
    public function deleteImage(int $id): ?array
    {
        $row = $this->where('id', $id)->first();

        $this->builder()->where('id', $id)->delete();

        return $row;
    }
}
