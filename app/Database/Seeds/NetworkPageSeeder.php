<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Seeds the CMS "Our Network" page (slug our-franchise, the existing URL).
 *
 *   php spark db:seed NetworkPageSeeder
 *
 * There is no Figma frame for this page: it is composed from the approved design system and
 * existing blocks (page hero, live figures, office directory, franchise call-back, newsletter).
 * Office figures use {offices} / {cities} tokens so they always match the franchises table.
 * Idempotent (skips when the slug exists).
 */
class NetworkPageSeeder extends Seeder
{
    public const SLUG  = 'our-franchise';
    private const HOME = 'assets/images/home-v2/';

    private string $now;

    public function run(): void
    {
        $this->now = date('Y-m-d H:i:s');
        if ($this->db->table('cms_pages')->where('slug', self::SLUG)->countAllResults() > 0) {
            return;
        }

        $this->db->table('cms_pages')->insert([
            'type' => 'page', 'title' => 'Our Network', 'slug' => self::SLUG, 'is_home' => 0, 'nav_key' => 'our franchisee',
            'status' => 1, 'published_at' => $this->now,
            'meta_title' => 'Our Network | Coldwell Banker Franchise Offices in India',
            'meta_description' => 'Find a Coldwell Banker franchise office near you: offices across India’s key metros with addresses and phone numbers.',
            'created_at' => $this->now, 'updated_at' => $this->now,
        ]);
        $pageId = (int) $this->db->insertID();

        $this->block($pageId, 10, 'page_hero', 'Hero', [
            'eyebrow' => 'Home  ·  Our Network',
            'title'   => 'Our Network',
            'lead'    => 'Coldwell Banker franchise offices across India’s key metros and fastest-growing corridors — find the office nearest you.',
            'cta_label' => 'Find an Office', 'cta_url' => '#offices',
            'image'   => self::HOME . 'towers.jpg', 'image_alt' => 'Glass office towers at blue hour',
        ]);
        $this->block($pageId, 20, 'stats_panel', 'Network at a glance', [
            'layout'  => 'center', 'eyebrow' => 'Network at a glance',
            'body'    => 'One global brand, local experts in every office. Each Coldwell Banker franchise brings the systems, training and reach of a brand trusted since 1906 to its own market.',
        ], ['stat' => [
            ['title' => '{offices}', 'subtitle' => 'Franchise offices'],
            ['title' => '{cities}', 'subtitle' => 'Cities across India'],
            ['title' => '1906', 'subtitle' => 'Brand founded'],
            ['title' => '40+', 'subtitle' => 'Countries & territories'],
        ]]);
        $this->block($pageId, 30, 'office_finder', 'Office directory', [
            'layout' => 'directory', 'title' => 'Find a Franchise Office',
            'lead'   => '{offices} offices across {cities} cities. Choose a city to see its offices.',
            'cta_label' => 'Contact Us', 'cta_url' => 'contact-us',
        ]);
        $this->block($pageId, 40, 'enquiry_form', 'Open an office', [
            'eyebrow' => 'Grow the network', 'title' => 'Open an Office in Your City',
            'lead'    => 'Don’t see Coldwell Banker in your city yet? Share your details and our franchise team will call you about the opportunity.',
            'phone'   => '', 'button' => 'Request a Call Back',
        ]);
        $this->block($pageId, 50, 'newsletter_cta', 'Newsletter', [
            'title' => "Let’s Build a Brighter\nFuture Together",
            'lead'  => 'Get franchise updates, market research and Coldwell Banker India news in your inbox.',
            'image' => self::HOME . 'cta.jpg',
        ]);

        // "Our Network" menu links (main + footer cities) point at the CMS page.
        $this->db->table('cms_menu_items')->where('page_id IS NULL')->groupStart()
            ->where('url', 'our-franchise')->orWhere('url', '/our-franchise')->groupEnd()
            ->update(['page_id' => $pageId, 'url' => null, 'updated_at' => $this->now]);
    }

    /**
     * @param array<string, string>                     $settings
     * @param array<string, list<array<string, mixed>>> $groups
     */
    private function block(int $pageId, int $sort, string $type, string $label, array $settings, array $groups = []): void
    {
        $this->db->table('cms_blocks')->insert([
            'page_id' => $pageId, 'type' => $type, 'label' => $label, 'settings' => json_encode($settings, JSON_UNESCAPED_UNICODE),
            'sort_order' => $sort, 'status' => 1, 'created_at' => $this->now, 'updated_at' => $this->now,
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
}
