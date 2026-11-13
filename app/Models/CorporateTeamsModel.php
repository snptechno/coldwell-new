<?php

namespace App\Models;

/**
 * Table `corporate_teams` (InnoDB, latin1_swedish_ci).
 * Columns: id:int, name:varchar, title:text, description:text, image:varchar, status:tinyint, order_no:int, created_date:timestamp
 *
 * Generated from the live schema (2026-11-13). Domain query methods are added
 * below this line by the step-4 port; keep $allowedFields in sync with the table.
 */
class CorporateTeamsModel extends BaseModel
{
    protected $table         = 'corporate_teams';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'name',
        'title',
        'description',
        'image',
        'status',
        'order_no',
        'created_date',
    ];

    /**
     * All members, active and inactive, by display order
     * (CI3: home_model::show_corporate_teams).
     *
     * @return list<array<string, mixed>>
     */
    public function listForAdmin(): array
    {
        return $this->builder()
            ->orderBy('order_no', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Active members by display order (CI3: inline query in views/corporate_team.php).
     *
     * @return list<array<string, mixed>>
     */
    public function activeOrdered(): array
    {
        return $this->builder()
            ->where('status', 1)
            ->orderBy('order_no', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * One member or null (CI3: home_model::show_corporate_team_by_id and
     * Ui_model::get_team_information).
     */
    public function memberById(int $id): ?array
    {
        return $this->builder()
            ->where('id', $id)
            ->get()
            ->getRowArray();
    }

    /**
     * Insert a member; returns affected rows (CI3: home_model::add_corporate_team).
     * $data['image'] is the stored file name; the upload itself is done by the caller.
     * `order_no` and `image` are NOT NULL without a default, so when they are not
     * supplied they get the implicit values a non-strict server stored (0 and '').
     */
    public function addMember(array $data): int
    {
        $data = $this->doProtectFields($data) + ['order_no' => 0, 'image' => ''];

        $this->builder()->insert($data);

        return $this->db->affectedRows();
    }

    /**
     * Update a member; returns affected rows (CI3: home_model::update_corporate_team).
     * Pass 'image' only when a new file was stored.
     */
    public function updateMember(int $id, array $data): int
    {
        $data = $this->doProtectFields($data);
        if ($data === []) {
            return 0;
        }

        $this->builder()
            ->where('id', $id)
            ->update($data);

        return $this->db->affectedRows();
    }

    /**
     * Delete a member and return the deleted row (null when it does not exist)
     * so the caller can remove uploads/corporate_team/<image>
     * (CI3: home_model::delete_corporate_team).
     */
    public function deleteMember(int $id): ?array
    {
        $row = $this->memberById($id);
        if ($row === null) {
            return null;
        }

        $this->builder()
            ->where('id', $id)
            ->delete();

        return $row;
    }

    /**
     * Set a member's display order; returns affected rows
     * (CI3: home_model::update_order_no_corporate_team).
     */
    public function updateOrderNo(int $id, int $orderNo): int
    {
        $this->builder()
            ->where('id', $id)
            ->update(['order_no' => $orderNo]);

        return $this->db->affectedRows();
    }
}
