<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Seeds the CMS "Research" page from the approved Figma frame (Research – v2, 135:576).
 *
 *   php spark db:seed ResearchPageSeeder
 *
 * Idempotent (skips when slug "research" exists). The tabs, featured report and grid are
 * automatic and read the existing Research admin module (Admin > Section > Research).
 * The hero image and CTA background use placeholder paths; replace with real assets.
 */
class ResearchPageSeeder extends Seeder
{
    public const SLUG  = 'research';
    private const HOME = 'assets/images/home-v2/';
    private const RSCH = 'assets/images/home-v2/';

    private string $now;

    public function run(): void
    {
        $this->now = date('Y-m-d H:i:s');
        if ($this->db->table('cms_pages')->where('slug', self::SLUG)->countAllResults() > 0) {
            return;
        }

        $this->db->table('cms_pages')->insert([
            'type'            => 'page',
            'title'           => 'Research',
            'slug'            => self::SLUG,
            'is_home'         => 0,
            'nav_key'         => 'research',
            'status'          => 1,
            'published_at'    => $this->now,
            'meta_title'      => 'Research & Reports | Coldwell Banker India',
            'meta_description'=> 'Explore Coldwell Banker India project reports and area insights. In-depth research to help you make informed real estate decisions.',
            'meta_keywords'   => 'real estate research, project reports, area insights, Coldwell Banker India',
            'og_image'        => self::HOME . 'og-research.jpg',
            'created_at'      => $this->now,
            'updated_at'      => $this->now,
        ]);
        $pageId = (int) $this->db->insertID();

        // 1. Hero (page_hero)
        $this->block($pageId, 10, 'page_hero', 'Hero', [
            'eyebrow'   => 'Home  ·  Insights  ·  Project Reports',
            'title'     => 'Market Research & Project Reports',
            'lead'      => 'Know what\'s new in real estate. Brief project reports and in-depth area insights to help you make informed decisions.',
            'image'     => self::RSCH . 'news-research.jpg',
            'image_alt' => 'Real estate research desk with market charts and documents',
        ]);

        // 2. Research Tabs
        $this->block($pageId, 20, 'research_tabs', 'Research Tabs', [
            'active' => 'project',
        ]);

        // 3. Featured Report
        $this->block($pageId, 30, 'featured_report', 'Featured Report', [
            'title'     => 'Latest Project Report',
            'limit'     => '1',
            'cta_label' => 'Download PDF',
        ]);

        // 4. Research Grid
        $this->block($pageId, 40, 'research_grid', 'Reports Grid', [
            'title'       => 'All Project Reports',
            'per_page'    => '6',
            'cta_label'   => 'Download',
            'load_label'  => 'Load More',
        ]);

        // 5. Our Approach
        $this->block($pageId, 50, 'approach_panel', 'Our Approach', [
            'eyebrow'   => 'OUR APPROACH',
            'statement' => 'Whether it is a residential property or a commercial space, we partner with you in making an informed decision.',
            'cta_label' => 'Talk to an Advisor',
            'cta_url'   => 'contact-us',
        ]);

        // 6. Newsletter CTA
        $this->block($pageId, 60, 'newsletter_cta', 'Newsletter CTA', [
            'title' => "Let's Build a Brighter\nFuture Together",
            'lead'  => 'Get franchise updates, market research and Coldwell Banker India news in your inbox.',
            'image' => self::HOME . 'cta.jpg',
        ]);

        // Link menu item to page
        $this->db->table('cms_menu_items')->where('page_id IS NULL')->groupStart()
            ->where('url', 'research')->orWhere('url', '/research')->groupEnd()
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