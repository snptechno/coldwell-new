<?php

namespace App\Models;

/**
 * Table `events` (InnoDB, latin1_swedish_ci).
 * Columns: id:int, title:text, start_date:varchar, end_date:varchar, duration:varchar, seats:varchar, status:int, created_date:timestamp
 *
 * Generated from the live schema (2026-11-13). Domain query methods are added
 * below this line by the step-4 port; keep $allowedFields in sync with the table.
 */
class EventsModel extends BaseModel
{
    protected $table         = 'events';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'title',
        'start_date',
        'end_date',
        'duration',
        'seats',
        'status',
        'created_date',
    ];

    /**
     * All events, newest first (CI3 home_model::show_events_list).
     *
     * @return list<array<string, mixed>>
     */
    public function eventList(): array
    {
        return $this->orderBy('id', 'DESC')->findAll();
    }

    /**
     * One event (CI3 home_model::show_events_by_id).
     */
    public function findEvent(int $id): ?array
    {
        return $this->builder()
            ->where('id', $id)
            ->get()
            ->getRowArray();
    }

    /**
     * Insert an event (CI3 home_model::add_events).
     * $data: title, start_date, end_date, duration. The form's seats field is not
     * read by the controller; seats is written as an empty string explicitly (the
     * implicit default a non-strict server used) so the insert is accepted under
     * STRICT_TRANS_TABLES.
     *
     * @param array<string, mixed> $data
     */
    public function addEvent(array $data): int
    {
        $data += ['seats' => ''];
        $this->insert($data);

        return $this->db->affectedRows();
    }

    /**
     * Update an event (CI3 home_model::update_events).
     *
     * @param array<string, mixed> $data
     */
    public function updateEvent(int $id, array $data): int
    {
        $this->update($id, $data);

        return $this->db->affectedRows();
    }

    /**
     * Delete an event (CI3 home_model::delete_event).
     */
    public function deleteEvent(int $id): bool
    {
        return (bool) $this->builder()->where('id', $id)->delete();
    }

    /**
     * Active events by start date, for the training calendar
     * (learning_development.php view query).
     *
     * @return list<array<string, mixed>>
     */
    public function activeByStartDate(): array
    {
        return $this->where('status', '1')
            ->orderBy('start_date', 'ASC')
            ->findAll();
    }
}
