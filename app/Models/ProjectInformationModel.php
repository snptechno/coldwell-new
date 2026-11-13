<?php

namespace App\Models;

/**
 * Table `project_information` (InnoDB, latin1_swedish_ci).
 * Columns: id:int, project_id:int, bedrooms:text, saleable_area:text, possession:date, developer:text, rating:varchar, towers:varchar, created_date:timestamp
 *
 * Generated from the live schema (2026-11-13). Domain query methods are added
 * below this line by the step-4 port; keep $allowedFields in sync with the table.
 */
class ProjectInformationModel extends BaseModel
{
    protected $table         = 'project_information';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'project_id',
        'bedrooms',
        'saleable_area',
        'possession',
        'developer',
        'rating',
        'towers',
        'created_date',
    ];

    /**
     * The information block (bedrooms, area, possession, ...) of one project.
     * CI3: home_model::getSingle_projects_information().
     */
    public function findByProject(int $projectId): ?array
    {
        return $this->where('project_id', $projectId)->first();
    }

    /**
     * Inserts the information block of a project; returns the new id.
     * CI3: home_model::add_project_information().
     */
    public function addInformation(array $data): int
    {
        $this->insert($data);

        return (int) $this->getInsertID();
    }

    /**
     * Updates an information block by its own id; returns the affected row count.
     * CI3: home_model::update_project_information().
     */
    public function updateInformation(int $id, array $data): int
    {
        $this->builder()
            ->where('id', $id)
            ->update(array_intersect_key($data, array_flip($this->allowedFields)));

        return $this->db->affectedRows();
    }
}
