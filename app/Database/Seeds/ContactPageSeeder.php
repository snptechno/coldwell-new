<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Seeds the CMS "Contact Us" page from the approved Figma frame (Contact Us – v2, 85:58).
 *
 *   php spark db:seed ContactPageSeeder
 *
 * Idempotent (skips when slug "contact-us" exists). Open client questions from the design notes:
 *   - Address spelling: the source reads "Sputink"; the design uses "Sputnik" (shown, to confirm).
 *   - Map: the old site's map pointed at Orion Business Park, Thane while the address is Bandra
 *     West, so no map embed is seeded (styled placeholder + "Get Directions" to the address).
 *   - Office hours are left empty (hidden) until confirmed.
 * Office counts and phones come live from the franchises table (open offices only).
 */
class ContactPageSeeder extends Seeder
{
    public const SLUG  = 'contact-us';
    private const HOME = 'assets/images/home-v2/';
    private const ADDR = 'A-2, The New Sputnik Co-op. Soc. Ltd, Plot No. 456, TPS 3, 24th Road, Bandra West, Mumbai 400050';

    private string $now;

    public function run(): void
    {
        $this->now = date('Y-m-d H:i:s');
        if ($this->db->table('cms_pages')->where('slug', self::SLUG)->countAllResults() > 0) {
            return;
        }

        $this->db->table('cms_pages')->insert([
            'type' => 'page', 'title' => 'Contact Us', 'slug' => self::SLUG, 'is_home' => 0, 'nav_key' => 'contact us',
            'status' => 1, 'published_at' => $this->now,
            'meta_title' => 'Contact Coldwell Banker India',
            'meta_description' => 'Contact Coldwell Banker India: corporate office in Bandra West, Mumbai, email info@coldwellbanker.in, call +91-8587819200, or find a franchise office near you.',
            'created_at' => $this->now, 'updated_at' => $this->now,
        ]);
        $pageId = (int) $this->db->insertID();

        $this->block($pageId, 10, 'page_hero', 'Hero', [
            'eyebrow' => 'Home  ·  Contact Us',
            'title'   => 'Let’s Talk',
            'lead'    => 'Exploring a franchise, joining as an agent or looking for property advice — our team and franchise offices across India are a call away.',
            'image'   => self::HOME . 'city-mumbai.jpg', 'image_alt' => 'Mumbai skyline at blue hour',
        ], []);

        $this->block($pageId, 20, 'contact_panel', 'Get in touch', [
            'title' => 'Get in Touch',
            'lead'  => 'Reach our corporate team directly, or send a message and we’ll connect you with the right person.',
            'form_title' => 'Send Us a Message', 'form_lead' => 'Our team will get back to you shortly.', 'button' => 'Submit Enquiry',
        ], ['channel' => [
            ['kicker' => 'pin', 'title' => 'Corporate Office', 'body' => self::ADDR],
            ['kicker' => 'mail', 'title' => 'Email Us', 'body' => 'info@coldwellbanker.in', 'cta_url' => 'mailto:info@coldwellbanker.in'],
            ['kicker' => 'phone', 'title' => 'Call Us', 'body' => '+91-8587819200', 'cta_url' => 'tel:+918587819200'],
            ['kicker' => 'building', 'title' => 'Franchise Enquiries', 'body' => 'Explore the franchise opportunity →', 'cta_url' => 'franchise-details'],
        ]]);

        $this->block($pageId, 30, 'location', 'Corporate office', [
            'eyebrow' => 'Coldwell Banker India · Bandra West',
            'title'   => 'Corporate Office, Mumbai',
            'address' => self::ADDR,
            'hours'   => '', // to be confirmed by the client
            'cta_label' => 'Get Directions', 'map_embed' => '',
        ], []);

        $this->block($pageId, 40, 'office_finder', 'Franchise offices', [
            'layout' => 'directory', 'title' => 'Our Franchise Offices',
            'lead'   => '{offices} offices across {cities} cities. Find the Coldwell Banker office nearest you.',
            'cta_label' => 'View All Offices', 'cta_url' => 'our-franchise',
        ], []);

        $this->block($pageId, 50, 'values', 'How can we help', [
            'layout' => 'grid3', 'eyebrow' => '', 'title' => 'How Can We Help?', 'lead' => '',
        ], ['value' => [
            ['kicker' => 'building', 'title' => 'Own a Franchise', 'subtitle' => 'Talk to our franchise team about territories, investment and support.', 'cta_label' => 'Franchise Enquiry', 'cta_url' => 'franchise-details#callback'],
            ['kicker' => 'people', 'title' => 'Join as an Agent', 'subtitle' => 'Build your career with structured agent training and certification.', 'cta_label' => 'Explore Careers', 'cta_url' => 'career'],
            ['kicker' => 'award', 'title' => 'Media & Press', 'subtitle' => 'Press releases, awards and media enquiries.', 'cta_label' => 'Visit Newsroom', 'cta_url' => 'media-center'],
        ]]);

        $this->block($pageId, 60, 'newsletter_cta', 'Newsletter', [
            'title' => "Let’s Build a Brighter\nFuture Together",
            'lead'  => 'Get franchise updates, market research and Coldwell Banker India news in your inbox.',
            'image' => self::HOME . 'cta.jpg',
        ], []);

        $this->db->table('cms_menu_items')->where('page_id IS NULL')->groupStart()
            ->where('url', 'contact-us')->orWhere('url', '/contact-us')->groupEnd()
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
