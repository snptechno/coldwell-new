<?php

namespace App\Database\Seeds;

/**
 * CMS "Selling a Home" page (slug selling-a-home). Same structure as the live Finding a Home page
 * (FindingAHomePageSeeder): media tabs + the automatic video hub, opened on the Selling a Home tab
 * (videos from Admin > Selling Home Videos). Idempotent.
 *
 *   php spark db:seed SellingAHomePageSeeder
 */
class SellingAHomePageSeeder extends CmsPageSeeder
{
    public const SLUG = 'selling-a-home';

    public function run(): void
    {
        $id = $this->createPage(self::SLUG, 'Selling a Home', [
            'nav_key' => 'media center',
            'meta_title' => 'Selling a Home Videos | Coldwell Banker India',
            'meta_description' => 'Guides on pricing, presentation, negotiation and closing — videos to help you sell your home with confidence.',
            'og_image' => self::IMG_HOME . 'living.jpg',
        ]);
        if ($id === null) {
            return;
        }
        $this->block($id, 10, 'page_hero', 'Hero', [
            'eyebrow' => 'Home  ·  Media  ·  Selling a Home', 'title' => 'Sell With Confidence, at the Right Price',
            'lead'    => 'Our specialists extend their personalised service to find the right buyers for your home — and handle everything in between.',
            'cta_label' => 'Get a Free Valuation', 'cta_url' => 'contact-us',
            'image'   => self::IMG_HOME . 'living.jpg', 'image_alt' => 'Bright living room in a premium apartment',
        ]);
        $this->block($id, 20, 'media_tabs', 'Media Tabs', ['active' => 'videos']);
        $this->block($id, 30, 'video_hub', 'Videos', [
            'title' => 'Home Selling Guides', 'lead' => 'Short explainers on each stage of selling a home.',
            'default_tab' => 'Selling a Home', 'more_title' => 'More From CB India', 'per_page' => '6', 'load_label' => 'Load More',
        ]);
        $this->newsletter($id, 40);
        $this->linkMenu($id, 'selling-a-home', 'selling_a_home');
    }
}
