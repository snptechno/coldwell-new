<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Seeds the CMS "NRI Services" page (slug nri, the existing URL) from Figma "NRI Services – v2"
 * (page 134:599, desktop frame 134:600, mobile frame 152:619).
 *
 *   php spark db:seed NriPageSeeder
 *
 * Services are the ones the old /nri page listed. The enquiry form posts to the existing
 * NRI Enquiry inbox (Admin > NRI Enquiry). Idempotent (skips when the slug exists).
 */
class NriPageSeeder extends Seeder
{
    public const SLUG = 'nri';
    private const IMG = 'assets/images/';

    private string $now;

    public function run(): void
    {
        $this->now = date('Y-m-d H:i:s');
        if ($this->db->table('cms_pages')->where('slug', self::SLUG)->countAllResults() > 0) {
            return;
        }

        $this->db->table('cms_pages')->insert([
            'type' => 'page', 'title' => 'NRI Services', 'slug' => self::SLUG, 'is_home' => 0, 'nav_key' => 'nri',
            'status' => 1, 'published_at' => $this->now,
            'meta_title' => 'NRI Real Estate Services in India | Coldwell Banker India',
            'meta_description' => 'Buy, sell, lease and manage property in India from abroad. Coldwell Banker India’s NRI desk handles legal checks, documentation, tax, registration and repatriation end to end.',
            'created_at' => $this->now, 'updated_at' => $this->now,
        ]);
        $pageId = (int) $this->db->insertID();

        $this->block($pageId, 10, 'page_hero', 'Hero', [
            'eyebrow'   => 'Home  ·  NRI Services',
            'title'     => 'Think Real Estate, Think India',
            'lead'      => 'Investment opportunities back home — end-to-end support for Non-Resident Indians buying, selling and managing property in India.',
            'cta_label' => 'Talk to Our NRI Desk', 'cta_url' => '#nri-enquiry',
            'image'     => self::IMG . 'home-v2/city-mumbai.jpg', 'image_alt' => 'Mumbai’s sea-facing skyline at blue hour',
        ], []);
        $this->block($pageId, 20, 'overview', 'Why India', [
            'eyebrow'   => 'Why India, why now',
            'statement' => 'As global markets appreciate, India takes centre stage as an attractive investment destination — and we help you invest in it with confidence, from anywhere.',
            'chips'     => "NRIs & PIOs\nOCI Card Holders\nReturning Indians\nGlobal Investors",
        ], []);
        $this->block($pageId, 30, 'values', 'Services', [
            'layout' => 'grid3', 'background' => 'panel', 'title' => 'Services for Every Step',
            'lead'   => 'Whether you are buying, selling or leasing out, our NRI desk handles it end to end.',
        ], ['value' => [
            ['kicker' => 'home', 'title' => 'Buy', 'subtitle' => 'Invest in the right home, in the right location.',
                'body' => "Right Location\nRight Property\nNegotiation\nLegal Consultancy & Documentation\nProperty Valuation"],
            ['kicker' => 'tag', 'title' => 'Sell', 'subtitle' => 'Get the right buyer and the right price.',
                'body' => "Right Buyer\nNegotiation\nDocumentation\nLegal Consultancy\nFinance"],
            ['kicker' => 'key', 'title' => 'Lease & Manage', 'subtitle' => 'Your property, looked after while you are away.',
                'body' => "Tenant Acquisition\nProperty Maintenance\nRent Collection & Deposit\nTax Consultancy\nRegistration & Stamp Duty\nRepatriation"],
        ]]);
        $this->block($pageId, 40, 'steps', 'How it works', [
            'style' => 'plain', 'title' => 'How It Works',
            'lead'  => 'From first call to rent in your account — a single point of contact the whole way.',
        ], ['step' => [
            ['title' => 'Share Your Goals', 'subtitle' => 'Tell our NRI desk what, where and why you want to invest.'],
            ['title' => 'Shortlist & Virtual Visits', 'subtitle' => 'Curated options with video walkthroughs and area insights.'],
            ['title' => 'Legal & Due Diligence', 'subtitle' => 'Title checks, documentation and valuation by experts.'],
            ['title' => 'Close Remotely', 'subtitle' => 'Power of attorney, registration and stamp duty handled.'],
            ['title' => 'Manage & Repatriate', 'subtitle' => 'Tenancy, maintenance, rent collection and repatriation.'],
        ]]);
        $this->block($pageId, 50, 'nri_enquiry_form', 'NRI desk form', [
            'eyebrow' => 'NRI Desk', 'title' => 'Talk to Our NRI Desk',
            'lead'    => 'Share your details and our NRI desk will call you at a time that suits your time zone.',
            'phone'   => '', 'button' => 'Send Request',
        ], []);
        $this->block($pageId, 60, 'newsletter_cta', 'Newsletter', [
            'title' => "Let’s Build a Brighter\nFuture Together",
            'lead'  => 'Get franchise updates, market research and Coldwell Banker India news in your inbox.',
            'image' => self::IMG . 'home-v2/cta.jpg',
        ], []);

        // The "NRI Corner" menu item pointed at the old route: link it to the CMS page.
        $this->db->table('cms_menu_items')->where('page_id IS NULL')->groupStart()
            ->where('url', 'nri')->orWhere('url', '/nri')->groupEnd()
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
