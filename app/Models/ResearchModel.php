<?php

namespace App\Models;

/**
 * Table `research` (InnoDB, latin1_swedish_ci).
 * Columns: id:int, type:varchar, title:varchar, description:varchar, image_name:varchar, pdf_name:varchar, is_active:tinyint, created_date:timestamp
 *
 * Generated from the live schema (2026-11-13). Domain query methods are added
 * below this line by the step-4 port; keep $allowedFields in sync with the table.
 */
class ResearchModel extends BaseModel
{
    protected $table         = 'research';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'type',
        'title',
        'description',
        'image_name',
        'pdf_name',
        'is_active',
        'created_date',
    ];

    /** Row id of the CMS page (table `pages`) that heads the public research page. */
    public const PAGE_ID = 8;

    /**
     * Columns that have no default in the schema and are optional in the admin
     * form, with the implicit values non-strict MySQL stored when CI3 omitted them.
     */
    private const INSERT_DEFAULTS = [
        'image_name' => '',
        'pdf_name'   => '',
        'is_active'  => 0,
    ];

    /**
     * All research rows, newest first (admin list).
     * CI3: home_model::show_research.
     */
    public function listNewestFirst(): array
    {
        return $this->builder()
            ->orderBy('id', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Active rows of one type, newest first (public pages).
     * $type is 'Project Reports' (views/research.php:53) or
     * 'Area Insights' (views/area_insights.php:53).
     */
    public function activeByType(string $type): array
    {
        return $this->builder()
            ->where('type', $type)
            ->where('is_active', 1)
            ->orderBy('id', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * One row by id, or null.
     * CI3: home_model::show_research_by_id.
     */
    public function findById(int $id): ?array
    {
        return $this->builder()
            ->where('id', $id)
            ->get()
            ->getRowArray();
    }

    /**
     * Header content of the public research page (CMS page id 8).
     * CI3: Ui_model::research (returned row(); now an array).
     */
    public function pageInfo(): ?array
    {
        return $this->db->table('pages')
            ->where('id', self::PAGE_ID)
            ->get()
            ->getRowArray();
    }

    /**
     * Insert a research row. $data holds type/title/description and, when the
     * files were stored, `image_name` and/or `pdf_name` (uploads are done by
     * the controller). Returns the affected row count, as CI3 did.
     * CI3: home_model::add_research (DB part).
     */
    public function createItem(array $data): int
    {
        $this->insert($data + self::INSERT_DEFAULTS, false);

        return $this->db->affectedRows();
    }

    /**
     * Update a research row; a newly stored image and/or PDF file name is
     * written in its own update afterwards, as in CI3.
     * Returns the affected row count of the last update.
     * CI3: home_model::update_research (DB part).
     */
    public function updateItem(int $id, array $data, ?string $imageName = null, ?string $pdfName = null): int
    {
        $this->update($id, $data);

        if ($imageName !== null) {
            $this->update($id, ['image_name' => $imageName]);
        }

        if ($pdfName !== null) {
            $this->update($id, ['pdf_name' => $pdfName]);
        }

        return $this->db->affectedRows();
    }

    /**
     * Delete a research row and return it (null when it did not exist), so the
     * caller can remove the files named in `image_name` and `pdf_name`.
     * CI3: home_model::delete_research (DB part).
     */
    public function deleteItem(int $id): ?array
    {
        $row = $this->findById($id);

        $this->builder()
            ->where('id', $id)
            ->delete();

        return $row;
    }
}
