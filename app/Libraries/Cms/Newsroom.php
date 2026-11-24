<?php

namespace App\Libraries\Cms;

/**
 * Cards of the CMS "newsroom" block: the latest real item from three existing admin
 * modules (Research, CB India News, CB India Videos), so the block needs no separate
 * editing. (Moved from App\Libraries\HomeNewsroom.)
 *
 * Card photos are library images per category. The uploaded news images are
 * screenshots of third-party publisher pages (unreadable at card size and owned by
 * the publishers), so they are not used as thumbnails.
 */
final class Newsroom
{
    private const IMG = 'assets/images/home-v2/';

    /** @return list<array{category: string, meta: string, title: string, href: string, external: bool, image: string, alt: string}> */
    public static function cards(): array
    {
        try {
            return self::load();
        } catch (\Throwable $e) {
            // The newsroom must never take a page down; log and show no cards.
            log_message('error', 'CMS newsroom: {msg}', ['msg' => $e->getMessage()]);

            return [];
        }
    }

    /** @return list<array<string, mixed>> */
    private static function load(): array
    {
        $db    = db_connect();
        $cards = [];

        $r = $db->table('research')->select('title, type, created_date')->where('is_active', 1)
            ->orderBy('created_date', 'DESC')->limit(1)->get()->getRowArray();
        if ($r) {
            $cards[] = self::card('Research', $r['created_date'], trim((string) $r['type']) ?: 'Market Report', (string) $r['title'], site_url('research'), self::IMG . 'news-research.jpg', 'Research desk with market charts');
        }

        $n = $db->table('coldwell_banker_news')->select('title, external_link, created_date')
            ->orderBy('created_date', 'DESC')->limit(1)->get()->getRowArray();
        if ($n) {
            // Only absolute http(s) links that pass CmsUrl are followed; anything else opens the media center.
            $link = CmsUrl::href((string) $n['external_link']);
            $href = preg_match('#^https?://#i', $link) ? $link : site_url('media-center');
            $cards[] = self::card('Press', $n['created_date'], 'In the News', strip_tags((string) $n['title']), $href, self::IMG . 'news-press.jpg', 'Real estate press event on stage');
        }

        $v = $db->table('cb_india_videos')->select('title, video_url, created_date')->where('is_active', 1)
            ->orderBy('created_date', 'DESC')->limit(1)->get()->getRowArray();
        if ($v) {
            $cards[] = self::card('Video', $v['created_date'], 'Video', (string) $v['title'], site_url('cb-india-videos'), self::IMG . 'news-video.jpg', 'Videographer filming an apartment tour');
        }

        return $cards;
    }

    /** @return array{category: string, meta: string, title: string, href: string, external: bool, image: string, alt: string} */
    private static function card(string $category, ?string $date, string $kind, string $title, string $href, string $image, string $alt): array
    {
        $ts   = $date ? strtotime($date) : false;
        $meta = ($ts ? date('M Y', $ts) . ' · ' : '') . $kind;

        return [
            'category' => $category,
            'meta'     => $meta,
            // Plain text: decoded here, escaped by the view.
            'title'    => html_entity_decode(trim($title), ENT_QUOTES | ENT_HTML5, 'UTF-8'),
            'href'     => $href,
            'external' => CmsUrl::isExternal($href),
            'image'    => $image,
            'alt'      => $alt,
        ];
    }
}
