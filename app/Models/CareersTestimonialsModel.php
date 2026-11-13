<?php

namespace App\Models;

/**
 * Table `careers_testimonials` (InnoDB, latin1_swedish_ci).
 * Columns: id:int, title:text, name:varchar, position:varchar, image_name:varchar, status:tinyint, created_date:timestamp
 *
 * Generated from the live schema (2026-11-13). Domain query methods are added
 * below this line by the step-4 port; keep $allowedFields in sync with the table.
 */
class CareersTestimonialsModel extends BaseModel
{
    protected $table         = 'careers_testimonials';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'title',
        'name',
        'position',
        'image_name',
        'status',
        'created_date',
    ];

    // ------------------------------------------------------------------
    // Step-4 port of home_model.php (careers_testimonials). See docs/port-map/videos.md.
    // File uploads (uploads/carrers_testimonial/) stay in the controller;
    // these methods only take/return plain data.
    // ------------------------------------------------------------------

    /**
     * Admin list, newest first.
     * CI3: home_model::show_careers_testimonials() (home_model.php:1226).
     */
    public function allLatestFirst(): array
    {
        return $this->orderBy('id', 'DESC')->findAll();
    }

    /**
     * One row for the admin edit form, or null.
     * CI3: home_model::show_careers_testimonials_by_id($id).
     */
    public function byId(int $id): ?array
    {
        return $this->builder()->where('id', $id)->get()->getRowArray();
    }

    /**
     * Every testimonial, any status, no ORDER BY (as in CI3).
     * CI3 in-view query: views/franchisees_openings.php:103.
     */
    public function allTestimonials(): array
    {
        return $this->builder()->get()->getResultArray();
    }

    /**
     * Insert a testimonial (name, position, title[, image_name]); returns the new id (0 on failure).
     * DB part of CI3 home_model::add_carrer_testimonial($data, $files). The controller
     * stores the uploaded image and then calls updateTestimonial($id, ['image_name' => ...]).
     */
    public function addTestimonial(array $data): int
    {
        $id = $this->insert($data, true);

        return $id === false ? 0 : (int) $id;
    }

    /**
     * Update a testimonial; returns the affected row count like CI3.
     * DB part of CI3 home_model::update_carrer_testimonial($id, $data, $files): when a new
     * image was uploaded the controller adds 'image_name' to $data before calling this.
     */
    public function updateTestimonial(int $id, array $data): int
    {
        $this->update($id, $data);

        return $this->db->affectedRows();
    }

    /**
     * Enable (1) / disable (0) a testimonial (column `status`); returns the affected row count.
     * CI3: home_model::change_status_carrers_testimonial($id, $status).
     */
    public function changeStatus(int $id, int $status): int
    {
        $this->setStatus($id, $status, 'status');

        return $this->db->affectedRows();
    }

    /**
     * Delete a testimonial and return the deleted row (or null), so the controller can
     * remove its image file from uploads/carrers_testimonial/.
     * DB part of CI3 home_model::delete_carrer_testimonial($id).
     */
    public function deleteTestimonial(int $id): ?array
    {
        $row = $this->byId($id);

        $this->builder()->where('id', $id)->delete();

        return $row;
    }
}
