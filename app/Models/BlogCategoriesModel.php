<?php

namespace App\Models;

/**
 * Table `blog_categories` (migration 2026-11-23-000001_CreateBlogTables).
 * Columns: id, name, slug (unique), description, meta_title, meta_description,
 * status (1 active / 0 hidden), sort_order, legacy_drupal_tid, legacy_alias, created_at, updated_at.
 */
class BlogCategoriesModel extends BaseModel
{
    protected $table         = 'blog_categories';
    protected $primaryKey    = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'name', 'slug', 'description', 'meta_title', 'meta_description', 'status', 'sort_order',
        'legacy_drupal_tid', 'legacy_alias',
    ];

    /** @return list<array<string, mixed>> with a post_count column */
    public function listForAdmin(): array
    {
        $posts = $this->db->prefixTable('blog_posts');

        return $this->builder()
            ->select($this->table . '.*, (SELECT COUNT(*) FROM ' . $posts . ' p WHERE p.category_id = ' . $this->db->prefixTable($this->table) . '.id) AS post_count', false)
            ->orderBy('sort_order', 'ASC')->orderBy('name', 'ASC')
            ->get()->getResultArray();
    }

    /** Active categories with at least one live post, for the public sidebar. */
    public function listPublicWithCounts(): array
    {
        $now = date('Y-m-d H:i:s');

        return $this->db->table($this->table . ' c')
            ->select('c.id, c.name, c.slug, COUNT(p.id) AS post_count')
            ->join($this->db->prefixTable('blog_posts') . ' p', 'p.category_id = c.id AND p.status = 1 AND p.published_at <= ' . $this->db->escape($now), 'inner', false)
            ->where('c.status', 1)
            ->groupBy('c.id, c.name, c.slug, c.sort_order')
            ->orderBy('c.sort_order', 'ASC')->orderBy('c.name', 'ASC')
            ->get()->getResultArray();
    }

    /** @return array<int, string> id => name */
    public function options(): array
    {
        $out = [];
        foreach ($this->builder()->select('id, name')->orderBy('name', 'ASC')->get()->getResultArray() as $r) {
            $out[(int) $r['id']] = $r['name'];
        }

        return $out;
    }

    public function categoryById(int $id): ?array
    {
        return $this->builder()->where('id', $id)->get()->getRowArray();
    }

    public function activeBySlug(string $slug): ?array
    {
        return $this->builder()->where('slug', $slug)->where('status', 1)->get()->getRowArray();
    }

    public function slugExists(string $slug, ?int $exceptId = null): bool
    {
        $b = $this->builder()->where('slug', $slug);
        if ($exceptId !== null) {
            $b->where('id !=', $exceptId);
        }

        return $b->countAllResults() > 0;
    }

    public function byLegacyTid(int $tid): ?array
    {
        return $this->builder()->where('legacy_drupal_tid', $tid)->get()->getRowArray();
    }

    public function byLegacyAlias(string $alias): ?array
    {
        return $this->builder()->where('legacy_alias', $alias)->get()->getRowArray();
    }

    public function deleteCategory(int $id): bool
    {
        // Posts keep existing without a category.
        $this->db->table('blog_posts')->where('category_id', $id)->update(['category_id' => null]);

        return (bool) $this->builder()->where('id', $id)->delete();
    }
}
