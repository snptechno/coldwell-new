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
        $webp = CmsUrl::webpUrl($image);
        $attr += ['loading' => 'lazy', 'decoding' => 'async'];
        $a = '';
        foreach ($attr as $k => $v) {
            $a .= ' ' . $k . '="' . esc($v, 'attr') . '"';
        }
        $img = '<img src="' . esc($src, 'attr') . '" alt="' . esc($alt, 'attr') . '"' . $a . '>';

        return $webp !== '' ? '<picture><source type="image/webp" srcset="' . esc($webp, 'attr') . '">' . $img . '</picture>' : $img;
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
