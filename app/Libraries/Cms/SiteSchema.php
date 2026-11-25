<?php

namespace App\Libraries\Cms;

/**
 * Menu locations and site-wide settings of the CMS.
 */
final class SiteSchema
{
    /**
     * Menu locations rendered by the layout. `depth` 2 allows one level of children
     * (desktop dropdown / drawer group); `icons` = social icons picked from the URL host.
     */
    public const MENUS = [
        'main'           => ['name' => 'Main navigation', 'depth' => 2, 'max' => 30, 'help' => 'Glass pill menu in the header and the phone menu. Items with children become a dropdown.'],
        'footer_quick'   => ['name' => 'Footer — Quick Links', 'depth' => 1, 'max' => 10, 'help' => 'First footer column.'],
        'footer_network' => ['name' => 'Footer — Our Network', 'depth' => 1, 'max' => 10, 'help' => 'Second footer column.'],
        'footer_media'   => ['name' => 'Footer — Media Center', 'depth' => 1, 'max' => 10, 'help' => 'Third footer column.'],
        'footer_legal'   => ['name' => 'Footer — legal links', 'depth' => 1, 'max' => 6, 'help' => 'Bottom row of the footer.'],
        'social'         => ['name' => 'Social links', 'depth' => 1, 'max' => 8, 'help' => 'Footer "Follow us" icons. Icon is chosen from the link: facebook, linkedin, twitter / x, youtube, instagram.', 'icons' => true],
    ];

    /** Column headings of the footer menus (editable via settings). */
    public const FOOTER_HEADINGS = ['footer_quick' => 'footer_quick_title', 'footer_network' => 'footer_network_title', 'footer_media' => 'footer_media_title'];

    /** @var array<string, array<string, array{label: string, type: string, max: int, hint?: string}>> */
    public const SETTINGS = [
        'Header' => [
            'nav_cta_label' => ['label' => 'Header button label', 'type' => 'text', 'max' => 40],
            'nav_cta_url'   => ['label' => 'Header button link', 'type' => 'url', 'max' => 255],
        ],
        'Footer' => [
            'footer_email'          => ['label' => 'Email', 'type' => 'text', 'max' => 120],
            'footer_phone'          => ['label' => 'Phone', 'type' => 'text', 'max' => 40],
            'footer_address'        => ['label' => 'Address', 'type' => 'textarea', 'max' => 300],
            'footer_quick_title'    => ['label' => 'Column 1 heading', 'type' => 'text', 'max' => 40],
            'footer_network_title'  => ['label' => 'Column 2 heading', 'type' => 'text', 'max' => 40],
            'footer_media_title'    => ['label' => 'Column 3 heading', 'type' => 'text', 'max' => 40],
            'footer_social_title'   => ['label' => 'Social heading', 'type' => 'text', 'max' => 40],
            'footer_copyright'      => ['label' => 'Copyright line', 'type' => 'text', 'max' => 160, 'hint' => '{year} is replaced with the current year.'],
        ],
    ];

    /** @return array<string, array{label: string, type: string, max: int, hint?: string}> */
    public static function settingFields(): array
    {
        return array_merge(...array_values(self::SETTINGS));
    }

    /** Social icon key for a URL (fb | in | x | yt | ig | link). */
    public static function socialIcon(string $url): string
    {
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));

        return match (true) {
            str_contains($host, 'facebook.')  => 'fb',
            str_contains($host, 'linkedin.')  => 'in',
            str_contains($host, 'twitter.'), $host === 'x.com', str_ends_with($host, '.x.com') => 'x',
            str_contains($host, 'youtube.'), str_contains($host, 'youtu.be') => 'yt',
            str_contains($host, 'instagram.') => 'ig',
            default                           => 'link',
        };
    }
}
