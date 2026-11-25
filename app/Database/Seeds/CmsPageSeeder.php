<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Shared helpers for seeders that create one CMS page from a Figma design: create the page row
 * (idempotent — returns null when the slug already exists), add blocks with their items, and
 * point menu items that still use the page's old URL at the new page.
 */
abstract class CmsPageSeeder extends Seeder
{
    protected const IMG_HOME     = 'assets/images/home-v2/';
    protected const IMG_ABOUT    = 'assets/images/about-v2/';
    protected const IMG_LEARNING = 'assets/images/learning-v2/';

    protected string $now = '';

    /**
     * @param array<string, mixed> $fields cms_pages columns (meta_title, meta_description, nav_key …)
     */
    protected function createPage(string $slug, string $title, array $fields = []): ?int
    {
        $this->now = $this->now !== '' ? $this->now : date('Y-m-d H:i:s');
        if ($this->db->table('cms_pages')->where('slug', $slug)->countAllResults() > 0) {
            return null;
        }
        $this->db->table('cms_pages')->insert(array_merge([
            'type' => 'page', 'title' => $title, 'slug' => $slug, 'is_home' => 0, 'nav_key' => null,
            'status' => 1, 'published_at' => $this->now, 'created_at' => $this->now, 'updated_at' => $this->now,
        ], $fields));

        return (int) $this->db->insertID();
    }

    /**
     * @param array<string, mixed>                     $settings
     * @param array<string, list<array<string, mixed>>> $groups
     */
    protected function block(int $pageId, int $sort, string $type, string $label, array $settings, array $groups = [], int $status = 1): void
    {
        $this->db->table('cms_blocks')->insert([
            'page_id' => $pageId, 'type' => $type, 'label' => $label, 'settings' => json_encode($settings, JSON_UNESCAPED_UNICODE),
            'sort_order' => $sort, 'status' => $status, 'created_at' => $this->now, 'updated_at' => $this->now,
        ]);
        $blockId = (int) $this->db->insertID();
        $rows    = [];
        foreach ($groups as $group => $items) {
            foreach ($items as $i => $it) {
                $rows[] = array_merge([
                    'block_id' => $blockId, 'item_group' => $group, 'kicker' => null, 'badge' => null, 'title' => null,
                    'subtitle' => null, 'body' => null, 'cta_label' => null, 'cta_url' => null, 'image' => null, 'image_alt' => null,
                    'sort_order' => ($i + 1) * 10, 'status' => 1, 'created_at' => $this->now, 'updated_at' => $this->now,
                ], $it);
            }
        }
        if ($rows !== []) {
            $this->db->table('cms_block_items')->insertBatch($rows);
        }
    }

    /** Menu items still pointing at one of $urls (no page) now link to the CMS page. */
    protected function linkMenu(int $pageId, string ...$urls): void
    {
        $q = $this->db->table('cms_menu_items')->where('page_id IS NULL')->groupStart();
        foreach ($urls as $i => $u) {
            $i === 0 ? $q->where('url', $u)->orWhere('url', '/' . $u) : $q->orWhere('url', $u)->orWhere('url', '/' . $u);
        }
        $q->groupEnd()->update(['page_id' => $pageId, 'url' => null, 'updated_at' => $this->now]);
    }

    /** Standard newsletter band that ends every page. */
    protected function newsletter(int $pageId, int $sort): void
    {
        $this->block($pageId, $sort, 'newsletter_cta', 'Newsletter', [
            'title' => "Let’s Build a Brighter\nFuture Together",
            'lead'  => 'Get franchise updates, market research and Coldwell Banker India news in your inbox.',
            'image' => self::IMG_HOME . 'cta.jpg',
        ]);
    }
}
