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
            'help'  => 'Shorter photo hero for inner pages; holds the site menu. Use as the first block.',
            'hero'  => true,
            'settings' => [
                'eyebrow'   => ['label' => 'Eyebrow / breadcrumb', 'type' => 'text', 'max' => 120],
                'title'     => ['label' => 'Headline', 'type' => 'textarea', 'required' => true, 'max' => 200],
                'lead'      => ['label' => 'Lead text', 'type' => 'textarea', 'max' => 400],
                'cta_label' => ['label' => 'Button label', 'type' => 'text', 'max' => 60],
                'cta_url'   => ['label' => 'Button link', 'type' => 'url', 'max' => 255],
                'image'     => ['label' => 'Background photo', 'type' => 'image', 'required' => true],
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
                'title'     => ['label' => 'Heading', 'type' => 'textarea', 'required' => true, 'max' => 160],
                'body'      => ['label' => 'Text', 'type' => 'textarea', 'max' => 600],
                'cta_label' => ['label' => 'Button label', 'type' => 'text', 'max' => 60],
                'cta_url'   => ['label' => 'Button link', 'type' => 'url', 'max' => 255],
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
                    'title'    => ['label' => 'Figure', 'type' => 'text', 'required' => true, 'max' => 20, 'hint' => 'Only publish verified figures.'],
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
            'help'  => 'Centred heading and white icon cards. "Row" fits up to 5 per row (About > Our Values); "3-column grid" highlights the first card in navy (Franchise > Why partner with us).',
            'settings' => [
                'layout'  => ['label' => 'Layout', 'type' => 'select', 'options' => ['row' => 'Row (up to 5)', 'grid3' => '3-column grid, first card highlighted'], 'default' => 'row'],
                'eyebrow' => ['label' => 'Eyebrow', 'type' => 'text', 'max' => 60],
                'title' => ['label' => 'Heading', 'type' => 'text', 'required' => true, 'max' => 120],
                'lead'  => ['label' => 'Lead text', 'type' => 'textarea', 'max' => 300],
            ],
            'groups' => [
                'value' => ['label' => 'Cards', 'max' => 12, 'fields' => [
                    'kicker'   => ['label' => 'Icon', 'type' => 'select', 'options' => self::ICONS, 'default' => 'shield'],
                    'title'    => ['label' => 'Title', 'type' => 'text', 'required' => true, 'max' => 60],
                    'subtitle' => ['label' => 'Text', 'type' => 'text', 'max' => 160],
                    'cta_label' => ['label' => 'Button label (optional)', 'type' => 'text', 'max' => 40],
                    'cta_url'   => ['label' => 'Button link', 'type' => 'url', 'max' => 255],
                ]],
            ],
        ],
        'steps' => [
            'label' => 'Numbered steps (process)',
            'help'  => 'Soft panel with centred heading and numbered steps joined by a line; the last step is gold.',
            'settings' => [
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
