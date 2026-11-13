<?php

namespace App\Models;

/**
 * Table `job_applications` (InnoDB, latin1_swedish_ci).
 * Columns: id:int, job_id:int, fname:varchar, lname:varchar, gender:varchar, email_id:varchar, mobile_no:varchar, phone_no:varchar, address:text, resume:varchar, created_date:timestamp
 *
 * Generated from the live schema (2026-11-13). Domain query methods are added
 * below this line by the step-4 port; keep $allowedFields in sync with the table.
 */
class JobApplicationsModel extends BaseModel
{
    protected $table         = 'job_applications';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'job_id',
        'fname',
        'lname',
        'gender',
        'email_id',
        'mobile_no',
        'phone_no',
        'address',
        'resume',
        'created_date',
    ];

    /**
     * All applications that belong to an existing job, newest first, with the
     * job's position and type (as `position_type`).
     * CI3: home_model::show_job_application
     */
    public function listWithPosition(): array
    {
        return $this->db->table('job_applications')
            ->select('job_applications.*, jobs.position, jobs.type AS position_type')
            ->join('jobs', 'job_applications.job_id = jobs.id', 'inner')
            ->orderBy('job_applications.id', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * One application with its job's position (as `position_name`), or null
     * when the application or its job does not exist.
     * CI3: home_model::show_job_candidate_details
     */
    public function detailsWithPosition(int $id): ?array
    {
        return $this->db->table('job_applications')
            ->select('job_applications.*, jobs.position AS position_name')
            ->join('jobs', 'job_applications.job_id = jobs.id', 'inner')
            ->where('job_applications.id', $id)
            ->get()
            ->getRowArray();
    }

    /**
     * One application row by id, or null (admin resume download).
     * CI3: home_model::get_candidate_resume
     */
    public function findApplication(int $id): ?array
    {
        return $this->builder()
            ->where('id', $id)
            ->orderBy('id', 'DESC')
            ->get()
            ->getRowArray();
    }

    /**
     * One application joined with its job, or null. Both tables are selected
     * with `*`, so the shared column names (`id`, `created_date`) carry the
     * jobs values, exactly as in CI3. Callers use `resume`, `position`, `type`.
     * CI3: Ui_model::get_candidate_resume
     */
    public function findWithJob(int $id): ?array
    {
        return $this->db->table('job_applications')
            ->select('job_applications.*, jobs.*')
            ->join('jobs', 'job_applications.job_id = jobs.id', 'inner')
            ->where('job_applications.id', $id)
            ->get()
            ->getRowArray();
    }

    /**
     * Insert an application (DB part of the public apply form); returns the new id.
     * The resume upload is handled by the controller, which then calls setResume().
     * CI3: Ui_model::save_job_application (insert)
     */
    public function createApplication(array $data): int
    {
        $this->insert($data);

        return (int) $this->getInsertID();
    }

    /**
     * Store the uploaded resume file name on an application.
     * CI3: Ui_model::save_job_application (update after move_uploaded_file)
     */
    public function setResume(int $id, string $fileName): bool
    {
        return (bool) $this->builder()
            ->where('id', $id)
            ->update(['resume' => $fileName]);
    }
}
