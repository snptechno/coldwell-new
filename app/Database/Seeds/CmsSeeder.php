<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Seeds the CMS with the client-approved home page (Figma "Home – Elegant v2" 41:41),
 * the menus shown in that design and the site settings.
 *
 *   php spark db:seed CmsSeeder
 *
 * Idempotent: each part is only seeded when its table is empty.
 * Unverified facts stay hidden (status 0) until an admin confirms them: the
 * "[XX]K+ Professionals" stat and the Pune / Hyderabad / Chennai city cards.
 */
class CmsSeeder extends Seeder
{
    private const IMG = 'assets/images/home-v2/';

    private string $now;

    public function run(): void
    {
        $this->now = date('Y-m-d H:i:s');
        if ($this->db->table('cms_settings')->countAllResults() === 0) {
            $this->settings();
        }
        if ($this->db->table('cms_pages')->countAllResults() === 0) {
            $this->homePage();
        }
        if ($this->db->table('cms_menus')->countAllResults() === 0) {
            $this->menus();
        }
    }

    private function settings(): void
    {
        $values = [
            'nav_cta_label'        => 'Franchise Enquiry',
            'nav_cta_url'          => 'partner-with-us',
            // Real corporate contacts from the current Contact Us page (contact_us.php).
            'footer_email'         => 'info@coldwellbanker.in',
            'footer_phone'         => '+91-8587819200',
            'footer_address'       => '',
            'footer_quick_title'   => 'Quick Links',
            'footer_network_title' => 'Our Network',
            'footer_media_title'   => 'Media Center',
            'footer_social_title'  => 'Follow us',
            'footer_copyright'     => '© {year} Coldwell Banker India. All rights reserved.',
        ];
        $rows = [];
        foreach ($values as $k => $v) {
            $rows[] = ['setting_key' => $k, 'value' => $v, 'updated_at' => $this->now];
        }
        $this->db->table('cms_settings')->insertBatch($rows);
    }

    private function homePage(): void
    {
        $this->db->table('cms_pages')->insert([
            'type' => 'page', 'title' => 'Home', 'slug' => 'home', 'is_home' => 1, 'nav_key' => 'home', 'status' => 1,
            'published_at' => $this->now, 'meta_title' => null, 'meta_description' => null,
            'created_at' => $this->now, 'updated_at' => $this->now,
        ]);
        $pageId = (int) $this->db->insertID();

        $this->block($pageId, 10, 'hero_slider', 'Hero', ['video_label' => 'Watch our story', 'video_url' => 'cb-india-videos'], [
            'slide' => [
                ['kicker' => 'Coldwell Banker India  ·  Since 1906', 'title' => "Global Legacy.\nIndian Ambition.",
                 'body' => 'The Indian arm of one of the world’s most recognised real estate brands — empowering entrepreneurs, agents and partners to build enduring real estate businesses.',
                 'cta_label' => 'Partner With Us', 'cta_url' => 'partner-with-us', 'image' => self::IMG . 'hero.jpg',
                 'image_alt' => 'Contemporary glass residence with a reflecting pool at blue hour'],
                ['kicker' => 'Franchise opportunity', 'title' => "Own a Territory.\nCarry a Global Brand.",
                 'body' => 'Build your real estate business with Coldwell Banker’s systems, training and brand recognition behind you.',
                 'cta_label' => 'Explore Franchise', 'cta_url' => 'franchise-details', 'image' => self::IMG . 'cta.jpg',
                 'image_alt' => 'Modern residence at night under a starry sky'],
                ['kicker' => 'Our network', 'title' => "Offices in India’s\nKey Metros.",
                 'body' => 'Franchise offices and affiliated professionals across India’s fastest-growing real estate corridors.',
                 'cta_label' => 'Find an Office', 'cta_url' => 'our-franchise', 'image' => self::IMG . 'towers.jpg',
                 'image_alt' => 'Glass office towers at blue hour'],
            ],
            'stat' => [
                ['title' => '120', 'subtitle' => 'Years of legacy'],
                ['title' => '40+', 'subtitle' => 'Countries & territories'],
                ['title' => '[XX]K+', 'subtitle' => 'Professionals', 'status' => 0], // unverified
            ],
        ]);
        $this->block($pageId, 20, 'mission', 'Who we are', [
            'eyebrow' => 'Who we are',
            'statement' => 'At our core, real estate is more than property — it is a century-old promise of trust, integrity and opportunity, now built for India’s long-term growth.',
            'cta_label' => 'More about Coldwell Banker', 'cta_url' => 'about-coldwell-banker',
        ], ['photo' => [
            ['image' => self::IMG . 'villa.jpg', 'image_alt' => 'Luxury villa with an infinity pool at dusk'],
            ['image' => self::IMG . 'towers.jpg', 'image_alt' => 'Glass office towers at blue hour'],
            ['image' => self::IMG . 'living.jpg', 'image_alt' => 'Bright living room with navy accents'],
            ['image' => self::IMG . 'boardroom.jpg', 'image_alt' => 'Professionals meeting in a glass boardroom'],
            ['image' => self::IMG . 'handshake.jpg', 'image_alt' => 'Handshake in a corporate lobby'],
        ]]);
        $this->block($pageId, 30, 'city_slider', 'Our presence', [
            'title' => 'Our Presence Across India',
            'lead'  => 'Franchise offices and affiliated professionals in India’s key metros and fastest-growing real estate corridors.',
        ], ['city' => [
            ['title' => 'Delhi NCR', 'subtitle' => 'Corporate Headquarters', 'body' => 'Gurugram · Noida · New Delhi', 'cta_url' => 'our-franchise', 'image' => self::IMG . 'city-delhi.jpg', 'image_alt' => 'Delhi NCR skyline at dusk'],
            ['title' => 'Mumbai', 'subtitle' => 'Western Hub', 'body' => 'MMR · Navi Mumbai · Thane', 'cta_url' => 'our-franchise', 'image' => self::IMG . 'city-mumbai.jpg', 'image_alt' => 'Mumbai sea-facing skyline at blue hour'],
            ['title' => 'Bengaluru', 'subtitle' => 'Southern Growth Corridor', 'body' => 'Whitefield · North Bengaluru', 'cta_url' => 'our-franchise', 'image' => self::IMG . 'city-bengaluru.jpg', 'image_alt' => 'Bengaluru towers and greenery at dusk'],
            ['title' => 'Pune', 'cta_url' => 'our-franchise', 'image' => self::IMG . 'towers.jpg', 'status' => 0],
            ['title' => 'Hyderabad', 'cta_url' => 'our-franchise', 'image' => self::IMG . 'towers.jpg', 'status' => 0],
            ['title' => 'Chennai', 'cta_url' => 'our-franchise', 'image' => self::IMG . 'towers.jpg', 'status' => 0],
        ]]);
        $this->block($pageId, 40, 'card_grid', 'Ways to grow', [
            'title' => 'Ways to Grow With Us',
            'lead'  => 'Choose the path that matches your ambition — own a franchise, build your career, or partner with us as a corporate client.',
        ], ['card' => [
            ['badge' => 'Flagship Programme', 'kicker' => 'For Entrepreneurs', 'title' => 'Franchise Ownership', 'subtitle' => 'Own territory · Global brand', 'cta_label' => 'Apply Now', 'cta_url' => 'franchise-details', 'image' => self::IMG . 'boardroom.jpg', 'image_alt' => 'Professionals meeting in a glass boardroom'],
            ['badge' => 'Career', 'kicker' => 'For Professionals', 'title' => 'Sales Associate', 'subtitle' => 'Training · Leads · Growth', 'cta_label' => 'Join Us', 'cta_url' => 'career', 'image' => self::IMG . 'family.jpg', 'image_alt' => 'Advisor welcoming a family at their home'],
            ['badge' => 'Enterprise', 'kicker' => 'For Corporates', 'title' => 'Corporate Services', 'subtitle' => 'Leasing · Relocation', 'cta_label' => 'Enquire', 'cta_url' => 'contact-us', 'image' => self::IMG . 'handshake.jpg', 'image_alt' => 'Handshake in a corporate lobby'],
        ]]);
        $this->block($pageId, 50, 'expertise', 'Expertise', [
            'title' => "A Century of Expertise,\nSmarter Decisions",
            'body'  => 'Founded in San Francisco in 1906, Coldwell Banker has guided generations of buyers, sellers and entrepreneurs. Our India team pairs that global playbook with local market intelligence to help every partner grow with confidence.',
            'cta_label' => 'Talk to Our Team', 'cta_url' => 'contact-us',
        ], [
            'photo' => [
                ['image' => self::IMG . 'living.jpg', 'image_alt' => 'Bright living room with navy accents'],
                ['image' => self::IMG . 'towers.jpg', 'image_alt' => 'Glass office towers at blue hour'],
                ['image' => self::IMG . 'villa.jpg', 'image_alt' => 'Luxury villa with an infinity pool at dusk'],
            ],
            'milestone' => [
                ['title' => '1906', 'subtitle' => 'Founded in San Francisco'],
                ['title' => '1913', 'subtitle' => 'Brand name established'],
                ['title' => 'Today', 'subtitle' => '40+ countries & territories'],
            ],
        ]);
        $this->block($pageId, 60, 'newsroom', 'Newsroom', [
            'title' => 'Newsroom & Insights', 'lead' => 'Announcements, research and perspectives from Coldwell Banker India.',
            'cta_label' => 'View all news', 'cta_url' => 'media-center',
        ], []);
        $this->block($pageId, 70, 'newsletter_cta', 'Newsletter', [
            'title' => "Let’s Build a Brighter\nFuture Together",
            'lead'  => 'Get franchise updates, market research and Coldwell Banker India news in your inbox.',
            'image' => self::IMG . 'cta.jpg',
        ], []);
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

    private function menus(): void
    {
        $homeId = (int) ($this->db->table('cms_pages')->select('id')->where('is_home', 1)->get()->getRow('id') ?? 0);
        $menus  = [
            'main' => ['Main navigation', [
                ['Home', ['page_id' => $homeId ?: null, 'url' => $homeId ? null : '/', 'active_keys' => 'home']],
                ['About Us', ['url' => 'about-coldwell-banker', 'active_keys' => 'about us,corporate team']],
                ['What We Do', ['url' => 'learning-development', 'active_keys' => 'learning development,research,career,Franchisees Openings,nri,videos'], [
                    ['Learning & Development', ['url' => 'learning-development', 'active_keys' => 'learning development']],
                    ['Research', ['url' => 'research', 'active_keys' => 'research']],
                    ['Careers', ['url' => 'career', 'active_keys' => 'career,Franchisees Openings']],
                    ['NRI Corner', ['url' => 'nri', 'active_keys' => 'nri']],
                    ['Videos', ['url' => 'finding-a-home', 'active_keys' => 'videos']],
                ]],
                ['Franchise', ['url' => 'franchise-details', 'active_keys' => 'franchise details,partner with us']],
                ['Our Network', ['url' => 'our-franchise', 'active_keys' => 'our franchisee']],
                ['Media', ['url' => 'media-center', 'active_keys' => 'media center,awards']],
                ['Contact', ['url' => 'contact-us', 'active_keys' => 'contact us']],
            ]],
            'footer_quick' => ['Footer — Quick Links', [
                ['Home', ['page_id' => $homeId ?: null, 'url' => $homeId ? null : '/']], ['About Us', ['url' => 'about-coldwell-banker']],
                ['What We Do', ['url' => 'learning-development']], ['Franchise', ['url' => 'franchise-details']],
                ['Careers', ['url' => 'career']], ['Contact', ['url' => 'contact-us']],
            ]],
            'footer_network' => ['Footer — Our Network', [
                ['Delhi NCR', ['url' => 'our-franchise']], ['Mumbai', ['url' => 'our-franchise']], ['Bengaluru', ['url' => 'our-franchise']],
                ['Pune', ['url' => 'our-franchise']], ['Hyderabad', ['url' => 'our-franchise']],
            ]],
            'footer_media' => ['Footer — Media Center', [
                ['News', ['url' => 'media-center']], ['Awards', ['url' => 'awards']], ['Research', ['url' => 'research']], ['Blogs', ['url' => 'blog']],
            ]],
            'footer_legal' => ['Footer — legal links', [
                ['Privacy Policy', ['url' => 'privacy-policy']], ['Terms & Conditions', ['url' => 'terms-and-conditions']], ['Disclaimer', ['url' => 'disclaimer']],
            ]],
            'social' => ['Social links', [
                ['Facebook', ['url' => 'https://www.facebook.com/coldwellbanker.india']],
                ['LinkedIn', ['url' => 'https://www.linkedin.com/company/coldwell-banker-india']],
                ['X (Twitter)', ['url' => 'https://twitter.com/CBIndiatweet']],
                ['YouTube', ['url' => 'https://www.youtube.com/user/coldwellbanker']],
            ]],
        ];
        foreach ($menus as $location => [$name, $items]) {
            $this->db->table('cms_menus')->insert(['location' => $location, 'name' => $name, 'created_at' => $this->now, 'updated_at' => $this->now]);
            $this->menuItems((int) $this->db->insertID(), null, $items);
        }
    }

    /** @param list<array{0: string, 1: array<string, mixed>, 2?: list<mixed>}> $items */
    private function menuItems(int $menuId, ?int $parentId, array $items): void
    {
        foreach ($items as $i => $it) {
            $this->db->table('cms_menu_items')->insert(array_merge([
                'menu_id' => $menuId, 'parent_id' => $parentId, 'label' => $it[0], 'page_id' => null, 'url' => null,
                'new_tab' => 0, 'active_keys' => null, 'sort_order' => ($i + 1) * 10, 'status' => 1,
                'created_at' => $this->now, 'updated_at' => $this->now,
            ], $it[1]));
            if (! empty($it[2])) {
                $this->menuItems($menuId, (int) $this->db->insertID(), $it[2]);
            }
        }
    }
}
