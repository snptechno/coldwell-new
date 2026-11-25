<?php

namespace App\Database\Seeds;

/**
 * CMS "Corporate Team" page (slug corporate-team, Figma Corporate Team – v2 132:3).
 * Real leadership photos only (the About page set). Idempotent.
 *
 *   php spark db:seed CorporateTeamPageSeeder
 */
class CorporateTeamPageSeeder extends CmsPageSeeder
{
    public const SLUG = 'corporate-team';

    public function run(): void
    {
        $id = $this->createPage(self::SLUG, 'Corporate Team', [
            'nav_key' => 'corporate team',
            'meta_title' => 'Corporate Team | Coldwell Banker India',
            'meta_description' => 'Meet the leadership team of Coldwell Banker India — the people transforming the Indian real estate sector.',
        ]);
        if ($id === null) {
            return;
        }
        $img = self::IMG_ABOUT;

        $this->block($id, 10, 'page_hero', 'Hero', [
            'eyebrow' => 'Home  ·  About Us  ·  Team', 'title' => 'The People Behind the Brand',
            'lead'    => 'True team builds true passion. The members of Coldwell Banker India are on a mission to transform the Indian real estate sector.',
            'image'   => self::IMG_HOME . 'towers.jpg', 'image_alt' => 'Glass office towers at blue hour',
        ]);
        $this->block($id, 20, 'overview', 'Statement', [
            'eyebrow'   => 'Our leadership',
            'statement' => 'Corporate visionaries and business partners, building strategic ways to enrich your real estate experience — whether you are finding or selling a home.',
        ]);
        $this->block($id, 30, 'leaders', 'Leadership', [
            'title' => 'Leadership Team',
            'lead'  => 'The team guiding Coldwell Banker India’s growth and partner success.',
        ], ['person' => [
            ['title' => 'Ramnik Chopra', 'subtitle' => 'Managing Director', 'body' => '', 'image' => $img . 'leader-ramnik.jpg', 'image_alt' => 'Ramnik Chopra, Managing Director'],
            ['title' => 'Anupam Sinha', 'subtitle' => 'Director', 'image' => $img . 'leader-anupam.jpg', 'image_alt' => 'Anupam Sinha'],
            ['title' => 'Charu D’souza', 'subtitle' => 'HR Head', 'image' => $img . 'leader-charu.jpg', 'image_alt' => 'Charu D’souza'],
            ['title' => 'Mojahid Taj Syed', 'subtitle' => 'Head NRI Sales – India', 'image' => $img . 'leader-syed.jpg', 'image_alt' => 'Mojahid Taj Syed'],
            ['title' => 'Rahul Gaikwad', 'subtitle' => 'Head – International Business Development', 'image' => $img . 'leader-rahul.jpg', 'image_alt' => 'Rahul Gaikwad'],
            ['title' => 'Alok Tiwari', 'subtitle' => 'Business Development', 'image' => $img . 'leader-alok.jpg', 'image_alt' => 'Alok Tiwari'],
            ['title' => 'Diana Joyal', 'subtitle' => 'General Manager – Operations, Strategy, Marketing', 'image' => $img . 'leader-diana.jpg', 'image_alt' => 'Diana Joyal'],
        ]]);
        $this->block($id, 40, 'approach_panel', 'Work with us', [
            'eyebrow'   => 'Work with us',
            'statement' => 'Build the future of Indian real estate with us. We are always looking for people who share our passion for service, integrity and growth.',
            'cta_label' => 'View Openings', 'cta_url' => 'career',
        ]);
        $this->newsletter($id, 50);
        $this->linkMenu($id, 'corporate-team', 'corporate_team');
    }
}
