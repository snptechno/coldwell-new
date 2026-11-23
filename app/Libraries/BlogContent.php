<?php

namespace App\Libraries;

use DOMDocument;
use DOMElement;
use DOMNode;

/**
 * Helpers for blog text: URL slugs, HTML sanitising and plain-text excerpts.
 *
 * The sanitiser is an allow-list built on DOMDocument (no HTMLPurifier in vendor/).
 * It keeps the formatting an editor produces (headings, lists, links, images, tables)
 * and drops scripts, styles, event handlers and javascript:/data: URLs.
 */
final class BlogContent
{
    /** tag => allowed attributes */
    private const ALLOWED = [
        'p' => [], 'br' => [], 'hr' => [], 'strong' => [], 'b' => [], 'em' => [], 'i' => [], 'u' => [],
        's' => [], 'sub' => [], 'sup' => [], 'small' => [], 'span' => [], 'div' => [], 'blockquote' => [],
        'h2' => ['id'], 'h3' => ['id'], 'h4' => ['id'], 'h5' => [], 'h6' => [], 'pre' => [], 'code' => [],
        'ul' => [], 'ol' => [], 'li' => [], 'dl' => [], 'dt' => [], 'dd' => [],
        'a' => ['href', 'title', 'target', 'rel'],
        'img' => ['src', 'alt', 'title', 'width', 'height'],
        'figure' => [], 'figcaption' => [],
        'table' => [], 'thead' => [], 'tbody' => [], 'tr' => [], 'th' => ['colspan', 'rowspan'], 'td' => ['colspan', 'rowspan'],
        'iframe' => ['src', 'width', 'height', 'allowfullscreen', 'frameborder'],
    ];

    /** Tags removed together with their content. */
    private const DROP = ['script', 'style', 'object', 'embed', 'form', 'input', 'button', 'select', 'textarea', 'link', 'meta', 'base', 'svg', 'math', 'noscript', 'template'];

    /** iframes are only kept for these video hosts. */
    private const IFRAME_HOSTS = ['www.youtube.com', 'youtube.com', 'www.youtube-nocookie.com', 'player.vimeo.com'];

    /** "Garden Homes - A style!" => "garden-homes-a-style" */
    public static function slugify(string $text, int $maxLength = 120): string
    {
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = str_replace(['&', "\u{2019}", "'"], [' and ', '', ''], $text);
        if (function_exists('transliterator_transliterate')) {
            $t = transliterator_transliterate('Any-Latin; Latin-ASCII', $text);
            if (is_string($t)) {
                $text = $t;
            }
        }
        $text = strtolower($text);
        $text = (string) preg_replace('/[^a-z0-9]+/', '-', $text);
        $text = trim($text, '-');
        if (strlen($text) > $maxLength) {
            $text = rtrim(substr($text, 0, $maxLength), '-');
        }

        return $text;
    }

    /** True when the value is a valid slug (lowercase letters, digits, single dashes). */
    public static function isValidSlug(string $slug): bool
    {
        return (bool) preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug);
    }

    /** Plain-text excerpt of HTML, cut on a word boundary. */
    public static function excerpt(string $html, int $length = 160): string
    {
        $html = str_replace(['<br', '</p>', '</li>', '</h'], [' <br', ' </p>', ' </li>', ' </h'], $html);
        $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = trim((string) preg_replace('/\s+/u', ' ', $text));
        if (mb_strlen($text) <= $length) {
            return $text;
        }
        $cut   = mb_substr($text, 0, $length - 1);
        $space = mb_strrpos($cut, ' ');
        if ($space !== false && $space > $length * 0.6) {
            $cut = mb_substr($cut, 0, $space);
        }

        return rtrim($cut, ' ,.;:-') . "\u{2026}";
    }

    /** Wrap plain text (Drupal bodies with blank-line paragraphs) in <p> tags. */
    public static function autoParagraph(string $text): string
    {
        $text = str_replace(["\r\n", "\r"], "\n", trim($text));
        if ($text === '') {
            return '';
        }
        if (preg_match('/<(p|div|h[1-6]|ul|ol|table|blockquote)[\s>]/i', $text)) {
            return $text;
        }
        $out = [];
        foreach (preg_split('/\n\s*\n/', $text) as $para) {
            $para = trim($para);
            if ($para !== '') {
                $out[] = '<p>' . nl2br($para, false) . '</p>';
            }
        }

        return implode("\n", $out);
    }

    /** Allow-list HTML sanitiser for post bodies. */
    public static function sanitize(string $html): string
    {
        if (trim($html) === '') {
            return '';
        }
        $doc  = new DOMDocument('1.0', 'UTF-8');
        $prev = libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml encoding="UTF-8"?><div id="__root">' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NONET);
        libxml_clear_errors();
        libxml_use_internal_errors($prev);

        $root = null;
        foreach ($doc->getElementsByTagName('div') as $div) {
            if ($div->getAttribute('id') === '__root') {
                $root = $div;
                break;
            }
        }
        if (! $root instanceof DOMElement) {
            return '';
        }
        self::cleanChildren($root);

        $out = '';
        foreach ($root->childNodes as $child) {
            $out .= $doc->saveHTML($child);
        }

        return trim($out);
    }

    private static function cleanChildren(DOMNode $node): void
    {
        $children = [];
        foreach ($node->childNodes as $c) {
            $children[] = $c;
        }
        foreach ($children as $child) {
            if ($child->nodeType === XML_COMMENT_NODE || $child->nodeType === XML_PI_NODE) {
                $node->removeChild($child);

                continue;
            }
            if (! $child instanceof DOMElement) {
                continue;
            }
            $tag = strtolower($child->tagName);
            if (in_array($tag, self::DROP, true)) {
                $node->removeChild($child);

                continue;
            }
            self::cleanChildren($child);
            if (! array_key_exists($tag, self::ALLOWED)) {
                // Unknown tag: keep its (already cleaned) content, drop the wrapper.
                while ($child->firstChild) {
                    $node->insertBefore($child->firstChild, $child);
                }
                $node->removeChild($child);

                continue;
            }
            $allowed = self::ALLOWED[$tag];
            foreach (iterator_to_array($child->attributes) as $attr) {
                if (! in_array(strtolower($attr->nodeName), $allowed, true)) {
                    $child->removeAttribute($attr->nodeName);
                }
            }
            foreach (['href', 'src'] as $urlAttr) {
                if ($child->hasAttribute($urlAttr) && ! self::safeUrl($child->getAttribute($urlAttr))) {
                    $child->removeAttribute($urlAttr);
                }
            }
            if ($tag === 'iframe') {
                $host = strtolower((string) parse_url($child->getAttribute('src'), PHP_URL_HOST));
                if (! in_array($host, self::IFRAME_HOSTS, true)) {
                    $node->removeChild($child);

                    continue;
                }
            }
            if ($tag === 'a' && $child->getAttribute('target') === '_blank') {
                $child->setAttribute('rel', 'noopener noreferrer');
            }
        }
    }

    private static function safeUrl(string $url): bool
    {
        $url = trim(html_entity_decode($url, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        $url = (string) preg_replace('/[\x00-\x20]+/', '', $url);
        if ($url === '' || str_starts_with($url, '#') || str_starts_with($url, '/')) {
            return true;
        }
        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));

        return $scheme === '' ? ! str_contains($url, ':') : in_array($scheme, ['http', 'https', 'mailto', 'tel'], true);
    }
}
