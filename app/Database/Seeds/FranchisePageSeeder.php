<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Seeds the CMS "Franchise" page from the approved Figma frame (Franchise – v2, 77:58).
 *
 *   php spark db:seed FranchisePageSeeder
 *
 * Idempotent (skips when slug "franchise-details" exists). Content that the client has not
 * supplied yet is seeded HIDDEN (status 0) so it never shows as fact:
 *   - FAQ block: questions from the design, answers still to be confirmed.
 *   - Testimonials block: the design needs franchise-owner quotes; the existing
 *     franchises_testimonials rows are client reviews of individual offices, not owner stories.
 * The office finder is automatic (open offices from the franchises table).
 */
class FranchisePageSeeder extends Seeder
{
    public const SLUG  = 'franchise-details';
    private const HOME = 'assets/images/home-v2/';

    private string $now;

    public function run(): void
    {
        $this->now = date('Y-m-d H:i:s');
        if ($this->db->table('cms_pages')->where('slug', self::SLUG)->countAllResults() > 0) {
            return;
        }

        $this->db->table('cms_pages')->insert([
            'type' => 'page', 'title' => 'Franchise', 'slug' => self::SLUG, 'is_home' => 0, 'nav_key' => 'franchise details',
            'status' => 1, 'published_at' => $this->now,
            'meta_title' => 'Real Estate Franchise in India | Coldwell Banker India',
            'meta_description' => 'Own a Coldwell Banker real estate franchise in India: global brand equity, a defined territory, Coldwell Banker University training and ongoing business coaching.',
            'created_at' => $this->now, 'updated_at' => $this->now,
        ]);
        $pageId = (int) $this->db->insertID();

        $this->block($pageId, 10, 'page_hero', 'Hero', [
            'eyebrow' => 'Home  ·  Franchise',
            'title'   => 'Own a Coldwell Banker Franchise',
            'lead'    => 'Build your real estate business on a brand trusted since 1906 — with global systems, training and a growing network across India behind you.',
            'cta_label' => 'Request a Call Back', 'cta_url' => '#callback',
            'image'   => self::HOME . 'boardroom.jpg', 'image_alt' => 'Professionals meeting in a glass boardroom',
        ], []);

        $this->block($pageId, 20, 'values', 'Why partner with us', [
            'layout' => 'grid3', 'eyebrow' => 'Why partner with us',
            'title'  => 'Everything You Need to Grow',
            'lead'   => 'A Coldwell Banker franchise gives you the brand, systems and support of a global leader — with the freedom to build your own business.',
        ], ['value' => [
            ['kicker' => 'globe', 'title' => 'Global Brand Equity', 'subtitle' => 'Trade under one of the world’s most recognised real estate names, trusted since 1906.'],
            ['kicker' => 'pin', 'title' => 'Defined Territory', 'subtitle' => 'Grow within a clearly defined market area agreed with Coldwell Banker India.'],
            ['kicker' => 'award', 'title' => 'Coldwell Banker University', 'subtitle' => 'Structured training and certification for you and every associate you hire.'],
            ['kicker' => 'chart', 'title' => 'Technology & Marketing', 'subtitle' => 'Brand-standard marketing, digital tools and listing support from day one.'],
            ['kicker' => 'people', 'title' => 'Referral Network', 'subtitle' => 'Tap into referrals across the Coldwell Banker network in India and abroad.'],
            ['kicker' => 'handshake', 'title' => 'Business Coaching', 'subtitle' => 'Ongoing guidance on hiring, operations and growth from our franchise team.'],
        ]]);

        $this->block($pageId, 30, 'steps', 'Journey to ownership', [
            'title' => 'Your Journey to Ownership', 'lead' => 'Five clear steps from first conversation to launch day.',
        ], ['step' => [
            ['title' => 'Enquire', 'subtitle' => 'Share your details and we’ll call you back.'],
            ['title' => 'Discovery Call', 'subtitle' => 'Understand the model, investment and fit.'],
            ['title' => 'Territory & Plan', 'subtitle' => 'Agree your market area and business plan.'],
            ['title' => 'Agreement & Onboarding', 'subtitle' => 'Sign up and set up your office and team.'],
            ['title' => 'Launch', 'subtitle' => 'Open with CB University training and launch support.'],
        ]]);

        $this->block($pageId, 40, 'office_finder', 'Find an office', [
            'title' => 'Find a Franchise Office', 'lead' => 'Our franchise network across India — find the office nearest you.',
        ], []);

        // Hidden until real franchise-owner testimonials are supplied.
        $this->block($pageId, 50, 'testimonials', 'Partner stories (add real owner quotes, then show)', [
            'title' => 'Partners Who Grew With Us', 'lead' => 'Stories from Coldwell Banker franchise owners across India.',
        ], [], 0);

        // Hidden until Coldwell Banker India confirms the answers.
        $this->block($pageId, 60, 'faq', 'FAQ (answers to be confirmed, then show)', [
            'title' => "Frequently Asked\nQuestions",
            'lead'  => 'Quick answers for prospective franchise owners. Can’t find yours? Request a call back below.',
        ], ['qa' => [
            ['title' => 'What investment is required?', 'body' => '[Investment range and fee structure — to be confirmed by Coldwell Banker India.]', 'status' => 0],
            ['title' => 'Do I need real estate experience?', 'body' => '[Answer to be confirmed by Coldwell Banker India.]', 'status' => 0],
            ['title' => 'How is my territory defined?', 'body' => '[Answer to be confirmed by Coldwell Banker India.]', 'status' => 0],
            ['title' => 'What training and support will I receive?', 'body' => '[Answer to be confirmed by Coldwell Banker India.]', 'status' => 0],
            ['title' => 'How long does it take to launch?', 'body' => '[Answer to be confirmed by Coldwell Banker India.]', 'status' => 0],
        ]], 0);

        $this->block($pageId, 70, 'enquiry_form', 'Request a call back', [
            'eyebrow' => 'Still have queries?', 'title' => 'Request a Call Back',
            'lead'    => 'Our expert advisors are just a phone call away. Share your details and our franchise team will reach out to discuss the opportunity.',
            'phone'   => '', 'button' => 'Send Request',
        ], []);

        $this->block($pageId, 80, 'newsletter_cta', 'Newsletter', [
            'title' => "Let’s Build a Brighter\nFuture Together",
            'lead'  => 'Get franchise updates, market research and Coldwell Banker India news in your inbox.',
            'image' => self::HOME . 'cta.jpg',
        ], []);

        // The header button and home hero linked to /partner-with-us, which never worked: point them
        // at this page's call-back form.
        $this->db->table('cms_settings')->where('setting_key', 'nav_cta_url')->whereIn('value', ['partner-with-us', '/partner-with-us'])
            ->update(['value' => 'franchise-details#callback', 'updated_at' => $this->now]);
        $this->db->table('cms_block_items')->whereIn('cta_url', ['partner-with-us', '/partner-with-us'])
            ->update(['cta_url' => 'franchise-details#callback', 'updated_at' => $this->now]);

        // Menu "Franchise" links point at the CMS page.
        $this->db->table('cms_menu_items')->where('page_id IS NULL')->groupStart()
            ->where('url', 'franchise-details')->orWhere('url', '/franchise-details')->groupEnd()
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
