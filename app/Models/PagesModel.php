<?php

namespace App\Models;

/**
 * Table `pages` (MyISAM, latin1_swedish_ci).
 * Columns: id:int, name:varchar, content:text, title:varchar, meta_description:text, meta_keywords:text, status:enum, created_on:timestamp
 *
 * Generated from the live schema (2026-11-13). Domain query methods are added
 * below this line by the step-4 port; keep $allowedFields in sync with the table.
 */
class PagesModel extends BaseModel
{
    /*
     * Fixed `pages` rows read by the public site (CI3: one Ui_model method per
     * page, each with a hard-coded id). Several legacy methods share an id; the
     * mapping is kept exactly as it was (see docs/port-map/cms.md).
     */
    public const PAGE_HOME                   = 1;  // page_info
    public const PAGE_VIDEOS                 = 2;  // selling_a_home, testimonials
    public const PAGE_OUR_FRANCHISEE         = 3;  // our_franchisee
    public const PAGE_OUR_FRANCHISEE_DETAILS = 4;  // our_franchisee_details
    public const PAGE_LEARNING_DEVELOPMENT   = 5;  // learning_development
    public const PAGE_MEDIA_CENTER           = 6;  // media_center, industry_news, knowledge_series, newsletter
    public const PAGE_CORPORATE_TEAM         = 7;  // corporate_team
    public const PAGE_RESEARCH               = 8;  // area_insights
    public const PAGE_CAREER                 = 9;  // career
    public const PAGE_ABOUT_US               = 10; // coldwell_banker_usa
    public const PAGE_NRI                    = 11; // nri
    public const PAGE_CONTACT_US             = 12; // contact_us
    public const PAGE_PRIVACY_POLICY         = 19; // privacy_policy
    public const PAGE_TERMS_AND_CONDITIONS   = 20; // terms_and_conditions
    public const PAGE_DISCLAIMER             = 21; // disclaimer
    public const PAGE_FAQ                    = 22; // faq
    public const PAGE_REFUND                 = 23; // refund
    public const PAGE_FINDING_A_HOME         = 24; // finding_a_home, quick_pay

    protected $table         = 'pages';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'name',
        'content',
        'title',
        'meta_description',
        'meta_keywords',
        'status',
        'created_on',
    ];

    /**
     * Admin page list, highest id first (CI3: home_model::show_pages_list).
     *
     * @return list<array<string, mixed>>
     */
    public function listForAdmin(): array
    {
        return $this->builder()
            ->orderBy('id', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * One page row or null (CI3: home_model::getSingle_page and every
     * Ui_model page method, e.g. Ui_model::nri() is pageById(self::PAGE_NRI)).
     */
    public function pageById(int $id): ?array
    {
        return $this->builder()
            ->where('id', $id)
            ->get()
            ->getRowArray();
    }

    /**
     * Update a page; returns affected rows (CI3: home_model::update_page).
     */
    public function updatePage(int $id, array $data): int
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
     * CI3: home_model::change_status_page.
     *
     * `pages`.`status` is ENUM('0','1'), so the value is written as the string
     * '0'/'1': MySQL reads an unquoted integer assigned to an ENUM as the
     * member index (1 would store '0').
     */
    public function setStatus(int $id, int $status, string $column = 'status'): bool
    {
        return $this->builder()
            ->where($this->primaryKey, $id)
            ->update([$column => $status === 1 ? '1' : '0']);
    }

    /**
     * Delete a page (CI3: home_model::delete_page).
     */
    public function deletePage(int $id): bool
    {
        return (bool) $this->builder()
            ->where('id', $id)
            ->delete();
    }
}
