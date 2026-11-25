<?php

namespace App\Database\Seeds;

/**
 * CMS "Testimonials" page (slug testimonials, Figma Testimonials – v2 140:617).
 * Quotes are real client testimonials from franchises_testimonials (lightly trimmed); videos come
 * from the video admin screens via the automatic video hub. Idempotent.
 *
 *   php spark db:seed TestimonialsPageSeeder
 */
class TestimonialsPageSeeder extends CmsPageSeeder
{
    public const SLUG = 'testimonials';

    public function run(): void
    {
        $id = $this->createPage(self::SLUG, 'Testimonials', [
            'nav_key' => 'media center',
            'meta_title' => 'Client Testimonials | Coldwell Banker India',
            'meta_description' => 'Home buyers, sellers and investors on working with Coldwell Banker offices across India.',
        ]);
        if ($id === null) {
            return;
        }
        $this->block($id, 10, 'page_hero', 'Hero', [
            'eyebrow' => 'Home  ·  Media  ·  Testimonials', 'title' => 'In Our Clients’ Words',
            'lead'    => 'Home buyers, sellers and investors on working with Coldwell Banker offices across India.',
            'image'   => self::IMG_HOME . 'family.jpg', 'image_alt' => 'A family receiving the keys to their new home',
        ]);
        $this->block($id, 20, 'media_tabs', 'Media Tabs', ['active' => 'testimonials']);
        $this->block($id, 30, 'testimonials', 'Client stories', [
            'title' => 'Client Stories', 'lead' => 'Real words from clients of our franchise offices.',
        ], ['quote' => [
            ['body' => 'I purchased my home with the help of the Coldwell Banker Gujarat family. My experience while dealing with the team has been great. Their enthusiasm and energy levels are amazing!', 'title' => 'Mr. Nihir Mandowara', 'subtitle' => 'Coldwell Banker KD Properties, Ahmedabad'],
            ['body' => 'I would like to thank you for all your help in finding me my unit. I am extremely happy with my purchase and will not hesitate to recommend your services to anyone in the future.', 'title' => 'Shikha Sharma', 'subtitle' => 'Coldwell Banker Maxim Estate, Navi Mumbai'],
            ['body' => 'We highly recommend Coldwell Banker Anjaneya Spaces to all our friends and family members who wish to buy or sell their property.', 'title' => 'Anna Joseph, Nimesh Shah, Arvind Goswami', 'subtitle' => 'Coldwell Banker Anjaneya Spaces, Mumbai'],
            ['body' => 'I am very thankful to the Coldwell Banker Gujarat team for taking good care of our requirements. We really had a wonderful experience while dealing with them.', 'title' => 'Mr. Nilesh Godhaniya', 'subtitle' => 'Coldwell Banker KD Properties, Ahmedabad'],
            ['body' => 'I am blessed that I got a family like Coldwell Banker. They guided and helped me in all the steps towards buying a home. Keep up the great service!', 'title' => 'Anil Kumar', 'subtitle' => 'Coldwell Banker KD Properties, Ahmedabad'],
            ['body' => 'Team Coldwell Banker Gujarat’s co-ordination with us was highly professional and committed. I appreciate the work of Coldwell Banker.', 'title' => 'Mr. Mitesh Prajapati', 'subtitle' => 'Coldwell Banker KD Properties, Ahmedabad'],
        ]]);
        $this->block($id, 40, 'video_hub', 'Video stories', [
            'title' => 'Success Stories on Video', 'lead' => 'Agents and franchise partners on growing with Coldwell Banker.',
            'default_tab' => 'Testimonials', 'more_title' => 'More From CB India', 'per_page' => '3', 'load_label' => 'Load More',
        ]);
        $this->newsletter($id, 50);
        $this->linkMenu($id, 'testimonials');
    }
}
