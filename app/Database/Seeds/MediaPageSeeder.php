<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Seeds the CMS "Media Center" page from the approved Figma frame (Media Center – v2, 79:58).
 *
 *   php spark db:seed MediaPageSeeder
 *
 * Idempotent (skips when slug "media-center" exists). All listings are automatic and read the
 * existing Media admin modules (CB India News, Industry News, Awards, Research, Newsletter and
 * the five video tables); this page only sets headings, counts and buttons. The featured story
 * is pinned to the Favista acquisition (news id 80) as in the design, when that row exists.
 */
class MediaPageSeeder extends Seeder
{
    public const SLUG  = 'media-center';
    private const HOME = 'assets/images/home-v2/';

    private string $now;

    public function run(): void
    {
        $this->now = date('Y-m-d H:i:s');
        if ($this->db->table('cms_pages')->where('slug', self::SLUG)->countAllResults() > 0) {
            return;
        }
        $favista = '';
        try {
            if ($this->db->tableExists('coldwell_banker_news')
                && $this->db->table('coldwell_banker_news')->where('id', 80)->countAllResults() > 0) {
                $favista = '80';
            }
        } catch (\Throwable) {
            $favista = '';
        }

        $this->db->table('cms_pages')->insert([
            'type' => 'page', 'title' => 'Media Center', 'slug' => self::SLUG, 'is_home' => 0, 'nav_key' => 'media center',
            'status' => 1, 'published_at' => $this->now,
            'meta_title' => 'Media Center | Coldwell Banker India News, Awards & Research',
            'meta_description' => 'Coldwell Banker India news coverage, industry news, awards, research reports, videos and newsletters.',
            'created_at' => $this->now, 'updated_at' => $this->now,
        ]);
        $pageId = (int) $this->db->insertID();

        $this->block($pageId, 10, 'page_hero', 'Hero', [
            'eyebrow' => 'Home  ·  Media Center',
            'title'   => 'Newsroom & Insights',
            'lead'    => 'Stay updated with trending real estate news coverage, awards, research and articles from Coldwell Banker India.',
            'image'   => self::HOME . 'news-press.jpg', 'image_alt' => 'Real estate press event on stage',
        ]);
        $this->block($pageId, 20, 'featured_news', 'Featured story', [
            'eyebrow' => 'Featured · CB India News', 'news_id' => $favista, 'cta_label' => 'Read Article',
            'image' => self::HOME . 'handshake.jpg', 'image_alt' => 'Handshake in a corporate lobby',
        ]);
        $this->block($pageId, 30, 'news_feed', 'Latest news', [
            'title' => 'Latest from the Newsroom', 'per_source' => '12', 'page_size' => '6',
        ]);
        $this->block($pageId, 40, 'awards_list', 'Awards', [
            'title' => 'Awards & Recognition',
            'lead'  => 'Recognition for Coldwell Banker India and the professionals across our franchise network.',
            'limit' => '5', 'cta_label' => 'View All Awards', 'cta_url' => 'awards',
        ]);
        $this->block($pageId, 50, 'video_gallery', 'Videos', [
            'title' => 'Videos', 'lead' => 'Stories, guides and films from the Coldwell Banker network.',
            'cta_label' => 'All Videos', 'cta_url' => 'cb-india-videos',
        ]);
        $this->block($pageId, 60, 'archive_lists', 'Research & newsletter', [
            'left_title' => 'Research & Reports', 'left_lead' => 'Area insights and project reports from our research team.',
            'right_title' => 'Newsletter Archive', 'right_lead' => 'Past editions of the Coldwell Banker India newsletter.', 'limit' => '4',
        ]);
        $this->block($pageId, 70, 'newsletter_cta', 'Newsletter', [
            'title' => "Let’s Build a Brighter\nFuture Together",
            'lead'  => 'Get franchise updates, market research and Coldwell Banker India news in your inbox.',
            'image' => self::HOME . 'cta.jpg',
        ]);

        $this->db->table('cms_menu_items')->where('page_id IS NULL')->groupStart()
            ->where('url', 'media-center')->orWhere('url', '/media-center')->groupEnd()
            ->update(['page_id' => $pageId, 'url' => null, 'updated_at' => $this->now]);
    }

    /** @param array<string, string> $settings */
    private function block(int $pageId, int $sort, string $type, string $label, array $settings): void
    {
        $this->db->table('cms_blocks')->insert([
            'page_id' => $pageId, 'type' => $type, 'label' => $label, 'settings' => json_encode($settings, JSON_UNESCAPED_UNICODE),
            'sort_order' => $sort, 'status' => 1, 'created_at' => $this->now, 'updated_at' => $this->now,
        ]);
    }
}
