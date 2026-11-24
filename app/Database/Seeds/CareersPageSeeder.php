<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Seeds the CMS "Careers" page (slug career, the existing URL) from Figma Careers 90:58.
 *
 *   php spark db:seed CareersPageSeeder
 *
 * Openings and the application form read the jobs table live (Admin > Job Openings), so they
 * always match what HR has published. Employee stories are seeded HIDDEN until real ones are
 * supplied. Idempotent (skips when the slug exists).
 */
class CareersPageSeeder extends Seeder
{
    public const SLUG = 'career';
    private const IMG = 'assets/images/';

    private string $now;

    public function run(): void
    {
        $this->now = date('Y-m-d H:i:s');
        if ($this->db->table('cms_pages')->where('slug', self::SLUG)->countAllResults() > 0) {
            return;
        }

        $this->db->table('cms_pages')->insert([
            'type' => 'page', 'title' => 'Careers', 'slug' => self::SLUG, 'is_home' => 0, 'nav_key' => 'career',
            'status' => 1, 'published_at' => $this->now,
            'meta_title' => 'Careers at Coldwell Banker India | Current Openings',
            'meta_description' => 'Build your career in real estate with Coldwell Banker India: current openings at the master franchise and across our franchise network, and how to apply.',
            'created_at' => $this->now, 'updated_at' => $this->now,
        ]);
        $pageId = (int) $this->db->insertID();

        $this->block($pageId, 10, 'page_hero', 'Hero', [
            'eyebrow' => 'Home  ·  Careers',
            'title'   => 'Be Part of the Most Respected Brand in Real Estate',
            'lead'    => 'Aspiring to build a career in real estate? Associate with Coldwell Banker India and give direction to your endless growth.',
            'cta_label' => 'View Openings', 'cta_url' => '#openings',
            'image'   => self::IMG . 'home-v2/handshake.jpg', 'image_alt' => 'Two professionals shaking hands across a desk',
        ], []);
        $this->block($pageId, 20, 'values', 'Why join', [
            'layout' => 'grid4', 'title' => 'Why Build Your Career With Us',
            'lead'   => 'Join a global brand with a century of expertise — and grow with structured training and a nationwide network.',
        ], ['value' => [
            ['kicker' => 'globe', 'title' => 'A Global Brand', 'subtitle' => 'Represent one of the most recognised names in real estate, trusted since 1906.'],
            ['kicker' => 'award', 'title' => 'World-Class Training', 'subtitle' => 'Learn with programs benchmarked to Coldwell Banker University.'],
            ['kicker' => 'chart', 'title' => 'Clear Growth Path', 'subtitle' => 'Grow from associate to manager — or to franchise ownership.'],
            ['kicker' => 'building', 'title' => 'A Nationwide Network', 'subtitle' => 'Work with franchise offices across India’s key cities.'],
        ]]);
        $this->block($pageId, 30, 'job_openings', 'Current openings', [
            'title' => 'Current Openings',
            'lead'  => 'Roles at Coldwell Banker India and across our franchise network.',
        ], []);
        $this->block($pageId, 40, 'expertise', 'Life at Coldwell Banker', [
            'title' => 'Life at Coldwell Banker',
            'body'  => 'From your first Franchise Specialist Program to networking meets with peers across India, you’ll learn alongside professionals who share a century-old commitment to integrity and service.',
            'cta_label' => 'Learning & Development', 'cta_url' => 'learning-development', 'cta_tone' => 'light',
        ], [
            'photo' => [
                ['image' => self::IMG . 'learning-v2/training-1.jpg', 'image_alt' => 'Coldwell Banker training session'],
                ['image' => self::IMG . 'learning-v2/training-2.jpg', 'image_alt' => 'Participants at a Coldwell Banker workshop'],
                ['image' => self::IMG . 'learning-v2/training-3.jpg', 'image_alt' => 'Coldwell Banker team at a networking meet'],
            ],
            'milestone' => [
                ['title' => 'FSP', 'subtitle' => 'Franchise Specialist Program'],
                ['title' => 'Evolve', 'subtitle' => 'Networking meets'],
                ['title' => 'CBU', 'subtitle' => 'Certification'],
            ],
        ]);
        // Hidden until real employee stories (name, role, consent) are supplied.
        $this->block($pageId, 50, 'testimonials', 'Employee stories (add real quotes, then show)', [
            'title' => 'Inspiring Stories of Success',
            'lead'  => 'Hear from the people building their careers with Coldwell Banker India.',
        ], [], 0);
        $this->block($pageId, 60, 'job_apply_form', 'Application form', [
            'eyebrow' => 'Apply', 'title' => 'Job Application Form',
            'lead'    => 'Please fill in your details to help us understand your requirements. Choose a role, attach your résumé, and our HR team will be in touch.',
            'cv_email' => '', 'button' => 'Submit Application',
        ], []);
        $this->block($pageId, 70, 'newsletter_cta', 'Newsletter', [
            'title' => "Let’s Build a Brighter\nFuture Together",
            'lead'  => 'Get career, franchise and Coldwell Banker India news in your inbox.',
            'image' => self::IMG . 'home-v2/cta.jpg',
        ], []);

        $this->db->table('cms_menu_items')->where('page_id IS NULL')->groupStart()
            ->where('url', 'career')->orWhere('url', '/career')->groupEnd()
            ->update(['page_id' => $pageId, 'url' => null, 'updated_at' => $this->now]);
    }

    /** @param array<string, list<array<string, mixed>>> $groups */
    private function block(int $pageId, int $sort, string $type, string $label, array $settings, array $groups, int $status = 1): void
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
}
