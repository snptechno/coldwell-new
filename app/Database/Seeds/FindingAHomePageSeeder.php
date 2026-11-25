<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Seeds the CMS "Finding a Home (Videos)" page from Figma "Videos – v2" (140:197 / 140:198).
 *
 *   php spark db:seed FindingAHomePageSeeder
 *
 * Idempotent (skips when slug "finding-a-home" exists). Video content is pulled live
 * from the 5 Video admin modules (Admin > Section > Videos).
 */
class FindingAHomePageSeeder extends Seeder
{
    public const SLUG  = 'finding-a-home';
    private const HOME = 'assets/images/home-v2/';

    private string $now;

    public function run(): void
    {
        $this->now = date('Y-m-d H:i:s');
        if ($this->db->table('cms_pages')->where('slug', self::SLUG)->countAllResults() > 0) {
            return;
        }

        $this->db->table('cms_pages')->insert([
            'type'             => 'page',
            'title'            => 'Finding a Home',
            'slug'             => self::SLUG,
            'is_home'          => 0,
            'nav_key'          => 'media center',
            'status'           => 1,
            'published_at'     => $this->now,
            'meta_title'       => 'Finding a Home Videos | Coldwell Banker India',
            'meta_description' => 'Films, guides and success stories from Coldwell Banker India and the global Coldwell Banker network.',
            'meta_keywords'    => 'finding a home, real estate videos, Coldwell Banker India',
            'og_image'         => self::HOME . 'news-video.jpg',
            'created_at'       => $this->now,
            'updated_at'       => $this->now,
        ]);
        $pageId = (int) $this->db->insertID();

        // 1. Hero (page_hero)
        $this->block($pageId, 10, 'page_hero', 'Hero', [
            'eyebrow'   => 'Home  ·  Media  ·  Videos',
            'title'     => 'Stories in Motion',
            'lead'      => 'Films, guides and success stories from Coldwell Banker India and the global Coldwell Banker network.',
            'image'     => self::HOME . 'news-video.jpg',
            'image_alt' => 'Videographer filming a modern residential interior',
        ]);

        // 2. Media Tabs
        $this->block($pageId, 20, 'media_tabs', 'Media Tabs', [
            'active' => 'videos',
        ]);

        // 3. Video Hub (Featured + 3 side + More From CB India grid)
        $this->block($pageId, 30, 'video_hub', 'Videos', [
            'title'       => 'Videos',
            'lead'        => 'Stories, guides and films from the Coldwell Banker network.',
            'default_tab' => 'Finding a Home',
            'more_title'  => 'More From CB India',
            'per_page'    => '6',
            'load_label'  => 'Load More',
        ]);

        // 4. Newsletter CTA
        $this->block($pageId, 40, 'newsletter_cta', 'Newsletter CTA', [
            'title' => "Let's Build a Brighter\nFuture Together",
            'lead'  => 'Get franchise updates, market research and Coldwell Banker India news in your inbox.',
            'image' => self::HOME . 'cta.jpg',
        ]);

        // Link menu item to page
        $this->db->table('cms_menu_items')->where('page_id IS NULL')->groupStart()
            ->where('url', 'finding-a-home')->orWhere('url', '/finding-a-home')->groupEnd()
            ->update(['page_id' => $pageId, 'url' => null, 'updated_at' => $this->now]);
    }

    /** @param array<string, string> $settings */
    private function block(int $pageId, int $sort, string $type, string $label, array $settings): void
    {
        $this->db->table('cms_blocks')->insert([
            'page_id'    => $pageId,
            'type'       => $type,
            'label'      => $label,
            'settings'   => json_encode($settings, JSON_UNESCAPED_UNICODE),
            'sort_order' => $sort,
            'status'     => 1,
            'created_at' => $this->now,
            'updated_at' => $this->now,
        ]);
    }
}
