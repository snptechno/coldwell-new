<?php

namespace App\Models;

use CodeIgniter\Database\BaseBuilder;

/**
 * Table `franchisee_project_enquiry` (MyISAM, utf8mb3_general_ci).
 * Columns: id:int, franchisee_id:int, name:varchar, email_id:varchar, phone:varchar, interested_in:varchar, created_date:datetime
 *
 * Generated from the live schema (2026-11-13). Domain query methods are added
 * below this line by the step-4 port; keep $allowedFields in sync with the table.
 */
class FranchiseeProjectEnquiryModel extends BaseModel
{
    protected $table         = 'franchisee_project_enquiry';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'franchisee_id',
        'name',
        'email_id',
        'phone',
        'interested_in',
        'created_date',
    ];

    /**
     * Insert one franchisee project enquiry and return its id (false on failure).
     * CI3: Ui_model::franchisee_details_enquiry
     *
     * @param array<string, mixed> $data
     */
    public function addEnquiry(array $data): int|false
    {
        $id = $this->insert($data, true);

        return $id === false ? false : (int) $id;
    }

    /**
     * All enquiries joined with their franchise title (as franchise_name), newest id first.
     * Enquiries whose franchise no longer exists are excluded (inner join, as in CI3).
     * CI3: home_model::franchisee_project_enquiries
     *
     * @return list<array<string, mixed>>
     */
    public function allWithFranchiseName(): array
    {
        return $this->withFranchiseName()->get()->getResultArray();
    }

    /**
     * Same as allWithFranchiseName(), limited to enquiries whose created_date falls
     * between $startDate 00:00:00 and $endDate 23:59:59 (inclusive). Dates are Y-m-d strings.
     * CI3: home_model::info_franchisee_project_enquiries_by_dates
     *
     * @return list<array<string, mixed>>
     */
    public function byCreatedDateRangeWithFranchiseName(string $startDate, string $endDate): array
    {
        return $this->withFranchiseName()
            ->where('franchisee_project_enquiry.created_date >=', $startDate . ' 00:00:00')
            ->where('franchisee_project_enquiry.created_date <=', $endDate . ' 23:59:59')
            ->get()
            ->getResultArray();
    }

    /**
     * Delete one row by id.
     * CI3: home_model::delete_franchisee_project_enquiry
     */
    public function deleteById(int $id): bool
    {
        return (bool) $this->builder()->where('id', $id)->delete();
    }

    private function withFranchiseName(): BaseBuilder
    {
        return $this->db->table('franchisee_project_enquiry')
            ->select('franchisee_project_enquiry.*, franchises.title AS franchise_name')
            ->join('franchises', 'franchisee_project_enquiry.franchisee_id = franchises.id', 'inner')
            ->orderBy('franchisee_project_enquiry.id', 'desc');
    }
}
