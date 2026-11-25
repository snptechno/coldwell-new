<?php

namespace App\Database\Seeds;

/**
 * CMS "Site Map" page (slug site-map, Figma Site Map – v2 145:178): navy header (no photo) + the
 * sitemap block. Every link is a live route of this site. Idempotent.
 *
 *   php spark db:seed SiteMapPageSeeder
 */
class SiteMapPageSeeder extends CmsPageSeeder
{
    public const SLUG = 'site-map';

    /** Group => [icon, links "Label | path"] — also used by the tests to check every link resolves. */
    public const GROUPS = [
        'About' => ['people', ['Home | /', 'About Coldwell Banker | about-coldwell-banker', 'Corporate Team | corporate-team', 'Careers | career', 'Franchise Openings | franchisees-openings']],
        'What We Do' => ['building', ['NRI Services | nri', 'Research | research', 'Area Insights | area-insights', 'Learning & Development | learning-development', 'CB Training Program | cb-training-program']],
        'Franchise' => ['handshake', ['Own a Franchise | franchise-details', 'Our Network | our-franchise']],
        'Media Center' => ['star', ['Media Center | media-center', 'Finding a Home | finding-a-home', 'Selling a Home | selling-a-home', 'Testimonials | testimonials', 'Blog | blog']],
        'Help & Legal' => ['shield', ['Contact Us | contact-us', 'FAQ | faq', 'Quick Pay | quick-pay', 'Privacy Policy | privacy-policy', 'Terms & Conditions | terms-and-conditions', 'Disclaimer | disclaimer', 'Refund Policy | refund']],
    ];

    public function run(): void
    {
        $id = $this->createPage(self::SLUG, 'Site Map', [
            'meta_title' => 'Site Map | Coldwell Banker India',
            'meta_description' => 'Every page on the Coldwell Banker India website, in one place.',
        ]);
        if ($id === null) {
            return;
        }
        $this->block($id, 10, 'page_hero', 'Header', [
            'eyebrow' => 'Home  ·  Site Map', 'title' => 'Site Map', 'lead' => 'Every page on coldwellbanker.in, in one place.', 'image' => '',
        ]);
        $items = [];
        foreach (self::GROUPS as $title => [$icon, $links]) {
            $items[] = ['kicker' => $icon, 'title' => $title, 'body' => implode("\n", $links)];
        }
        $this->block($id, 20, 'sitemap', 'Link groups', [], ['group' => $items]);
        $this->newsletter($id, 30);
        $this->linkMenu($id, 'site-map', 'site_map');
    }
}
