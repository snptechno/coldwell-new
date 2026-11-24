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
