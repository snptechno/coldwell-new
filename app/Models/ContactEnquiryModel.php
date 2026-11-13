<?php

namespace App\Models;

/**
 * Table `contact_enquiry` (MyISAM, latin1_swedish_ci).
 * Columns: id:int, name:varchar, email:varchar, phone:varchar, message:text, interested_in:varchar, created_on:timestamp
 *
 * Generated from the live schema (2026-11-13). Domain query methods are added
 * below this line by the step-4 port; keep $allowedFields in sync with the table.
 */
class ContactEnquiryModel extends BaseModel
{
    protected $table         = 'contact_enquiry';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'name',
        'email',
        'phone',
        'message',
        'interested_in',
        'created_on',
    ];

    /**
     * Insert one contact enquiry and return its id (false on failure).
     * CI3: home_model::add_contact_enquiry, Ui_model::contact_enquiry
     *
     * @param array<string, mixed> $data
     */
    public function addEnquiry(array $data): int|false
    {
        $id = $this->insert($data, true);

        return $id === false ? false : (int) $id;
    }

    /**
     * All rows, newest id first.
     * CI3: home_model::project_contact_enquiry, home_model::show_contact_enquiry
     *
     * @return list<array<string, mixed>>
     */
    public function allNewestFirst(): array
    {
        return $this->orderBy('id', 'desc')->findAll();
    }

    /**
     * Rows whose created_on falls between $startDate 00:00:00 and $endDate 23:59:59
     * (inclusive, same as CI3 BETWEEN), newest created_on first. Dates are Y-m-d strings.
     * CI3: home_model::info_contact_enquiry_by_dates
     *
     * @return list<array<string, mixed>>
     */
    public function byCreatedDateRange(string $startDate, string $endDate): array
    {
        return $this->where('created_on >=', $startDate . ' 00:00:00')
            ->where('created_on <=', $endDate . ' 23:59:59')
            ->orderBy('created_on', 'desc')
            ->findAll();
    }

    /**
     * Delete one row by id.
     * CI3: home_model::delete_contact
     */
    public function deleteById(int $id): bool
    {
        return (bool) $this->builder()->where('id', $id)->delete();
    }
}
