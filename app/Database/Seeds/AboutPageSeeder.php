<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Seeds the CMS "About Us" page from the approved Figma frame (About Us – v2, 73:58) with the
 * real copy of the live About page.
 *
 *   php spark db:seed AboutPageSeeder
 *
 * Idempotent: does nothing when a page with slug "about-coldwell-banker" exists.
 * Facts are only shown when verified: "Offices worldwide", "Affiliated agents" and
 * "150+ agents" stay hidden (status 0); "9 cities / 29 franchise offices" come from the
 * franchises table (open offices, Nov 2026). The MD bio is left empty until supplied.
 */
class AboutPageSeeder extends Seeder
{
    private const IMG   = 'assets/images/about-v2/';
    private const HOME  = 'assets/images/home-v2/';
    public const SLUG   = 'about-coldwell-banker';

    private string $now;

    public function run(): void
    {
        $this->now = date('Y-m-d H:i:s');
        if ($this->db->table('cms_pages')->where('slug', self::SLUG)->countAllResults() > 0) {
            return;
        }

        // SEO from the legacy pages row (About Us = id 10), when that table exists.
        $seo = [];
        try {
            if ($this->db->tableExists('pages')) {
                $seo = $this->db->table('pages')->select('title, meta_description, meta_keywords')->where('id', 10)->get()->getRowArray() ?: [];
            }
        } catch (\Throwable) {
            $seo = [];
        }

        $this->db->table('cms_pages')->insert([
            'type' => 'page', 'title' => 'About Us', 'slug' => self::SLUG, 'is_home' => 0, 'nav_key' => 'about us',
            'status' => 1, 'published_at' => $this->now,
            'meta_title' => $seo['title'] ?? 'About Coldwell Banker India',
            'meta_description' => $seo['meta_description'] ?? 'Coldwell Banker India is the Indian arm of Coldwell Banker Real Estate LLC, fulfilling the dream of home since 1906.',
            'meta_keywords' => $seo['meta_keywords'] ?? null,
            'created_at' => $this->now, 'updated_at' => $this->now,
        ]);
        $pageId = (int) $this->db->insertID();

        $this->block($pageId, 10, 'page_hero', 'Hero', [
            'eyebrow' => 'Home  ·  About Us',
            'title'   => 'About the Coldwell Banker® Brand',
            'lead'    => 'Fulfilling the dream of home since 1906. For over a century, helping people find the perfect home — ethically and reliably — has been our purpose.',
            'image'   => self::HOME . 'towers.jpg', 'image_alt' => 'Glass office towers at blue hour',
        ], []);

        $this->block($pageId, 20, 'stats_panel', 'Our purpose', [
            'layout'  => 'center', 'eyebrow' => 'Our purpose',
            'body'    => 'Our vision defines us — and every professional in our global network. Our aim will never diverge from our mission: to deliver the dream of home, ethically and reliably.',
        ], ['stat' => [
            ['title' => '1906', 'subtitle' => 'Founded in San Francisco'],
            ['title' => '40+', 'subtitle' => 'Countries & territories'],
            ['title' => '[XX]+', 'subtitle' => 'Offices worldwide', 'status' => 0],    // unverified
            ['title' => '[XX]K+', 'subtitle' => 'Affiliated agents', 'status' => 0],   // unverified
        ]]);

        $this->block($pageId, 30, 'expertise', 'A story of leadership', [
            'title' => 'A Story of Leadership',
            'body'  => 'After San Francisco was hit by a devastating earthquake, a young real estate agent, Colbert Coldwell, saw the need for a trustworthy, honest and knowledgeable real estate service to help rebuild the city. He founded his firm on August 27, 1906, and later welcomed Benjamin Arthur Banker as a partner. For more than a century, professionalism and superior customer service have remained at the core of the Coldwell Banker philosophy.',
            'cta_label' => 'Explore Franchise', 'cta_url' => 'franchise-details',
        ], [
            'photo' => [
                ['image' => self::HOME . 'living.jpg', 'image_alt' => 'Bright living room with navy accents'],
                ['image' => self::HOME . 'villa.jpg', 'image_alt' => 'Luxury villa with an infinity pool at dusk'],
                ['image' => self::HOME . 'towers.jpg', 'image_alt' => 'Glass office towers at blue hour'],
            ],
            'milestone' => [
                ['title' => '27 Aug 1906', 'subtitle' => 'Founded in San Francisco'],
                ['title' => 'Partner', 'subtitle' => 'Benjamin Arthur Banker joins'],
                ['title' => 'Today', 'subtitle' => '40+ countries & territories'],
            ],
        ]);

        $this->block($pageId, 40, 'stats_panel', 'Coldwell Banker India', [
            'layout' => 'split', 'title' => 'About Coldwell Banker India',
            'body'   => 'Coldwell Banker India is the Indian arm of Coldwell Banker Real Estate LLC. Known for people-centric endeavours to fulfil the dream of a perfect home, we carry forward a legacy first founded in 1906 in North America — rendering credible real estate advisory services through a dedicated team and a widespread network of affiliated sales associates.',
            'cta_label' => 'Our Network', 'cta_url' => 'our-franchise',
        ], ['stat' => [
            ['title' => '9', 'subtitle' => 'Cities across India', 'kicker' => 'pin'],
            ['title' => '29', 'subtitle' => 'Franchise offices', 'kicker' => 'building'],
            ['title' => '150+', 'subtitle' => 'Affiliated agents', 'kicker' => 'people', 'status' => 0], // unverified
        ]]);

        $this->block($pageId, 50, 'leaders', 'Leadership', [
            'title' => 'Our Leadership',
            'lead'  => 'The team guiding Coldwell Banker India’s growth and partner success.',
            'cta_label' => 'Corporate Team', 'cta_url' => 'corporate-team',
        ], ['person' => [
            ['title' => 'Ramnik Chopra', 'subtitle' => 'Managing Director', 'body' => '', 'image' => self::IMG . 'leader-ramnik.jpg', 'image_alt' => 'Ramnik Chopra, Managing Director'],
            ['title' => 'Anupam Sinha', 'subtitle' => 'Director', 'image' => self::IMG . 'leader-anupam.jpg', 'image_alt' => 'Anupam Sinha'],
            ['title' => 'Charu D’souza', 'subtitle' => 'HR Head', 'image' => self::IMG . 'leader-charu.jpg', 'image_alt' => 'Charu D’souza'],
            ['title' => 'Mojahid Taj Syed', 'subtitle' => 'Head NRI Sales – India', 'image' => self::IMG . 'leader-syed.jpg', 'image_alt' => 'Mojahid Taj Syed'],
            ['title' => 'Rahul Gaikwad', 'subtitle' => 'Head – International Business Development', 'image' => self::IMG . 'leader-rahul.jpg', 'image_alt' => 'Rahul Gaikwad'],
            ['title' => 'Alok Tiwari', 'subtitle' => 'Business Development', 'image' => self::IMG . 'leader-alok.jpg', 'image_alt' => 'Alok Tiwari'],
            ['title' => 'Diana Joyal', 'subtitle' => 'General Manager – Operations, Strategy, Marketing', 'image' => self::IMG . 'leader-diana.jpg', 'image_alt' => 'Diana Joyal'],
        ]]);

        $this->block($pageId, 60, 'feature_cards', 'Why Coldwell Banker', [
            'title' => 'Why Coldwell Banker',
            'lead'  => 'A century of experience, the widest reach in the industry, and a culture built to keep you ahead.',
        ], ['card' => [
            ['title' => 'Legacy', 'body' => 'For over a century, professionalism and superior customer service have been core philosophies of Coldwell Banker. No other real estate company offers such vast exposure coupled with so many years of experience.'],
            ['title' => 'Generation Blue', 'body' => 'Join the Coldwell Banker network and become part of Generation Blue — a shared culture of professionals across 40+ countries focused on growing their business and staying ahead of technology and industry trends.', 'image' => self::IMG . 'logo-genblue.png', 'image_alt' => 'Generation Blue Experience'],
            ['title' => 'Innovation', 'body' => "An industry pioneer for over a century, Coldwell Banker was the first full-service real estate brand to:\n- Launch a national website\n- Stream online videos\n- Create apps and responsive websites\n- Start a dedicated YouTube channel\n- Be recognised for best-in-class training"],
            ['title' => 'Coldwell Banker University', 'body' => 'Make success a habit with training and expert guidance. Our internationally recognised modules equip you with the skills to deliver a truly exceptional customer experience.', 'image' => self::IMG . 'logo-cbu.jpg', 'image_alt' => 'Coldwell Banker University'],
        ]]);

        $this->block($pageId, 70, 'values', 'Our values', [
            'title' => 'Our Values',
            'lead'  => 'The principles behind every franchise, every associate and every client relationship.',
        ], ['value' => [
            ['kicker' => 'shield', 'title' => 'Trust', 'subtitle' => 'Honest, transparent advice at every step.'],
            ['kicker' => 'bulb', 'title' => 'Innovation', 'subtitle' => 'Pioneering tools and ideas since 1906.'],
            ['kicker' => 'handshake', 'title' => 'Collaboration', 'subtitle' => 'One network, sharing knowledge and success.'],
            ['kicker' => 'chart', 'title' => 'Business Acumen', 'subtitle' => 'Smart, data-led decisions that build value.'],
            ['kicker' => 'heart', 'title' => 'Customer Focus', 'subtitle' => 'Every decision begins with the customer.'],
        ]]);

        $this->block($pageId, 80, 'newsletter_cta', 'Newsletter', [
            'title' => "Let’s Build a Brighter\nFuture Together",
            'lead'  => 'Get franchise updates, market research and Coldwell Banker India news in your inbox.',
            'image' => self::HOME . 'cta.jpg',
        ], []);

        // Point the main/footer "About Us" links at the CMS page.
        $this->db->table('cms_menu_items')->where('page_id IS NULL')->groupStart()
            ->where('url', 'about-coldwell-banker')->orWhere('url', '/about-coldwell-banker')->groupEnd()
            ->update(['page_id' => $pageId, 'url' => null, 'updated_at' => $this->now]);
    }

    /** @param array<string, list<array<string, mixed>>> $groups */
    private function block(int $pageId, int $sort, string $type, string $label, array $settings, array $groups): void
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
