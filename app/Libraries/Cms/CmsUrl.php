<?php

namespace App\Libraries\Cms;

/**
 * Link and image URL rules for every CMS-entered value (pages, blocks, menus, settings).
 * Admin input is never echoed as a raw href/src: it always goes through here.
 */
final class CmsUrl
{
    /** Admin uploads live in public/uploads/<UPLOAD_DIR>/. */
    public const UPLOAD_DIR = 'cms';

    /**
     * Admin-entered link -> safe href. http(s) URLs stay, "#anchor", mailto: and tel: stay,
     * site paths ("contact-us", "/franchise-details?x=1") become site URLs, anything else
     * (javascript:, data:, vbscript:, protocol-relative //evil) returns ''.
     */
    public static function href(?string $url): string
    {
        $url = trim((string) $url);
        if ($url === '' || preg_match('/[\x00-\x1F\x7F]/', $url)) {
            return '';
        }
        if (str_starts_with($url, '//') || str_starts_with($url, '\\')) {
            return '';
        }
        if (preg_match('#^https?://[^\s/$.?\#][^\s]*$#i', $url)) {
            return $url;
        }
        if (str_starts_with($url, '#') || preg_match('#^mailto:[^\s@]+@[^\s@]+$#i', $url) || preg_match('#^tel:\+?[0-9 ()-]{3,}$#i', $url)) {
            return $url;
        }
        if (preg_match('#^[a-z][a-z0-9+.-]*:#i', $url)) {
            return '';
        }

        return site_url(ltrim($url, '/'));
    }

    /** True when an href leaves this site (opens in a new tab with rel=noopener). */
    public static function isExternal(string $href): bool
    {
        if (! preg_match('#^https?://#i', $href)) {
            return false;
        }
        $host = (string) parse_url($href, PHP_URL_HOST);
        $own  = (string) parse_url(base_url(), PHP_URL_HOST);

        return $host !== '' && strcasecmp($host, $own) !== 0;
    }

    /**
     * Stored image value -> public URL. Seeded defaults are paths under public/ that start
     * with "assets/"; admin uploads are bare file names in uploads/cms/. Anything else
     * (absolute URLs, "../", other folders) is rejected.
     */
    public static function imageUrl(?string $image): string
    {
        $image = trim((string) $image);
        if ($image === '' || str_contains($image, '..') || str_contains($image, '\\')) {
            return '';
        }
        if (str_contains($image, '/')) {
            return preg_match('#^assets/[A-Za-z0-9/_.-]+\.(jpe?g|png|webp|svg)$#i', $image) ? base_url($image) : '';
        }

        return preg_match('#^[A-Za-z0-9_.-]+\.(jpe?g|png|webp)$#i', $image) ? base_url('uploads/' . self::UPLOAD_DIR . '/' . $image) : '';
    }

    /** WEBP sibling of a seeded JPG default (assets/.../x.jpg -> x.webp), or ''. */
    public static function webpUrl(?string $image): string
    {
        $image = trim((string) $image);
        if (self::imageUrl($image) === '' || ! str_contains($image, '/') || ! preg_match('/\.jpe?g$/i', $image)) {
            return '';
        }
        $webp = (string) preg_replace('/\.jpe?g$/i', '.webp', $image);

        return is_file(rtrim(FCPATH, '\\/') . '/' . $webp) ? base_url($webp) : '';
    }
}
