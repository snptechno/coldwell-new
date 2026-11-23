<?php

namespace App\Models;

use App\Libraries\BlogContent;

/**
 * Table `blog_posts` (migration 2026-11-23-000001_CreateBlogTables).
 * Columns: id, title, slug (unique), excerpt, content, featured_image, image_alt, category_id,
 * author, status (0 draft / 1 published), published_at, meta_title, meta_description,
 * meta_keywords, canonical_url, og_image, noindex, legacy_drupal_nid, legacy_alias, views,
 * created_at, updated_at.
 *
 * A post is live when status = 1 AND published_at <= now (a future date = scheduled).
 */
class BlogPostsModel extends BaseModel
{
    protected $table         = 'blog_posts';
    protected $primaryKey    = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'title', 'slug', 'excerpt', 'content', 'featured_image', 'image_alt', 'category_id', 'author',
        'status', 'published_at', 'meta_title', 'meta_description', 'meta_keywords', 'canonical_url',
        'og_image', 'noindex', 'legacy_drupal_nid', 'legacy_alias',
    ];

    public const STATUS_DRAFT     = 0;
    public const STATUS_PUBLISHED = 1;

    private function now(): string
    {
        return date('Y-m-d H:i:s');
    }

    /** Restrict the model's builder to live posts. */
    public function live(): static
    {
        $this->where($this->table . '.status', self::STATUS_PUBLISHED)
            ->where($this->table . '.published_at <=', $this->now());

        return $this;
    }

    /** Joined category name/slug for listing rows. */
    public function withCategory(): static
    {
        $this->select($this->table . '.*, c.name AS category_name, c.slug AS category_slug')
            ->join('blog_categories c', 'c.id = ' . $this->table . '.category_id', 'left');

        return $this;
    }

    /**
     * Admin list with search, category/status filter and paging.
     *
     * @param array{q?: string, category?: int|string, status?: string} $filters
     */
    public function adminPage(array $filters, int $perPage = 20): array
    {
        $this->withCategory();
        $q = trim((string) ($filters['q'] ?? ''));
        if ($q !== '') {
            $this->groupStart()->like($this->table . '.title', $q)->orLike($this->table . '.slug', $q)->groupEnd();
        }
        if (($filters['category'] ?? '') !== '') {
            $this->where($this->table . '.category_id', (int) $filters['category']);
        }
        $status = (string) ($filters['status'] ?? '');
        if ($status === 'draft') {
            $this->where($this->table . '.status', self::STATUS_DRAFT);
        } elseif ($status === 'published') {
            $this->where($this->table . '.status', self::STATUS_PUBLISHED)->where($this->table . '.published_at <=', $this->now());
        } elseif ($status === 'scheduled') {
            $this->where($this->table . '.status', self::STATUS_PUBLISHED)->where($this->table . '.published_at >', $this->now());
        }

        return $this->orderBy($this->table . '.published_at', 'DESC')->orderBy($this->table . '.id', 'DESC')
            ->paginate($perPage);
    }

    /** Public listing page (optionally one category). */
    public function publicPage(int $perPage, ?int $categoryId = null): array
    {
        $this->withCategory()->live();
        if ($categoryId !== null) {
            $this->where($this->table . '.category_id', $categoryId);
        }

        return $this->orderBy($this->table . '.published_at', 'DESC')->orderBy($this->table . '.id', 'DESC')
            ->paginate($perPage);
    }

    public function postById(int $id): ?array
    {
        return $this->builder()->where('id', $id)->get()->getRowArray();
    }

    public function liveBySlug(string $slug): ?array
    {
        $row = $this->withCategory()->live()->where($this->table . '.slug', $slug)->first();

        return $row ?: null;
    }

    public function slugExists(string $slug, ?int $exceptId = null): bool
    {
        $b = $this->builder()->where('slug', $slug);
        if ($exceptId !== null) {
            $b->where('id !=', $exceptId);
        }

        return $b->countAllResults() > 0;
    }

    /** Slugify $source and append -2, -3 ... until it is free. */
    public function uniqueSlug(string $source, ?int $exceptId = null): string
    {
        $base = BlogContent::slugify($source);
        if ($base === '') {
            $base = 'post';
        }
        $slug = $base;
        for ($i = 2; $this->slugExists($slug, $exceptId); $i++) {
            $slug = $base . '-' . $i;
        }

        return $slug;
    }

    /** Latest live posts in the same category (then any category), excluding $post. */
    public function related(array $post, int $limit = 3): array
    {
        $rows = [];
        if (! empty($post['category_id'])) {
            $rows = $this->withCategory()->live()
                ->where($this->table . '.category_id', (int) $post['category_id'])
                ->where($this->table . '.id !=', (int) $post['id'])
                ->orderBy($this->table . '.published_at', 'DESC')->findAll($limit);
        }
        if (count($rows) < $limit) {
            $ids = array_merge([(int) $post['id']], array_map(static fn ($r) => (int) $r['id'], $rows));
            $more = $this->withCategory()->live()->whereNotIn($this->table . '.id', $ids)
                ->orderBy($this->table . '.published_at', 'DESC')->findAll($limit - count($rows));
            $rows = array_merge($rows, $more);
        }

        return $rows;
    }

    public function recent(int $limit = 5): array
    {
        return $this->select('id, title, slug, published_at, featured_image')->live()
            ->orderBy('published_at', 'DESC')->findAll($limit);
    }

    /** Every live, indexable post for the sitemap. */
    public function forSitemap(): array
    {
        return $this->select('slug, updated_at, published_at')->live()->where('noindex', 0)
            ->orderBy('published_at', 'DESC')->findAll();
    }

    public function incrementViews(int $id): void
    {
        $this->builder()->where('id', $id)->set('views', 'views + 1', false)->update();
    }

    public function byLegacyNid(int $nid): ?array
    {
        return $this->builder()->where('legacy_drupal_nid', $nid)->get()->getRowArray();
    }

    public function byLegacyAlias(string $alias): ?array
    {
        return $this->builder()->where('legacy_alias', $alias)->get()->getRowArray();
    }

    public function updatePost(int $id, array $data): int
    {
        $data = $this->doProtectFields($data);
        if ($data === []) {
            return 0;
        }
        $data['updated_at'] = $this->now();
        $this->builder()->where('id', $id)->update($data);

        return $this->db->affectedRows();
    }

    public function deletePost(int $id): bool
    {
        return (bool) $this->builder()->where('id', $id)->delete();
    }
}
