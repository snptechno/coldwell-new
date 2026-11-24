<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Seeds the CMS "Learning & Development" page from the approved Figma frame (L&D – v2, 83:58).
 *
 *   php spark db:seed LearningPageSeeder
 *
 * Idempotent (skips when slug "learning-development" exists). The training calendar and photo
 * gallery are automatic (Admin > Learning: Events and Gallery). The calendar never lists past
 * events: all events in the data are from 2017/2021, so it shows the "next batches" message
 * until a new batch is added. The page keeps its live URL (memory: L&D must stay a real page).
 */
class LearningPageSeeder extends Seeder
{
    public const SLUG  = 'learning-development';
    private const HOME = 'assets/images/home-v2/';
    private const LD   = 'assets/images/learning-v2/';

    private string $now;

    public function run(): void
    {
        $this->now = date('Y-m-d H:i:s');
        if ($this->db->table('cms_pages')->where('slug', self::SLUG)->countAllResults() > 0) {
            return;
        }

        $this->db->table('cms_pages')->insert([
            'type' => 'page', 'title' => 'Learning & Development', 'slug' => self::SLUG, 'is_home' => 0, 'nav_key' => 'learning development',
            'status' => 1, 'published_at' => $this->now,
            'meta_title' => 'Learning & Development | Coldwell Banker University India',
            'meta_description' => 'Coldwell Banker India training for franchise owners and agents: Coldwell Banker University programs, training calendar and photo gallery.',
            'created_at' => $this->now, 'updated_at' => $this->now,
        ]);
        $pageId = (int) $this->db->insertID();

        $this->block($pageId, 10, 'page_hero', 'Hero', [
            'eyebrow' => 'Home  ·  Learning & Development',
            'title'   => 'If You Learn, You Last.',
            'lead'    => 'If you develop, you drive. Practical, internationally benchmarked learning programs for Coldwell Banker franchise owners and agents.',
            'cta_label' => 'View Training Calendar', 'cta_url' => '#calendar',
            'image'   => self::HOME . 'news-research.jpg', 'image_alt' => 'Research desk with market charts',
        ]);
        $this->block($pageId, 20, 'overview', 'Overview', [
            'eyebrow'   => 'Overview',
            'statement' => 'Whether you are a seasoned real estate professional or just learning the ropes, we equip our specialists with the skills to meet today’s ever-changing market.',
            'body'      => 'Our training workshops provide relevant, updated and practical development for franchise owners and agents — building a solid foundation for a successful career while generating revenue for our franchisees.',
            'chips'     => "Franchise Owners\nSales Associates\nNew Agents\nManagers",
        ]);
        $this->block($pageId, 30, 'expertise', 'Coldwell Banker University', [
            'logo'  => 'assets/images/about-v2/logo-cbu.jpg',
            'title' => 'Coldwell Banker University',
            'quote' => '“Coldwell Banker University® learning programs are not simply about the practice of selling real estate; they’re about the practice of running a smarter business.”',
            'body'  => 'For over 30 years, CBU has developed and delivered learning content — online and off — to help Coldwell Banker® sales professionals achieve extraordinary business results, and give consumers the exceptional experience they deserve.',
        ], [
            'photo' => [
                ['image' => self::LD . 'training-1.jpg', 'image_alt' => 'Franchise Specialist Program classroom session'],
                ['image' => self::LD . 'training-2.jpg', 'image_alt' => 'Agents at a Coldwell Banker training workshop'],
                ['image' => self::LD . 'training-3.jpg', 'image_alt' => 'Trainer presenting at a Franchise Specialist Program'],
            ],
            'milestone' => [
                ['title' => '30+ yrs', 'subtitle' => 'of learning content'],
                ['title' => 'Online', 'subtitle' => '& classroom programs'],
                ['title' => 'Gen Blue', 'subtitle' => 'Experience network'],
            ],
        ]);
        $this->block($pageId, 40, 'numbered_cards', 'Training programs', [
            'title' => 'Training Programs',
            'lead'  => 'Practical modules designed for the Indian market and benchmarked to Coldwell Banker University standards.',
        ], ['card' => [
            ['title' => 'Product Programs', 'subtitle' => 'Master transactions end to end across every product line.', 'kicker' => 'Primary, Secondary, Rental'],
            ['title' => 'Technical & Legal Training', 'subtitle' => 'Documentation, compliance and the legal essentials of every deal.'],
            ['title' => 'How to Win a Mandate', 'subtitle' => 'Pitch your value and win exclusive seller mandates.'],
            ['title' => 'Capital Gains', 'subtitle' => 'Advise clients confidently on the tax side of property sales.'],
            ['title' => 'Negotiation & Closing Skills', 'subtitle' => 'Proven techniques to negotiate value and close with confidence.'],
            ['title' => 'Innovative Listings', 'subtitle' => 'Present primary and secondary listings that stand out.'],
        ]]);
        $this->block($pageId, 50, 'event_calendar', 'Training calendar', [
            'title' => 'Training Calendar',
            'lead'  => 'Upcoming Coldwell Banker India programs. Seats are limited for each batch.',
            'limit' => '5', 'cta_label' => 'Register Interest', 'cta_url' => 'contact-us#contact-form',
            'empty_title' => 'New training batches will be announced soon.',
            'empty_lead'  => 'Register your interest and our learning team will share the dates of the next Franchise Specialist Program and certification batches.',
        ]);
        $this->block($pageId, 60, 'album_gallery', 'Photo gallery', [
            'title' => 'Photo Gallery', 'lead' => 'Moments from our Franchise Specialist Programs and networking meets.',
            'cta_label' => '', 'cta_url' => '',
        ]);
        $this->block($pageId, 70, 'newsletter_cta', 'Newsletter', [
            'title' => "Let’s Build a Brighter\nFuture Together",
            'lead'  => 'Get franchise updates, market research and Coldwell Banker India news in your inbox.',
            'image' => self::HOME . 'cta.jpg',
        ]);

        $this->db->table('cms_menu_items')->where('page_id IS NULL')->groupStart()
            ->where('url', 'learning-development')->orWhere('url', '/learning-development')->groupEnd()
            ->update(['page_id' => $pageId, 'url' => null, 'updated_at' => $this->now]);
    }

    /**
     * @param array<string, string>               $settings
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
