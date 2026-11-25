<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Seeds all 8 Group B live-data CMS pages:
 *   1. industry-news       (Figma 139:2)
 *   2. knowledge-series    (Figma 139:160)
 *   3. newsletter          (Figma 139:353)
 *   4. awards              (Figma 140:2)
 *   5. cb-india-videos     (Figma 140:197)
 *   6. featured-videos     (Figma 140:416)
 *   7. franchisees-openings(Figma 134:304)
 *   8. area-insights       (Figma 136:2)
 *
 * Idempotent: skips any slug that already exists in cms_pages.
 *   php spark db:seed GroupBPagesSeeder
 */
class GroupBPagesSeeder extends Seeder
{
    private const HOME = 'assets/images/home-v2/';
    private string $now;

    public function run(): void
    {
        $this->now = date('Y-m-d H:i:s');
        $cta = [
            'title' => "Let's Build a Brighter\nFuture Together",
            'lead'  => 'Get franchise updates, market research and Coldwell Banker India news in your inbox.',
            'image' => self::HOME . 'cta.jpg',
        ];

        // 1. Industry News
        $this->seedPage('industry-news', 'Industry News', 'media center', [
            'meta_title'       => 'Industry News | Coldwell Banker India',
            'meta_description' => 'Latest Indian real estate industry news, policy updates, and market developments curated by Coldwell Banker India.',
        ], [
            ['page_hero', 'Hero', [
                'eyebrow'   => 'Home  ·  Media  ·  Industry News',
                'title'     => 'Industry News & Market Pulse',
                'lead'      => 'Stay informed with curated coverage of Indian real estate trends, regulations, and sector developments.',
                'image'     => self::HOME . 'news-press.jpg',
                'image_alt' => 'Real estate industry press coverage',
            ]],
            ['media_tabs', 'Media Tabs', ['active' => 'industry']],
            ['media_archive', 'Industry News Grid', ['source' => 'industry', 'title' => 'Industry News', 'per_page' => '6', 'load_label' => 'Load More']],
            ['newsletter_cta', 'Newsletter CTA', $cta],
        ]);

        // 2. Knowledge Series
        $this->seedPage('knowledge-series', 'Knowledge Series', 'media center', [
            'meta_title'       => 'Knowledge Series | Coldwell Banker India',
            'meta_description' => 'Expert articles, guides, and insights from Coldwell Banker India Knowledge Series.',
        ], [
            ['page_hero', 'Hero', [
                'eyebrow'   => 'Home  ·  Media  ·  Knowledge Series',
                'title'     => 'Coldwell Banker Knowledge Series',
                'lead'      => 'Deep-dive guides and expert perspectives for homebuyers, sellers, and real estate professionals.',
                'image'     => self::HOME . 'boardroom.jpg',
                'image_alt' => 'Coldwell Banker India knowledge session',
            ]],
            ['media_tabs', 'Media Tabs', ['active' => 'knowledge']],
            ['media_archive', 'Knowledge Series Grid', ['source' => 'knowledge', 'title' => 'Knowledge Series', 'per_page' => '6', 'load_label' => 'Load More']],
            ['newsletter_cta', 'Newsletter CTA', $cta],
        ]);

        // 3. Newsletter Archive
        $this->seedPage('newsletter', 'Newsletter', 'media center', [
            'meta_title'       => 'Newsletter Archive | Coldwell Banker India',
            'meta_description' => 'Browse past editions of the Coldwell Banker India newsletter.',
        ], [
            ['page_hero', 'Hero', [
                'eyebrow'   => 'Home  ·  Media  ·  Newsletter',
                'title'     => 'Coldwell Banker India Newsletter',
                'lead'      => 'Periodic editions covering network milestones, market reports, and franchise highlights.',
                'image'     => self::HOME . 'news-research.jpg',
                'image_alt' => 'Coldwell Banker India newsletter editions',
            ]],
            ['media_tabs', 'Media Tabs', ['active' => 'newsletter']],
            ['media_archive', 'Newsletter Grid', ['source' => 'newsletter', 'title' => 'Newsletter Archive', 'per_page' => '6', 'load_label' => 'Load More']],
            ['newsletter_cta', 'Newsletter CTA', $cta],
        ]);

        // 4. Awards
        $this->seedPage('awards', 'Awards & Recognition', 'media center', [
            'meta_title'       => 'Awards & Recognition | Coldwell Banker India',
            'meta_description' => 'Industry awards and honours earned by Coldwell Banker India.',
        ], [
            ['page_hero', 'Hero', [
                'eyebrow'   => 'Home  ·  Media  ·  Awards',
                'title'     => 'Awards & Recognition',
                'lead'      => 'Celebrating excellence in real estate franchising, brokerage leadership, and customer trust.',
                'image'     => self::HOME . 'towers.jpg',
                'image_alt' => 'Coldwell Banker India awards and recognition',
            ]],
            ['media_tabs', 'Media Tabs', ['active' => 'awards']],
            ['awards_list', 'Awards List', [
                'title'     => 'Honours & Accolades',
                'lead'      => 'Recognised by leading Indian and global real estate bodies.',
                'limit'     => '20',
                'cta_label' => 'Explore Franchise',
                'cta_url'   => 'franchise-details',
            ]],
            ['newsletter_cta', 'Newsletter CTA', $cta],
        ]);

        // 5. CB India Videos
        $this->seedPage('cb-india-videos', 'Videos', 'media center', [
            'meta_title'       => 'Videos | Coldwell Banker India',
            'meta_description' => 'Films, guides and success stories from Coldwell Banker India and the global Coldwell Banker network.',
        ], [
            ['page_hero', 'Hero', [
                'eyebrow'   => 'Home  ·  Media  ·  Videos',
                'title'     => 'Stories in Motion',
                'lead'      => 'Films, guides and success stories from Coldwell Banker India and the global Coldwell Banker network.',
                'image'     => self::HOME . 'news-video.jpg',
                'image_alt' => 'Coldwell Banker India videos',
            ]],
            ['media_tabs', 'Media Tabs', ['active' => 'videos']],
            ['video_hub', 'Videos', [
                'title'       => 'Videos',
                'lead'        => 'Stories, guides and films from the Coldwell Banker network.',
                'default_tab' => 'CB India',
                'more_title'  => 'More From CB India',
                'per_page'    => '6',
                'load_label'  => 'Load More',
            ]],
            ['newsletter_cta', 'Newsletter CTA', $cta],
        ]);

        // 6. Featured Videos
        $this->seedPage('featured-videos', 'Featured Videos', 'media center', [
            'meta_title'       => 'Featured Videos | Coldwell Banker India',
            'meta_description' => 'Featured films and spotlight stories from Coldwell Banker India.',
        ], [
            ['page_hero', 'Hero', [
                'eyebrow'   => 'Home  ·  Media  ·  Featured Videos',
                'title'     => 'Featured Videos',
                'lead'      => 'Spotlight films, brand stories, and highlights from Coldwell Banker India.',
                'image'     => self::HOME . 'news-video.jpg',
                'image_alt' => 'Coldwell Banker India featured videos',
            ]],
            ['media_tabs', 'Media Tabs', ['active' => 'videos']],
            ['video_hub', 'Featured Videos', [
                'title'       => 'Featured Videos',
                'lead'        => 'Curated spotlight films from the Coldwell Banker network.',
                'default_tab' => 'Featured',
                'more_title'  => 'More From CB India',
                'per_page'    => '6',
                'load_label'  => 'Load More',
            ]],
            ['newsletter_cta', 'Newsletter CTA', $cta],
        ]);

        // 7. Franchise Openings
        $this->seedPage('franchisees-openings', 'Franchise Openings', 'career', [
            'meta_title'       => 'Franchise Openings | Coldwell Banker India',
            'meta_description' => 'Explore current career opportunities across Coldwell Banker India franchise offices.',
        ], [
            ['page_hero', 'Hero', [
                'eyebrow'   => 'Home  ·  Careers  ·  Franchise Openings',
                'title'     => 'Franchise Career Openings',
                'lead'      => 'Join Coldwell Banker India franchise offices across leading cities and grow your real estate career.',
                'image'     => self::HOME . 'handshake.jpg',
                'image_alt' => 'Coldwell Banker India franchise careers',
            ]],
            ['job_openings', 'Current Openings', [
                'eyebrow' => 'CURRENT OPENINGS',
                'title'   => 'Open Positions Across Our Network',
                'lead'    => 'Explore active roles and apply directly below.',
            ]],
            ['job_apply_form', 'Apply Now', [
                'eyebrow' => 'APPLY NOW',
                'title'   => 'Submit Your Application',
                'lead'    => 'Share your profile and resume with our hiring team.',
            ]],
            ['newsletter_cta', 'Newsletter CTA', $cta],
        ]);

        // 8. Area Insights
        $this->seedPage('area-insights', 'Area Insights', 'research', [
            'meta_title'       => 'Area Insights | Coldwell Banker India',
            'meta_description' => 'In-depth area insights and neighbourhood research reports from Coldwell Banker India.',
        ], [
            ['page_hero', 'Hero', [
                'eyebrow'   => 'Home  ·  Insights  ·  Area Insights',
                'title'     => 'Market Research & Area Insights',
                'lead'      => "Know what's new in real estate. In-depth area insights and project reports to help you make informed decisions.",
                'image'     => self::HOME . 'news-research.jpg',
                'image_alt' => 'Real estate area insights and market charts',
            ]],
            ['research_tabs', 'Research Tabs', ['active' => 'area']],
            ['featured_report', 'Featured Insight', [
                'title'     => 'Latest Area Insight',
                'limit'     => '1',
                'cta_label' => 'Download PDF',
                'active'    => 'area',
            ]],
            ['research_grid', 'Insights Grid', [
                'title'      => 'All Project Reports',
                'area_title' => 'All Area Insights',
                'per_page'   => '6',
                'cta_label'  => 'Download',
                'load_label' => 'Load More',
                'active'     => 'area',
            ]],
            ['approach_panel', 'Our Approach', [
                'eyebrow'   => 'OUR APPROACH',
                'statement' => 'Whether it is a residential property or a commercial space, we partner with you in making an informed decision.',
                'cta_label' => 'Talk to an Advisor',
                'cta_url'   => 'contact-us',
            ]],
            ['newsletter_cta', 'Newsletter CTA', $cta],
        ]);
    }

    /**
     * @param array<string, string>                      $meta
     * @param list<array{0: string, 1: string, 2: array}> $blocks
     */
    private function seedPage(string $slug, string $title, string $navKey, array $meta, array $blocks): void
    {
        if ($this->db->table('cms_pages')->where('slug', $slug)->countAllResults() > 0) {
            return;
        }

        $this->db->table('cms_pages')->insert([
            'type'             => 'page',
            'title'            => $title,
            'slug'             => $slug,
            'is_home'          => 0,
            'nav_key'          => $navKey,
            'status'           => 1,
            'published_at'     => $this->now,
            'meta_title'       => $meta['meta_title'] ?? ($title . ' | Coldwell Banker India'),
            'meta_description' => $meta['meta_description'] ?? '',
            'meta_keywords'    => $meta['meta_keywords'] ?? '',
            'created_at'       => $this->now,
            'updated_at'       => $this->now,
        ]);
        $pageId = (int) $this->db->insertID();

        $sort = 10;
        foreach ($blocks as [$type, $label, $settings]) {
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
            $sort += 10;
        }

        $this->db->table('cms_menu_items')->where('page_id IS NULL')->groupStart()
            ->where('url', $slug)->orWhere('url', '/' . $slug)->groupEnd()
            ->update(['page_id' => $pageId, 'url' => null, 'updated_at' => $this->now]);
    }
}
