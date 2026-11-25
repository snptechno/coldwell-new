<?php

namespace App\Database\Seeds;

/**
 * CMS "CB Training Program" page (slug cb-training-program, Figma CB Training Program – v2 145:623).
 * Program content is the old CB-training-program page's. Its 2021 dates are stale, so the date and
 * format figures are placeholders for the client to fill in. Idempotent.
 *
 *   php spark db:seed TrainingPageSeeder
 */
class TrainingPageSeeder extends CmsPageSeeder
{
    public const SLUG = 'cb-training-program';

    public function run(): void
    {
        $id = $this->createPage(self::SLUG, 'CB Training Program', [
            'nav_key' => 'learning development',
            'meta_title' => 'RE Broker Certification Program | Coldwell Banker India',
            'meta_description' => 'A practical certification for real estate brokers: market research, the sales funnel, behavioural marketing, RERA & GST, and strategies to grow your business.',
        ]);
        if ($id === null) {
            return;
        }
        $this->block($id, 10, 'page_hero', 'Hero', [
            'eyebrow' => 'Home  ·  Learning  ·  Broker Certification', 'title' => 'Coldwell Banker RE Broker Certification Program',
            'lead'    => 'Stay aligned with best industry practice in a post-RERA market — a practical certification for real estate brokers.',
            'cta_label' => 'Register Interest', 'cta_url' => '#register',
            'image'   => self::IMG_HOME . 'boardroom.jpg', 'image_alt' => 'Professionals in a glass-walled boardroom',
        ]);
        $this->block($id, 20, 'stats_panel', 'Program facts', [
            'layout' => 'center', 'eyebrow' => 'Program at a glance',
            'body'   => 'A focused, practical program run by Coldwell Banker India for brokers who want to grow with the market.',
        ], ['stat' => [
            ['title' => '[DD–DD Mon]', 'subtitle' => 'Training dates'],
            ['title' => '10 AM – 1 PM', 'subtitle' => 'Time'],
            ['title' => '[Online/Venue]', 'subtitle' => 'Format'],
            ['title' => '7 modules', 'subtitle' => 'Program structure'],
        ]]);
        $this->block($id, 30, 'expertise', 'Why certification', [
            'title' => 'Why Broker Certification',
            'body'  => 'With the changing trends in real estate broking and the introduction of RERA, it has become imperative to stay aligned with the best industry practices and acquire the right skills. Coldwell Banker, one of the oldest real estate companies in the world, has launched this certification to help you understand recent changes and grow your business.',
            'cta_label' => '', 'cta_url' => '',
        ], ['photo' => [
            ['image' => self::IMG_LEARNING . 'training-1.jpg', 'image_alt' => 'Coldwell Banker training session'],
            ['image' => self::IMG_LEARNING . 'training-2.jpg', 'image_alt' => 'Participants at a Coldwell Banker workshop'],
            ['image' => self::IMG_LEARNING . 'training-3.jpg', 'image_alt' => 'Coldwell Banker classroom training'],
        ]]);
        $this->block($id, 40, 'numbered_cards', 'Program structure', [
            'title' => 'Program Structure', 'lead' => 'Seven modules, from market research to RERA and GST.',
        ], ['card' => [
            ['title' => 'Real Estate Scenario in India', 'subtitle' => 'The transition from the unorganised to the organised segment.'],
            ['title' => 'Market Intelligence', 'subtitle' => 'Why research matters, and scientific ways to approach real estate market research.'],
            ['title' => 'The Sales Funnel', 'subtitle' => 'Understanding its stages and conversion ratios.'],
            ['title' => 'Marketing', 'subtitle' => 'From demographic to behavioural targeting — with a live Facebook session.'],
            ['title' => 'Regulation & Reform', 'subtitle' => 'RERA and GST, and how they affect real estate.'],
            ['title' => 'Trends & Case Studies', 'subtitle' => 'Current trends in the Indian real estate sector, with case studies.'],
            ['title' => 'Sales & Marketing Efficiency', 'subtitle' => 'Strategies to enhance sales and marketing efficiency.'],
        ]]);
        $this->block($id, 50, 'values', 'Who can join', [
            'layout' => 'row', 'title' => 'Who Can Join',
        ], ['value' => [
            ['kicker' => 'building', 'title' => 'Practising Brokers', 'subtitle' => 'Real estate broking professionals who want to stay aligned with current market practice.'],
            ['kicker' => 'people', 'title' => 'Aspiring Brokers', 'subtitle' => 'Anyone planning to start a real estate broking business.'],
        ]]);
        $this->block($id, 60, 'approach_panel', 'Register', [
            'eyebrow'   => 'Fulfilling the dream of home since 1906',
            'statement' => 'Seats are limited. Register your interest and our Learning & Development team will confirm the next batch with you.',
            'cta_label' => 'Register Interest', 'cta_url' => 'contact-us',
        ]);
        $this->newsletter($id, 70);
        $this->linkMenu($id, 'cb-training-program', 'CB-training-program');
    }
}
