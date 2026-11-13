<?php

namespace App\Models;

/**
 * Table `franchises_testimonials` (InnoDB, utf8mb3_general_ci).
 * Columns: id:int, franchise_id:int, name:varchar, designation:varchar, description:text, is_active:tinyint, created_date:datetime
 *
 * Generated from the live schema (2026-11-13). Domain query methods are added
 * below this line by the step-4 port; keep $allowedFields in sync with the table.
 */
class FranchisesTestimonialsModel extends BaseModel
{
    protected $table         = 'franchises_testimonials';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'franchise_id',
        'name',
        'designation',
        'description',
        'is_active',
        'created_date',
    ];

    /**
     * All testimonials of one franchise, newest first (admin list).
     * CI3: home_model::show_franchiese_testimonials
     */
    public function byFranchiseNewestFirst(int $franchiseId): array
    {
        return $this->builder()
            ->where('franchise_id', $franchiseId)
            ->orderBy('id', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Active testimonials of one franchise (public franchise details page).
     * No ORDER BY, as before.
     * CI3: views/our_franchisee_details.php:280-281 (inline query)
     */
    public function activeByFranchise(int $franchiseId): array
    {
        return $this->builder()
            ->where('franchise_id', $franchiseId)
            ->where('is_active', 1)
            ->get()
            ->getResultArray();
    }

    /**
     * Insert a testimonial; returns the affected-row count (CI3 contract).
     * CI3: home_model::add_franchise_testimonials
     */
    public function addTestimonial(array $data): int
    {
        $this->insert($data, false);

        return $this->db->affectedRows();
    }

    /**
     * Update a testimonial; returns the affected-row count (CI3 contract).
     * CI3: home_model::update_franchise_testimonials
     */
    public function updateTestimonial(int $id, array $data): int
    {
        $this->update($id, $data);

        return $this->db->affectedRows();
    }

    /**
     * Delete a testimonial.
     * CI3: home_model::delete_franchise_testimonials (returned an undefined variable)
     */
    public function deleteTestimonial(int $id): bool
    {
        return (bool) $this->builder()->where('id', $id)->delete();
    }
}
