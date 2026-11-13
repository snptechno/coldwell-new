<?php

namespace App\Models;

/**
 * Table `projects_contacts` (InnoDB, latin1_swedish_ci).
 * Columns: id:int, project_id:varchar, address:text, location:varchar, email_id:varchar, phone_no:varchar, fax:varchar, latitude:varchar, longitude:varchar, created_date:timestamp
 *
 * Generated from the live schema (2026-11-13). Domain query methods are added
 * below this line by the step-4 port; keep $allowedFields in sync with the table.
 */
class ProjectsContactsModel extends BaseModel
{
    protected $table         = 'projects_contacts';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'project_id',
        'address',
        'location',
        'email_id',
        'phone_no',
        'fax',
        'latitude',
        'longitude',
        'created_date',
    ];

    /**
     * Insert one project contact block and return its id (false on failure).
     * CI3: home_model::add_project_contact
     *
     * @param array<string, mixed> $data
     */
    public function addContact(array $data): int|false
    {
        $id = $this->insert($data, true);

        return $id === false ? false : (int) $id;
    }

    /**
     * Update one project contact block; returns the number of affected rows.
     * CI3: home_model::update_project_contact
     *
     * @param array<string, mixed> $data
     */
    public function updateContact(int $id, array $data): int
    {
        $this->update($id, $data);

        return $this->db->affectedRows();
    }

    /**
     * The contact block of a project (first matching row, no ORDER BY as in CI3), or null.
     * project_id is a varchar column, so the id is compared as a string like CI3 did.
     * CI3: home_model::getSingle_project_contact
     *
     * @return array<string, mixed>|null
     */
    public function findByProjectId(int $projectId): ?array
    {
        return $this->builder()
            ->where('project_id', (string) $projectId)
            ->get()
            ->getRowArray();
    }

    /**
     * Home page slider: active home page banners with their project and location.
     * No ORDER BY (as in CI3).
     * CI3: views/home1.php:49
     *
     * @return list<array<string, mixed>>
     */
    public function homeBannerSlides(): array
    {
        return $this->db->table('projects p')
            ->select('p.id, p.name, h.sub_title, c.location, h.image, p.is_luxurious')
            ->join('home_page_banners h', 'p.id = h.project_id', 'inner')
            ->join('projects_contacts c', 'p.id = c.project_id', 'inner')
            ->where('h.status', '1')
            ->get()
            ->getResultArray();
    }

    /**
     * Active projects of one type and status with their location, ordered by name.
     * $projectType: 'Residential' | 'Commercial';
     * $projectStatus: 'ongoing_project' | 'upcoming_project' | 'completed_project'.
     * CI3: views/home1.php:158, 173, 185, 205, 217, 229
     *
     * @return list<array<string, mixed>>
     */
    public function activeProjectsByTypeAndStatus(string $projectType, string $projectStatus): array
    {
        return $this->db->table('projects p')
            ->select('p.id, p.small_image, p.name, c.location')
            ->join('projects_contacts c', 'p.id = c.project_id', 'inner')
            ->where('p.status', '1')
            ->where('p.project_status', $projectStatus)
            ->where('p.project_type', $projectType)
            ->orderBy('p.name', 'asc')
            ->get()
            ->getResultArray();
    }
}
