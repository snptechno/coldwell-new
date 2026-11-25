<?php

namespace App\Libraries\Cms;

/**
 * Registry of CMS block types. One entry drives the admin forms, validation and the
 * public partial (app/Views/cms/blocks/<type>.php).
 *
 * Field definition: ['label' => string, 'type' => text|textarea|url|image|html|number|select,
 *                    'required' => bool, 'max' => int, 'hint' => string, 'options' => [value => label], 'default' => mixed]
 *
 * - `settings`  block-level fields, stored as JSON in cms_blocks.settings.
 * - `groups`    repeatable item groups, stored in cms_block_items (item_group = key). Item fields
 *               must be columns of cms_block_items: kicker, badge, title, subtitle, body,
 *               cta_label, cta_url, image, image_alt.
 * - `hero`      true when the block renders the site header inside itself (must be first).
 */
final class BlockTypes
{
    /** Icon choices for icon-bearing items (keys of cb_icon()). */
    public const ICONS = [
        'pin' => 'Location pin', 'building' => 'Office building', 'people' => 'People', 'shield' => 'Shield (trust)',
        'bulb' => 'Light bulb (innovation)', 'handshake' => 'Handshake (collaboration)', 'chart' => 'Chart (business)',
        'heart' => 'Heart (customer)', 'globe' => 'Globe', 'award' => 'Award', 'star' => 'Star',
        'mail' => 'Envelope (email)', 'phone' => 'Phone', 'clock' => 'Clock',
        'home' => 'House (buy)', 'tag' => 'Price tag (sell)', 'key' => 'Key (lease / manage)',
    ];

    private const LINK_HINT = 'Site page path (e.g. contact-us) or a full https:// link.';

    public const TYPES = [
        'hero_slider' => [
            'label' => 'Hero slider (full)',
            'help'  => 'Full-height photo hero with slides and stat chips; holds the site menu. Use as the first block of the home page.',
            'hero'  => true,
            'settings' => [
                'video_label' => ['label' => 'Video link label', 'type' => 'text', 'max' => 40, 'hint' => 'Leave empty to hide the "Watch our story" link.'],
                'video_url'   => ['label' => 'Video link', 'type' => 'url', 'max' => 255],
            ],
            'groups' => [
                'slide' => ['label' => 'Slides', 'max' => 5, 'min' => 1, 'fields' => [
                    'kicker'    => ['label' => 'Eyebrow', 'type' => 'text', 'max' => 120, 'hint' => 'Use " · " to split; the part after it is hidden on phones.'],
                    'title'     => ['label' => 'Headline', 'type' => 'textarea', 'required' => true, 'max' => 255, 'hint' => 'A line break starts a new line.'],
                    'body'      => ['label' => 'Lead text', 'type' => 'textarea', 'max' => 400],
                    'cta_label' => ['label' => 'Button label', 'type' => 'text', 'max' => 60],
                    'cta_url'   => ['label' => 'Button link', 'type' => 'url', 'max' => 255],
                    'image'     => ['label' => 'Background photo', 'type' => 'image', 'required' => true, 'hint' => 'Landscape, at least 1920×1080; dusk / blue-hour works best under white text.'],
                    'image_alt' => ['label' => 'Photo description (alt)', 'type' => 'text', 'max' => 255],
                ]],
                'stat' => ['label' => 'Stat chips', 'max' => 4, 'fields' => [
                    'title'    => ['label' => 'Figure', 'type' => 'text', 'required' => true, 'max' => 20, 'hint' => 'Only publish verified figures.'],
                    'subtitle' => ['label' => 'Label', 'type' => 'text', 'required' => true, 'max' => 60],
                ]],
            ],
        ],
        'page_hero' => [
            'label' => 'Page hero (compact)',
            'help'  => 'Shorter photo hero for inner pages; holds the site menu. Use as the first block. Without a photo it becomes a short navy header (legal pages, FAQ, site map).',
            'hero'  => true,
            'settings' => [
                'eyebrow'   => ['label' => 'Eyebrow / breadcrumb', 'type' => 'text', 'max' => 120],
                'title'     => ['label' => 'Headline', 'type' => 'textarea', 'required' => true, 'max' => 200],
                'lead'      => ['label' => 'Lead text', 'type' => 'textarea', 'max' => 400],
                'cta_label' => ['label' => 'Button label', 'type' => 'text', 'max' => 60],
                'cta_url'   => ['label' => 'Button link', 'type' => 'url', 'max' => 255],
                'image'     => ['label' => 'Background photo (optional)', 'type' => 'image', 'hint' => 'Leave empty for the short navy header used on legal and help pages.'],
                'image_alt' => ['label' => 'Photo description (alt)', 'type' => 'text', 'max' => 255],
            ],
            'groups' => [],
        ],
        'mission' => [
            'label' => 'Statement + photo strip',
            'help'  => 'Centred eyebrow, statement, a strip of up to five photos and a button.',
            'settings' => [
                'eyebrow'   => ['label' => 'Eyebrow', 'type' => 'text', 'max' => 60],
                'statement' => ['label' => 'Statement', 'type' => 'textarea', 'required' => true, 'max' => 400],
                'cta_label' => ['label' => 'Button label', 'type' => 'text', 'max' => 60],
                'cta_url'   => ['label' => 'Button link', 'type' => 'url', 'max' => 255],
            ],
            'groups' => [
                'photo' => ['label' => 'Photos', 'max' => 5, 'fields' => [
                    'image'     => ['label' => 'Photo', 'type' => 'image', 'required' => true],
                    'image_alt' => ['label' => 'Photo description (alt)', 'type' => 'text', 'max' => 255],
                ]],
            ],
        ],
        'city_slider' => [
            'label' => 'City slider with tabs',
            'help'  => 'Heading, city tabs and portrait city cards (3 visible on desktop, swipe on phones).',
            'settings' => [
                'title' => ['label' => 'Heading', 'type' => 'text', 'required' => true, 'max' => 120],
                'lead'  => ['label' => 'Lead text', 'type' => 'textarea', 'max' => 300],
            ],
            'groups' => [
                'city' => ['label' => 'Cities', 'max' => 12, 'fields' => [
                    'title'     => ['label' => 'City', 'type' => 'text', 'required' => true, 'max' => 60],
                    'subtitle'  => ['label' => 'Tagline', 'type' => 'text', 'max' => 120],
                    'body'      => ['label' => 'Areas covered', 'type' => 'text', 'max' => 255],
                    'cta_url'   => ['label' => 'Card link', 'type' => 'url', 'max' => 255],
                    'image'     => ['label' => 'Portrait photo', 'type' => 'image', 'required' => true, 'hint' => 'Portrait, about 900×1200.'],
                    'image_alt' => ['label' => 'Photo description (alt)', 'type' => 'text', 'max' => 255],
                ]],
            ],
        ],
        'card_grid' => [
            'label' => 'Card panel (3-up, swipe on phones)',
            'help'  => 'Soft panel with heading and photo cards with badge, audience, title, highlights and a button.',
            'settings' => [
                'title' => ['label' => 'Heading', 'type' => 'text', 'required' => true, 'max' => 120],
                'lead'  => ['label' => 'Lead text', 'type' => 'textarea', 'max' => 300],
            ],
            'groups' => [
                'card' => ['label' => 'Cards', 'max' => 9, 'fields' => [
                    'badge'     => ['label' => 'Photo badge', 'type' => 'text', 'max' => 60],
                    'kicker'    => ['label' => 'Audience', 'type' => 'text', 'max' => 120],
                    'title'     => ['label' => 'Title', 'type' => 'text', 'required' => true, 'max' => 120],
                    'subtitle'  => ['label' => 'Highlights', 'type' => 'text', 'max' => 120],
                    'cta_label' => ['label' => 'Button label', 'type' => 'text', 'max' => 60],
                    'cta_url'   => ['label' => 'Button link', 'type' => 'url', 'max' => 255],
                    'image'     => ['label' => 'Photo', 'type' => 'image', 'required' => true],
                    'image_alt' => ['label' => 'Photo description (alt)', 'type' => 'text', 'max' => 255],
                ]],
            ],
        ],
        'expertise' => [
            'label' => 'Photo stack + text',
            'help'  => 'Three overlapping photos beside a heading, text, milestone chips and a button.',
            'settings' => [
                'logo'      => ['label' => 'Logo above heading (optional)', 'type' => 'image', 'hint' => 'Shown in a white chip, e.g. the Coldwell Banker University logo.'],
                'title'     => ['label' => 'Heading', 'type' => 'textarea', 'required' => true, 'max' => 160],
                'quote'     => ['label' => 'Quote (optional)', 'type' => 'textarea', 'max' => 400, 'hint' => 'Shown with a gold rule before the text.'],
                'body'      => ['label' => 'Text', 'type' => 'textarea', 'max' => 600],
                'cta_label' => ['label' => 'Button label', 'type' => 'text', 'max' => 60],
                'cta_url'   => ['label' => 'Button link', 'type' => 'url', 'max' => 255],
                'cta_tone'  => ['label' => 'Button style', 'type' => 'select', 'options' => ['gold' => 'Gold (primary)', 'light' => 'Light (secondary)'], 'default' => 'gold'],
            ],
            'groups' => [
                'photo' => ['label' => 'Stacked photos', 'max' => 3, 'fields' => [
                    'image'     => ['label' => 'Photo', 'type' => 'image', 'required' => true, 'hint' => 'Portrait, about 440×600. The last photo is in front.'],
                    'image_alt' => ['label' => 'Photo description (alt)', 'type' => 'text', 'max' => 255],
                ]],
                'milestone' => ['label' => 'Milestone chips', 'max' => 6, 'fields' => [
                    'title'    => ['label' => 'Year / tag', 'type' => 'text', 'required' => true, 'max' => 20],
                    'subtitle' => ['label' => 'Text', 'type' => 'text', 'required' => true, 'max' => 80],
                ]],
            ],
        ],
        'newsroom' => [
            'label' => 'Newsroom (automatic)',
            'help'  => 'Latest Research, CB India News and CB India Video, filled automatically from those modules.',
            'settings' => [
                'title'     => ['label' => 'Heading', 'type' => 'text', 'required' => true, 'max' => 120],
                'lead'      => ['label' => 'Lead text', 'type' => 'textarea', 'max' => 300],
                'cta_label' => ['label' => 'Button label', 'type' => 'text', 'max' => 60],
                'cta_url'   => ['label' => 'Button link', 'type' => 'url', 'max' => 255],
            ],
            'groups' => [],
        ],
        'stats_panel' => [
            'label' => 'Statement / intro + figures',
            'help'  => '"Centred": eyebrow, large statement and a row of figures (About › Our Purpose). "Split": soft panel with heading, text and button on the left and figure cards with icons on the right (About › Coldwell Banker India).',
            'settings' => [
                'layout'    => ['label' => 'Layout', 'type' => 'select', 'options' => ['center' => 'Centred statement + figure row', 'split' => 'Split panel + figure cards'], 'default' => 'center'],
                'eyebrow'   => ['label' => 'Eyebrow', 'type' => 'text', 'max' => 60],
                'title'     => ['label' => 'Heading', 'type' => 'text', 'max' => 120, 'hint' => 'Used by the split layout.'],
                'body'      => ['label' => 'Statement / text', 'type' => 'textarea', 'required' => true, 'max' => 800],
                'cta_label' => ['label' => 'Button label', 'type' => 'text', 'max' => 60],
                'cta_url'   => ['label' => 'Button link', 'type' => 'url', 'max' => 255],
            ],
            'groups' => [
                'stat' => ['label' => 'Figures', 'max' => 4, 'fields' => [
                    'title'    => ['label' => 'Figure', 'type' => 'text', 'required' => true, 'max' => 20, 'hint' => 'Only publish verified figures. {offices} and {cities} show live counts of open franchise offices.'],
                    'subtitle' => ['label' => 'Label', 'type' => 'text', 'required' => true, 'max' => 60],
                    'kicker'   => ['label' => 'Icon (split layout)', 'type' => 'select', 'options' => self::ICONS, 'default' => 'pin'],
                ]],
            ],
        ],
        'leaders' => [
            'label' => 'Leadership',
            'help'  => 'Heading row with a button, a featured leader (the first person, wide photo on navy) and a 3-column grid of the others.',
            'settings' => [
                'title'     => ['label' => 'Heading', 'type' => 'text', 'required' => true, 'max' => 120],
                'lead'      => ['label' => 'Lead text', 'type' => 'textarea', 'max' => 300],
                'cta_label' => ['label' => 'Button label', 'type' => 'text', 'max' => 60],
                'cta_url'   => ['label' => 'Button link', 'type' => 'url', 'max' => 255],
            ],
            'groups' => [
                'person' => ['label' => 'People', 'max' => 16, 'fields' => [
                    'title'     => ['label' => 'Name', 'type' => 'text', 'required' => true, 'max' => 80],
                    'subtitle'  => ['label' => 'Role', 'type' => 'text', 'required' => true, 'max' => 120, 'hint' => 'The first person is featured; their role shows as the gold eyebrow.'],
                    'body'      => ['label' => 'Short bio (featured person only)', 'type' => 'textarea', 'max' => 500],
                    'image'     => ['label' => 'Photo', 'type' => 'image', 'required' => true, 'hint' => 'Real photo only. Featured: landscape ~1400×680; others ~770×600.'],
                    'image_alt' => ['label' => 'Photo description (alt)', 'type' => 'text', 'max' => 255],
                ]],
            ],
        ],
        'feature_cards' => [
            'label' => 'Feature cards on navy panel',
            'help'  => 'Navy panel with centred heading and 2-column glass cards. A card can show a logo; lines in its text that start with "- " become a gold checklist.',
            'settings' => [
                'title' => ['label' => 'Heading', 'type' => 'text', 'required' => true, 'max' => 120],
                'lead'  => ['label' => 'Lead text', 'type' => 'textarea', 'max' => 300],
            ],
            'groups' => [
                'card' => ['label' => 'Cards', 'max' => 8, 'fields' => [
                    'title'     => ['label' => 'Title', 'type' => 'text', 'required' => true, 'max' => 80],
                    'body'      => ['label' => 'Text', 'type' => 'textarea', 'required' => true, 'max' => 1200, 'hint' => 'Start a line with "- " to make it a checklist item.'],
                    'image'     => ['label' => 'Logo (optional)', 'type' => 'image', 'hint' => 'Shown in a white chip. PNG/JPG, about 300×110.'],
                    'image_alt' => ['label' => 'Logo description (alt)', 'type' => 'text', 'max' => 255],
                ]],
            ],
        ],
        'values' => [
            'label' => 'Icon cards (values)',
            'help'  => 'Centred heading and white icon cards. "Row" fits up to 5 per row (About > Our Values); "3-column grid" highlights the first card in navy (Franchise > Why partner with us). A card\'s checklist lines become gold ticks (NRI > Services for Every Step).',
            'settings' => [
                'layout'  => ['label' => 'Layout', 'type' => 'select', 'options' => ['row' => 'Row (up to 5)', 'grid3' => '3-column grid, first card highlighted', 'grid4' => '4-column grid, first card highlighted'], 'default' => 'row'],
                'background' => ['label' => 'Background', 'type' => 'select', 'options' => ['white' => 'White', 'panel' => 'Soft panel'], 'default' => 'white'],
                'eyebrow' => ['label' => 'Eyebrow', 'type' => 'text', 'max' => 60],
                'title' => ['label' => 'Heading', 'type' => 'text', 'required' => true, 'max' => 120],
                'lead'  => ['label' => 'Lead text', 'type' => 'textarea', 'max' => 300],
            ],
            'groups' => [
                'value' => ['label' => 'Cards', 'max' => 12, 'fields' => [
                    'kicker'   => ['label' => 'Icon', 'type' => 'select', 'options' => self::ICONS, 'default' => 'shield'],
                    'title'    => ['label' => 'Title', 'type' => 'text', 'required' => true, 'max' => 60],
                    'subtitle' => ['label' => 'Text', 'type' => 'text', 'max' => 160],
                    'body'     => ['label' => 'Checklist (optional, one item per line)', 'type' => 'textarea', 'max' => 800, 'hint' => 'Up to 10 lines; each line becomes a gold tick item under the text.'],
                    'cta_label' => ['label' => 'Button label (optional)', 'type' => 'text', 'max' => 40],
                    'cta_url'   => ['label' => 'Button link', 'type' => 'url', 'max' => 255],
                ]],
            ],
        ],
        'steps' => [
            'label' => 'Numbered steps (process)',
            'help'  => 'Soft panel with centred heading and numbered steps joined by a line; the last step is gold. Use "Plain" right after another soft panel.',
            'settings' => [
                'style' => ['label' => 'Background', 'type' => 'select', 'options' => ['panel' => 'Soft panel', 'plain' => 'Plain (white)'], 'default' => 'panel'],
                'title' => ['label' => 'Heading', 'type' => 'text', 'required' => true, 'max' => 120],
                'lead'  => ['label' => 'Lead text', 'type' => 'textarea', 'max' => 300],
            ],
            'groups' => [
                'step' => ['label' => 'Steps', 'max' => 6, 'fields' => [
                    'title'    => ['label' => 'Step title', 'type' => 'text', 'required' => true, 'max' => 60],
                    'subtitle' => ['label' => 'Text', 'type' => 'text', 'max' => 160],
                ]],
            ],
        ],
        'office_finder' => [
            'label' => 'Franchise office finder (automatic)',
            'help'  => 'Office cards filled automatically from Franchises (open offices only), opening on the city with the most offices. "Finder" adds city / area selects (Franchise page); "Directory" shows city tabs with office counts and phone numbers (Contact page). {offices} and {cities} in the lead text are replaced with live counts.',
            'settings' => [
                'layout'    => ['label' => 'Layout', 'type' => 'select', 'options' => ['finder' => 'Finder (selects + cards)', 'directory' => 'Directory (tabs with counts + phones)'], 'default' => 'finder'],
                'title'     => ['label' => 'Heading', 'type' => 'text', 'required' => true, 'max' => 120],
                'lead'      => ['label' => 'Lead text', 'type' => 'textarea', 'max' => 300],
                'cta_label' => ['label' => 'Button label (directory)', 'type' => 'text', 'max' => 40],
                'cta_url'   => ['label' => 'Button link', 'type' => 'url', 'max' => 255],
            ],
            'groups' => [],
        ],
        'testimonials' => [
            'label' => 'Testimonial cards',
            'help'  => 'Soft panel with heading and 2-column quote cards. Only publish real, approved quotes.',
            'settings' => [
                'title' => ['label' => 'Heading', 'type' => 'text', 'required' => true, 'max' => 120],
                'lead'  => ['label' => 'Lead text', 'type' => 'textarea', 'max' => 300],
            ],
            'groups' => [
                'quote' => ['label' => 'Quotes', 'max' => 8, 'fields' => [
                    'body'      => ['label' => 'Quote', 'type' => 'textarea', 'required' => true, 'max' => 600],
                    'title'     => ['label' => 'Name', 'type' => 'text', 'required' => true, 'max' => 80],
                    'subtitle'  => ['label' => 'Franchise, city', 'type' => 'text', 'max' => 120],
                    'image'     => ['label' => 'Photo (optional)', 'type' => 'image', 'hint' => 'Square, about 200x200. Real photo only.'],
                    'image_alt' => ['label' => 'Photo description (alt)', 'type' => 'text', 'max' => 255],
                ]],
            ],
        ],
        'faq' => [
            'label' => 'FAQ accordion',
            'help'  => 'Heading and text on the left, expandable questions on the right. The first question opens by default.',
            'settings' => [
                'title' => ['label' => 'Heading', 'type' => 'textarea', 'required' => true, 'max' => 120],
                'lead'  => ['label' => 'Lead text', 'type' => 'textarea', 'max' => 300],
            ],
            'groups' => [
                'qa' => ['label' => 'Questions', 'max' => 20, 'fields' => [
                    'title' => ['label' => 'Question', 'type' => 'text', 'required' => true, 'max' => 200],
                    'body'  => ['label' => 'Answer', 'type' => 'textarea', 'required' => true, 'max' => 2000],
                ]],
            ],
        ],
        'enquiry_form' => [
            'label' => 'Request a call back (franchise enquiry form)',
            'help'  => 'Navy panel with text and phone on the left and the franchise enquiry form on the right. Submissions go to Franchisee Enquiry and email the franchise team. Link to it with #callback.',
            'settings' => [
                'eyebrow' => ['label' => 'Eyebrow', 'type' => 'text', 'max' => 60],
                'title'   => ['label' => 'Heading', 'type' => 'text', 'required' => true, 'max' => 120],
                'lead'    => ['label' => 'Lead text', 'type' => 'textarea', 'max' => 400],
                'phone'   => ['label' => 'Phone number', 'type' => 'text', 'max' => 40, 'hint' => 'Leave empty to use the footer phone from Site settings.'],
                'button'  => ['label' => 'Button label', 'type' => 'text', 'max' => 40, 'default' => 'Send Request'],
            ],
            'groups' => [],
        ],
        'legal_text' => [
            'label' => 'Legal / policy text with contents',
            'help'  => 'Numbered clauses in a reading column with an "On this page" list on the left and a help card at the end (Privacy, Terms, Disclaimer, Refund). In a clause, a blank line starts a new paragraph and lines starting with "- " become bullets.',
            'settings' => [
                'updated'    => ['label' => 'Last updated', 'type' => 'text', 'max' => 40, 'hint' => 'e.g. 1 Dec 2026. Leave empty to hide.'],
                'help_title' => ['label' => 'Help card heading', 'type' => 'text', 'max' => 80, 'default' => 'Questions about this page?'],
                'help_text'  => ['label' => 'Help card text', 'type' => 'text', 'max' => 160],
                'cta_label'  => ['label' => 'Help card button', 'type' => 'text', 'max' => 40, 'default' => 'Contact Us'],
                'cta_url'    => ['label' => 'Help card link', 'type' => 'url', 'max' => 255, 'default' => 'contact-us'],
            ],
            'groups' => [
                'clause' => ['label' => 'Clauses', 'max' => 30, 'min' => 1, 'fields' => [
                    'title' => ['label' => 'Heading', 'type' => 'text', 'required' => true, 'max' => 120],
                    'body'  => ['label' => 'Text', 'type' => 'textarea', 'required' => true, 'max' => 4000],
                ]],
            ],
        ],
        'sitemap' => [
            'label' => 'Site map (link groups)',
            'help'  => 'Cards of links, 3 per row; the first card is navy. One link per line as "Label | page-path" (or a full https:// link).',
            'settings' => [],
            'groups' => [
                'group' => ['label' => 'Link groups', 'max' => 9, 'min' => 1, 'fields' => [
                    'kicker' => ['label' => 'Icon', 'type' => 'select', 'options' => self::ICONS, 'default' => 'globe'],
                    'title'  => ['label' => 'Group name', 'type' => 'text', 'required' => true, 'max' => 60],
                    'body'   => ['label' => 'Links (Label | path, one per line)', 'type' => 'textarea', 'required' => true, 'max' => 2000],
                ]],
            ],
        ],
        'nri_enquiry_form' => [
            'label' => 'NRI desk enquiry form',
            'help'  => 'Navy panel with text and phone on the left and the NRI enquiry form on the right (name, email, phone, country, interest, preferred city). Submissions go to NRI Enquiry and email the NRI desk. Link to it with #nri-enquiry.',
            'settings' => [
                'eyebrow' => ['label' => 'Eyebrow', 'type' => 'text', 'max' => 60],
                'title'   => ['label' => 'Heading', 'type' => 'text', 'required' => true, 'max' => 120],
                'lead'    => ['label' => 'Lead text', 'type' => 'textarea', 'max' => 400],
                'phone'   => ['label' => 'Phone number', 'type' => 'text', 'max' => 40, 'hint' => 'Leave empty to use the footer phone from Site settings.'],
                'button'  => ['label' => 'Button label', 'type' => 'text', 'max' => 40, 'default' => 'Send Request'],
            ],
            'groups' => [],
        ],
        'contact_panel' => [
            'label' => 'Contact channels + message form',
            'help'  => 'Heading and contact cards on the left, the contact message form on the right (saved to Contact Enquiry and emailed to info@). Link to the form with #contact-form.',
            'settings' => [
                'title'      => ['label' => 'Heading', 'type' => 'text', 'required' => true, 'max' => 120],
                'lead'       => ['label' => 'Lead text', 'type' => 'textarea', 'max' => 300],
                'form_title' => ['label' => 'Form heading', 'type' => 'text', 'max' => 80, 'default' => 'Send Us a Message'],
                'form_lead'  => ['label' => 'Form text', 'type' => 'text', 'max' => 160],
                'button'     => ['label' => 'Button label', 'type' => 'text', 'max' => 40, 'default' => 'Submit Enquiry'],
            ],
            'groups' => [
                'channel' => ['label' => 'Contact cards', 'max' => 6, 'fields' => [
                    'kicker'  => ['label' => 'Icon', 'type' => 'select', 'options' => self::ICONS, 'default' => 'pin'],
                    'title'   => ['label' => 'Title', 'type' => 'text', 'required' => true, 'max' => 60],
                    'body'    => ['label' => 'Text', 'type' => 'textarea', 'required' => true, 'max' => 300],
                    'cta_url' => ['label' => 'Link (optional)', 'type' => 'url', 'max' => 255, 'hint' => 'mailto:, tel:, a site page or https:// link. The whole card becomes clickable.'],
                ]],
            ],
        ],
        'location' => [
            'label' => 'Office location (map + address)',
            'help'  => 'Soft panel with a map on the left and the address, hours and a directions button on the right. Without a map embed link a styled map placeholder with a pin is shown.',
            'settings' => [
                'eyebrow'   => ['label' => 'Map label', 'type' => 'text', 'max' => 80],
                'title'     => ['label' => 'Heading', 'type' => 'text', 'required' => true, 'max' => 120],
                'address'   => ['label' => 'Address', 'type' => 'textarea', 'required' => true, 'max' => 300],
                'hours'     => ['label' => 'Opening hours', 'type' => 'text', 'max' => 80, 'hint' => 'Leave empty until confirmed.'],
                'cta_label' => ['label' => 'Button label', 'type' => 'text', 'max' => 40, 'default' => 'Get Directions'],
                'map_embed' => ['label' => 'Google Maps embed link', 'type' => 'url', 'max' => 600, 'hint' => 'The src of a Google Maps "Embed a map" iframe (https://www.google.com/maps/embed?pb=...). Other links are ignored.'],
            ],
            'groups' => [],
        ],
        'featured_news' => [
            'label' => 'Featured news story (automatic)',
            'help'  => 'Large photo + story from CB India News. Leave the news ID empty for the latest item, or enter the ID of a news row (Admin > CB India News) to pin it.',
            'settings' => [
                'eyebrow'   => ['label' => 'Badge', 'type' => 'text', 'max' => 60, 'default' => 'Featured · CB India News'],
                'news_id'   => ['label' => 'News ID to pin (optional)', 'type' => 'number', 'max' => 10],
                'cta_label' => ['label' => 'Button label', 'type' => 'text', 'max' => 40, 'default' => 'Read Article'],
                'image'     => ['label' => 'Photo', 'type' => 'image', 'hint' => 'Library photo; publisher screenshots are not used.'],
                'image_alt' => ['label' => 'Photo description (alt)', 'type' => 'text', 'max' => 255],
            ],
            'groups' => [],
        ],
        'news_feed' => [
            'label' => 'News feed with filter tabs (automatic)',
            'help'  => 'Newest items from CB India News, Industry News, Awards, Research, Videos and Newsletter, with filter tabs and Load More.',
            'settings' => [
                'title'      => ['label' => 'Heading', 'type' => 'text', 'required' => true, 'max' => 120],
                'per_source' => ['label' => 'Items per source', 'type' => 'number', 'max' => 3, 'default' => '12', 'hint' => '1-30.'],
                'page_size'  => ['label' => 'Cards shown before "Load More"', 'type' => 'number', 'max' => 2, 'default' => '6'],
            ],
            'groups' => [],
        ],
        'awards_list' => [
            'label' => 'Awards & recognition (automatic)',
            'help'  => 'Soft panel: photo of the latest award with a photo, and the newest awards list (Admin > Awards).',
            'settings' => [
                'title'     => ['label' => 'Heading', 'type' => 'text', 'required' => true, 'max' => 120],
                'lead'      => ['label' => 'Lead text', 'type' => 'textarea', 'max' => 300],
                'limit'     => ['label' => 'Awards listed', 'type' => 'number', 'max' => 2, 'default' => '5'],
                'cta_label' => ['label' => 'Button label', 'type' => 'text', 'max' => 40],
                'cta_url'   => ['label' => 'Button link', 'type' => 'url', 'max' => 255],
            ],
            'groups' => [],
        ],
        'video_gallery' => [
            'label' => 'Video gallery with tabs (automatic)',
            'help'  => 'Featured video + list per category (CB India, Featured, Finding a Home, Selling a Home, Testimonials), from the video admin screens. Opens YouTube.',
            'settings' => [
                'title'     => ['label' => 'Heading', 'type' => 'text', 'required' => true, 'max' => 120],
                'lead'      => ['label' => 'Lead text', 'type' => 'textarea', 'max' => 300],
                'cta_label' => ['label' => 'Button label', 'type' => 'text', 'max' => 40],
                'cta_url'   => ['label' => 'Button link', 'type' => 'url', 'max' => 255],
            ],
            'groups' => [],
        ],
        'archive_lists' => [
            'label' => 'Research + newsletter archive (automatic)',
            'help'  => 'Two cards: newest research reports (PDF) and newsletter editions.',
            'settings' => [
                'left_title'  => ['label' => 'Research heading', 'type' => 'text', 'max' => 80, 'default' => 'Research & Reports'],
                'left_lead'   => ['label' => 'Research text', 'type' => 'text', 'max' => 160],
                'right_title' => ['label' => 'Newsletter heading', 'type' => 'text', 'max' => 80, 'default' => 'Newsletter Archive'],
                'right_lead'  => ['label' => 'Newsletter text', 'type' => 'text', 'max' => 160],
                'limit'       => ['label' => 'Items per list', 'type' => 'number', 'max' => 2, 'default' => '4'],
            ],
            'groups' => [],
        ],
        'overview' => [
            'label' => 'Overview statement + audience chips',
            'help'  => 'Centred eyebrow, large statement, supporting text and a row of chips (one per line).',
            'settings' => [
                'eyebrow'   => ['label' => 'Eyebrow', 'type' => 'text', 'max' => 60],
                'statement' => ['label' => 'Statement', 'type' => 'textarea', 'required' => true, 'max' => 400],
                'body'      => ['label' => 'Text', 'type' => 'textarea', 'max' => 800],
                'chips'     => ['label' => 'Chips (one per line)', 'type' => 'textarea', 'max' => 400],
            ],
            'groups' => [],
        ],
        'numbered_cards' => [
            'label' => 'Numbered cards (programs)',
            'help'  => 'Soft panel with centred heading and numbered white cards (3 per row); the first number is gold. Tags: comma-separated.',
            'settings' => [
                'title' => ['label' => 'Heading', 'type' => 'text', 'required' => true, 'max' => 120],
                'lead'  => ['label' => 'Lead text', 'type' => 'textarea', 'max' => 300],
            ],
            'groups' => [
                'card' => ['label' => 'Cards', 'max' => 12, 'fields' => [
                    'title'    => ['label' => 'Title', 'type' => 'text', 'required' => true, 'max' => 80],
                    'subtitle' => ['label' => 'Text', 'type' => 'text', 'max' => 200],
                    'kicker'   => ['label' => 'Tags (comma-separated)', 'type' => 'text', 'max' => 120],
                ]],
            ],
        ],
        'event_calendar' => [
            'label' => 'Training calendar (automatic)',
            'help'  => 'Navy panel listing UPCOMING training events (Admin > Learning > Events). Past events are never shown; with none upcoming, the "no batches" text and button show instead.',
            'settings' => [
                'title'       => ['label' => 'Heading', 'type' => 'text', 'required' => true, 'max' => 120],
                'lead'        => ['label' => 'Lead text', 'type' => 'textarea', 'max' => 300],
                'limit'       => ['label' => 'Events shown', 'type' => 'number', 'max' => 2, 'default' => '5'],
                'cta_label'   => ['label' => 'Button label', 'type' => 'text', 'max' => 40, 'default' => 'Register Interest'],
                'cta_url'     => ['label' => 'Button link', 'type' => 'url', 'max' => 255],
                'empty_title' => ['label' => 'Text when no batch is scheduled', 'type' => 'text', 'max' => 160],
                'empty_lead'  => ['label' => 'Supporting text when none', 'type' => 'textarea', 'max' => 300],
            ],
            'groups' => [],
        ],
        'album_gallery' => [
            'label' => 'Photo albums (automatic)',
            'help'  => 'Album bento from Learning > Gallery: newest album large, next four in a grid. Each album opens its photos in a viewer.',
            'settings' => [
                'title'     => ['label' => 'Heading', 'type' => 'text', 'required' => true, 'max' => 120],
                'lead'      => ['label' => 'Lead text', 'type' => 'textarea', 'max' => 300],
                'cta_label' => ['label' => 'Button label', 'type' => 'text', 'max' => 40],
                'cta_url'   => ['label' => 'Button link', 'type' => 'url', 'max' => 255],
            ],
            'groups' => [],
        ],
        'job_openings' => [
            'label' => 'Current job openings (automatic)',
            'help'  => 'Open positions from Admin > Job Openings, in tabs by type with counts. Rows expand to show requirements and key duties; Apply jumps to the application form with the role selected.',
            'settings' => [
                'title' => ['label' => 'Heading', 'type' => 'text', 'required' => true, 'max' => 120],
                'lead'  => ['label' => 'Lead text', 'type' => 'textarea', 'max' => 300],
                'empty' => ['label' => 'Text when there are no openings', 'type' => 'text', 'max' => 200],
            ],
            'groups' => [],
        ],
        'job_apply_form' => [
            'label' => 'Job application form',
            'help'  => 'Navy panel with the job application form (position, name, gender, mobile, email, address, résumé upload). Saved to Job Applications and emailed to HR. Link to it with #apply.',
            'settings' => [
                'eyebrow'  => ['label' => 'Eyebrow', 'type' => 'text', 'max' => 60],
                'title'    => ['label' => 'Heading', 'type' => 'text', 'required' => true, 'max' => 120],
                'lead'     => ['label' => 'Lead text', 'type' => 'textarea', 'max' => 400],
                'cv_email' => ['label' => 'Careers email for CVs', 'type' => 'text', 'max' => 120, 'hint' => 'Shown as "No matching role? Send your CV to …". Leave empty to hide.'],
                'button'   => ['label' => 'Button label', 'type' => 'text', 'max' => 40, 'default' => 'Submit Application'],
            ],
            'groups' => [],
        ],
        'rich_text' => [
            'label' => 'Text (rich text)',
            'help'  => 'Heading and formatted text in the content column.',
            'settings' => [
                'eyebrow' => ['label' => 'Eyebrow', 'type' => 'text', 'max' => 60],
                'title'   => ['label' => 'Heading', 'type' => 'text', 'max' => 160],
                'body'    => ['label' => 'Text', 'type' => 'html', 'required' => true, 'max' => 60000],
                'align'   => ['label' => 'Alignment', 'type' => 'select', 'options' => ['left' => 'Left', 'center' => 'Centred'], 'default' => 'left'],
            ],
            'groups' => [],
        ],
        'newsletter_cta' => [
            'label' => 'Newsletter band',
            'help'  => 'Full-width photo band with the email signup. Goes directly above the footer.',
            'settings' => [
                'title'     => ['label' => 'Heading', 'type' => 'textarea', 'required' => true, 'max' => 160],
                'lead'      => ['label' => 'Lead text', 'type' => 'textarea', 'max' => 300],
                'image'     => ['label' => 'Background photo', 'type' => 'image'],
            ],
            'groups' => [],
        ],
        'research_tabs' => [
            'label' => 'Research tabs',
            'help'  => 'Filter tabs for Research: Project Reports and Area Insights. Controls which list renders below.',
            'settings' => [
                'active' => ['label' => 'Active tab', 'type' => 'select', 'options' => ['project' => 'Project Reports', 'area' => 'Area Insights'], 'default' => 'project'],
            ],
            'groups' => [],
        ],
        'featured_report' => [
            'label' => 'Featured research report',
            'help'  => 'Large featured card: full-height image on the left, meta badge, title, summary and button on the right. Data comes from the Research admin (type = Project Reports, is_active = 1, newest first).',
            'settings' => [
                'title'     => ['label' => 'Section heading', 'type' => 'text', 'required' => true, 'max' => 120, 'default' => 'Latest Project Report'],
                'limit'     => ['label' => 'Max items to show', 'type' => 'number', 'max' => 2, 'default' => '1'],
                'cta_label' => ['label' => 'Button label', 'type' => 'text', 'max' => 60, 'default' => 'Read Report'],
            ],
            'groups' => [],
        ],
        'research_grid' => [
            'label' => 'Research reports grid',
            'help'  => 'Responsive grid of research cards (3-up desktop, 1-up mobile). Each card: image, meta badge (Report + date + city), title, divider, button. Load More button at bottom. Data from Research admin filtered by active tab.',
            'settings' => [
                'title'       => ['label' => 'Section heading', 'type' => 'text', 'required' => true, 'max' => 120, 'default' => 'All Project Reports'],
                'per_page'    => ['label' => 'Cards per page', 'type' => 'number', 'max' => 2, 'default' => '6'],
                'cta_label'   => ['label' => 'Button label', 'type' => 'text', 'max' => 60, 'default' => 'Read Report'],
                'load_label'  => ['label' => 'Load More label', 'type' => 'text', 'max' => 60, 'default' => 'Load More'],
            ],
            'groups' => [],
        ],
        'approach_panel' => [
            'label' => 'Our Approach panel',
            'help'  => 'Centred panel with eyebrow, large statement and a CTA button. Used on Research page.',
            'settings' => [
                'eyebrow'   => ['label' => 'Eyebrow', 'type' => 'text', 'max' => 60, 'default' => 'OUR APPROACH'],
                'statement' => ['label' => 'Statement', 'type' => 'textarea', 'required' => true, 'max' => 800],
                'cta_label' => ['label' => 'Button label', 'type' => 'text', 'max' => 60, 'default' => 'Enquire Now'],
                'cta_url'   => ['label' => 'Button link', 'type' => 'url', 'max' => 255],
            ],
            'groups' => [],
        ],
        'media_tabs' => [
            'label' => 'Media section tabs',
            'help'  => 'Centred pill navigation linking across Media Center sections (News, Industry, Knowledge, Awards, Videos, Newsletter, Testimonials).',
            'settings' => [
                'active' => ['label' => 'Active tab key', 'type' => 'text', 'max' => 30, 'default' => 'videos', 'hint' => 'news, industry, knowledge, awards, videos, newsletter, or testimonials.'],
            ],
            'groups' => [],
        ],
        'video_hub' => [
            'label' => 'Videos hub + More Videos grid (automatic)',
            'help'  => 'Featured video + 3 side items per category tab, plus the rounded More Videos 3-column grid with Load More. Reads the 5 Video admin modules.',
            'settings' => [
                'title'       => ['label' => 'Heading', 'type' => 'text', 'required' => true, 'max' => 120, 'default' => 'Videos'],
                'lead'        => ['label' => 'Lead text', 'type' => 'textarea', 'max' => 300],
                'default_tab' => ['label' => 'Default active tab', 'type' => 'text', 'max' => 60, 'default' => 'Finding a Home'],
                'more_title'  => ['label' => 'More Videos heading', 'type' => 'text', 'max' => 120, 'default' => 'More From CB India'],
                'per_page'    => ['label' => 'Cards per page', 'type' => 'number', 'max' => 2, 'default' => '6'],
                'load_label'  => ['label' => 'Load More label', 'type' => 'text', 'max' => 60, 'default' => 'Load More'],
            ],
            'groups' => [],
        ],
        'media_archive' => [
            'label' => 'Media archive grid (automatic)',
            'help'  => '3-column archive grid with Load More for Industry News, Knowledge Series, or Newsletter editions.',
            'settings' => [
                'source'     => ['label' => 'Source table', 'type' => 'text', 'required' => true, 'max' => 30, 'default' => 'industry', 'hint' => 'industry, knowledge, or newsletter.'],
                'title'      => ['label' => 'Heading', 'type' => 'text', 'max' => 120],
                'per_page'   => ['label' => 'Cards per page', 'type' => 'number', 'max' => 2, 'default' => '6'],
                'load_label' => ['label' => 'Load More label', 'type' => 'text', 'max' => 60, 'default' => 'Load More'],
            ],
            'groups' => [],
        ],
    ];

    public static function has(string $type): bool
    {
        return isset(self::TYPES[$type]);
    }

    /** @return array<string, mixed> */
    public static function get(string $type): array
    {
        return self::TYPES[$type] ?? throw new \InvalidArgumentException('Unknown block type: ' . $type);
    }

    public static function isHero(string $type): bool
    {
        return ! empty(self::TYPES[$type]['hero']);
    }

    /** Block settings JSON -> array with defaults for every declared field (unknown keys dropped). */
    public static function settings(string $type, ?string $json): array
    {
        $raw = json_decode((string) $json, true);
        $raw = is_array($raw) ? $raw : [];
        $out = [];
        foreach (self::get($type)['settings'] as $k => $f) {
            $v       = $raw[$k] ?? ($f['default'] ?? '');
            $out[$k] = is_scalar($v) ? (string) $v : '';
        }

        return $out;
    }
}
