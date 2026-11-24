<?php

/**
 * View helpers for the Coldwell Banker redesign (load with helper('cb')).
 * Every admin-entered link and image goes through App\Libraries\Cms\CmsUrl.
 */

use App\Libraries\Cms\CmsUrl;

if (! function_exists('cb_icon')) {
    /** Inline SVG icon (decorative: aria-hidden). */
    function cb_icon(string $name): string
    {
        $p = [
            'arrow'  => '<path d="M3 8h10M9 4l4 4-4 4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>',
            'prev'   => '<path d="M13 8H3M7 4L3 8l4 4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>',
            'diag'   => '<path d="M4.5 11.5l7-7M5.5 4.5h6v6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>',
            'menu'   => '<path d="M2.5 4.5h11M2.5 8h11M2.5 11.5h7" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>',
            'close'  => '<path d="M4 4l8 8M12 4l-8 8" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>',
            'chev'   => '<path d="M3.5 6l4.5 4.5L12.5 6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>',
            'play'   => '<path d="M5 3.2v9.6L13 8z" fill="currentColor"/>',
            'pause'  => '<rect x="4" y="3" width="2.6" height="10" rx="1" fill="currentColor"/><rect x="9.4" y="3" width="2.6" height="10" rx="1" fill="currentColor"/>',
            'fb'     => '<path d="M9.2 15V8.6h2.1l.3-2.5H9.2V4.5c0-.7.2-1.2 1.2-1.2h1.3V1.1A17 17 0 0 0 9.8 1C8 1 6.7 2.1 6.7 4.2v1.9H4.6v2.5h2.1V15z" fill="currentColor"/>',
            'in'     => '<path d="M3.6 5.6h-2.4V14h2.4zM2.4 1.6a1.4 1.4 0 1 0 0 2.8 1.4 1.4 0 0 0 0-2.8M14.8 9.3c0-2.3-.5-4-3.1-4-1.3 0-2.1.7-2.5 1.3V5.6H6.9V14h2.4V9.8c0-1.1.2-2.2 1.6-2.2s1.4 1.3 1.4 2.3V14h2.4z" fill="currentColor"/>',
            'x'      => '<path d="M12.2 1.5h2.3L9.4 7.3l5.9 7.2h-4.6L7.1 10l-4.1 4.5H.7l5.4-6.2L.4 1.5h4.7l3.3 4.1zm-.8 11.7h1.3L4.4 2.7H3z" fill="currentColor"/>',
            'yt'     => '<path d="M15.2 4.5a2 2 0 0 0-1.4-1.4C12.6 2.8 8 2.8 8 2.8s-4.6 0-5.8.3A2 2 0 0 0 .8 4.5 21 21 0 0 0 .5 8a21 21 0 0 0 .3 3.5 2 2 0 0 0 1.4 1.4c1.2.3 5.8.3 5.8.3s4.6 0 5.8-.3a2 2 0 0 0 1.4-1.4c.3-1.2.3-3.5.3-3.5s0-2.3-.3-3.5M6.5 10.2V5.8L10.3 8z" fill="currentColor"/>',
            'ig'     => '<rect x="1.8" y="1.8" width="12.4" height="12.4" rx="3.6" fill="none" stroke="currentColor" stroke-width="1.5"/><circle cx="8" cy="8" r="2.9" fill="none" stroke="currentColor" stroke-width="1.5"/><circle cx="11.7" cy="4.3" r=".9" fill="currentColor"/>',
            'pin' => '<path d="M8 14.5s4.6-4.1 4.6-8A4.6 4.6 0 0 0 3.4 6.5c0 3.9 4.6 8 4.6 8z" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/><circle cx="8" cy="6.4" r="1.7" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>',
            'building' => '<path d="M3 14.5V2.5h7v12M10 6h3v8.5M1.5 14.5h13M5.2 5h1.6M5.2 7.6h1.6M5.2 10.2h1.6" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>',
            'people' => '<circle cx="6" cy="5.2" r="2.4" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/><path d="M1.8 13.8c.4-2.4 2.1-3.8 4.2-3.8s3.8 1.4 4.2 3.8M10.6 3.1a2.3 2.3 0 0 1 0 4.3M11.9 10.3c1.3.5 2.1 1.7 2.3 3.5" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>',
            'shield' => '<path d="M8 1.6l5.2 2v4.1c0 3.3-2.2 5.6-5.2 6.7-3-1.1-5.2-3.4-5.2-6.7V3.6z" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/><path d="M5.6 8l1.7 1.7 3.1-3.3" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>',
            'bulb' => '<path d="M5.8 11.2c0-1.5-2-2.6-2-5A4.2 4.2 0 0 1 12.2 6.2c0 2.4-2 3.5-2 5zM6.2 13.4h3.6M6.9 15h2.2" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>',
            'handshake' => '<path d="M1.5 6.2l2.6-2.4 2.6 1 1.6-1 2.4.4 3.8 2.6M1.5 6.2v4l3 2.6M14.5 6.8v3.6l-3.2 2.4M6.7 4.8l-1.8 2.6c.6.8 1.6.8 2.2.2l1.1-1.1 3.6 3.2M10 11l-1.4 1.3M8.2 9.6l-1.6 1.5" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>',
            'chart' => '<path d="M1.8 14.2h12.4M3.6 11.4V8.8M6.9 11.4V6.4M10.2 11.4V4.2M13.4 11.4V2.6" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>',
            'heart' => '<path d="M8 13.8S1.8 10.2 1.8 5.9A3.2 3.2 0 0 1 8 4.4a3.2 3.2 0 0 1 6.2 1.5c0 4.3-6.2 7.9-6.2 7.9z" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>',
            'globe' => '<circle cx="8" cy="8" r="6.3" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/><path d="M1.8 8h12.4M8 1.7c1.8 1.8 2.6 3.9 2.6 6.3S9.8 12.5 8 14.3C6.2 12.5 5.4 10.4 5.4 8S6.2 3.5 8 1.7z" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>',
            'award' => '<circle cx="8" cy="6" r="4.2" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/><path d="M5.6 9.4l-1.2 5 3.6-1.8 3.6 1.8-1.2-5" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>',
            'star' => '<path d="M8 1.8l1.9 3.9 4.3.6-3.1 3 .7 4.3L8 11.6l-3.8 2 .7-4.3-3.1-3 4.3-.6z" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>',
            'check' => '<path d="M4.4 8.3l2.4 2.3 4.8-5.1" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>',
            'phone' => '<path d="M5.6 2.2l1.5 3.1-1.3 1.2a8 8 0 0 0 3.7 3.7l1.2-1.3 3.1 1.5-.5 2.4c-.1.5-.6.9-1.1.9A10.9 10.9 0 0 1 2.3 3.9c0-.5.4-1 .9-1.1z" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/>',
            'plus'  => '<path d="M8 3.5v9M3.5 8h9" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>',
            'minus' => '<path d="M3.5 8h9" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>',
            'mail'  => '<rect x="1.8" y="3.2" width="12.4" height="9.6" rx="1.6" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/><path d="M2.3 4l5.7 4.6L13.7 4" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>',
            'clock' => '<circle cx="8" cy="8" r="6.2" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/><path d="M8 4.6V8l2.4 1.6" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>',
            'download' => '<path d="M8 2.5v8M4.6 7.4L8 10.8l3.4-3.4M3 13.5h10" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>',
            'calendar' => '<rect x="2" y="3" width="12" height="11" rx="2" fill="none" stroke="currentColor" stroke-width="1.4"/><path d="M2 6.6h12M5.2 1.8v2.6M10.8 1.8v2.6" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>',
            'link'   => '<path d="M6.6 9.4l2.8-2.8M7.2 4.6l1.1-1.1a2.6 2.6 0 0 1 3.7 3.7l-1.1 1.1M8.8 11.4l-1.1 1.1a2.6 2.6 0 0 1-3.7-3.7l1.1-1.1" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>',
        ][$name] ?? '';

        return '<svg viewBox="0 0 16 16" aria-hidden="true" focusable="false">' . $p . '</svg>';
    }
}

if (! function_exists('cb_picture')) {
    /**
     * <picture> for a stored CMS image (CmsUrl::imageUrl; nothing when the value is not allowed) (WEBP source when a sibling exists).
     *
     * @param array<string, string> $attr extra <img> attributes (loading, width, height, class ...)
     */
    function cb_picture(?string $image, string $alt, array $attr = []): string
    {
        $src = CmsUrl::imageUrl($image);
        if ($src === '') {
            return '';
        }
        $webp  = CmsUrl::webpUrl($image);
        // `sizes` (CSS display width) lets the browser pick the smallest WEBP variant
        // (name-320/640/960.webp, generated next to seeded photos) that is still sharp.
        $sizes = $attr['sizes'] ?? '100vw';
        unset($attr['sizes']);
        $attr += ['loading' => 'lazy', 'decoding' => 'async'];
        $a = '';
        foreach ($attr as $k => $v) {
            $a .= ' ' . $k . '="' . esc($v, 'attr') . '"';
        }
        $img = '<img src="' . esc($src, 'attr') . '" alt="' . esc($alt, 'attr') . '"' . $a . '>';
        if ($webp === '') {
            return $img;
        }

        return '<picture><source type="image/webp" srcset="' . esc(cb_webp_srcset((string) $image, $webp), 'attr') . '" sizes="' . esc($sizes, 'attr') . '">' . $img . '</picture>';
    }
}

if (! function_exists('cb_webp_srcset')) {
    /** "x-320.webp 320w, x-640.webp 640w, x.webp 1536w" for a seeded image whose variants exist (memoised per request). */
    function cb_webp_srcset(string $image, string $webpUrl): string
    {
        static $memo = [];
        if (isset($memo[$image])) {
            return $memo[$image];
        }
        $root  = rtrim(FCPATH, '\\/') . '/';
        $file  = (string) preg_replace('/\.jpe?g$/i', '.webp', $image);
        $parts = [];
        foreach ([320, 640, 960] as $w) {
            $v = substr($file, 0, -5) . '-' . $w . '.webp';
            if (is_file($root . $v)) {
                $parts[] = base_url($v) . ' ' . $w . 'w';
            }
        }
        $size    = @getimagesize($root . $file);
        $parts[] = $webpUrl . ($size ? ' ' . $size[0] . 'w' : '');

        return $memo[$image] = count($parts) > 1 ? implode(', ', $parts) : $webpUrl;
    }
}

if (! function_exists('cb_link_attrs')) {
    /** href plus target/rel for an admin-entered link (external links open in a new tab); unsafe links fall back to the home page. */
    function cb_link_attrs(?string $url): string
    {
        $href = CmsUrl::href($url);
        if ($href === '') {
            return 'href="' . esc(site_url('/'), 'attr') . '"';
        }

        return 'href="' . esc($href, 'attr') . '"' . (CmsUrl::isExternal($href) ? ' target="_blank" rel="noopener"' : '');
    }
}

if (! function_exists('cb_pill')) {
    /** Pill Button (Figma component 41:40): tone gold | navy | light. Nothing without a label or a safe link. */
    function cb_pill(string $label, ?string $url, string $tone = 'gold', string $extraClass = ''): string
    {
        if (trim($label) === '' || CmsUrl::href($url) === '') {
            return '';
        }

        return '<a class="cb-pill cb-pill--' . esc($tone, 'attr') . ($extraClass !== '' ? ' ' . esc($extraClass, 'attr') : '') . '" ' . cb_link_attrs($url) . '>'
            . '<span>' . esc($label) . '</span><span class="cb-pill__icon">' . cb_icon('arrow') . '</span></a>';
    }
}
