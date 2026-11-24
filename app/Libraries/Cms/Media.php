<?php

namespace App\Libraries\Cms;

/**
 * Read side of the existing Media modules for the Media Center CMS blocks: CB India News,
 * Industry News, Awards, Research, Newsletter and the five video tables. The content keeps
 * being managed in the existing admin screens; blocks only choose headings and counts.
 *
 * - Legacy news titles often embed "Title - Publication - dd.mm.yyyy"; that is split into
 *   title, source and the real publication date (created_date is the import date there).
 * - News cards use library photography (the uploaded news images are screenshots of
 *   third-party publisher pages: unreadable at card size and owned by the publishers).
 * - Every query is cached with the CMS cache and never throws.
 */
final class Media
{
    /** Library photos rotated over news cards (seeded WEBP/JPG defaults). */
    private const PHOTOS = ['city-mumbai', 'towers', 'boardroom', 'news-research', 'family', 'villa', 'news-press', 'handshake', 'living', 'city-delhi', 'news-video', 'city-bengaluru'];

    /** Video tables => tab label and kind badge. */
    public const VIDEO_SOURCES = [
        'cb_india_videos'     => ['CB India', 'CB India'],
        'featured_videos'     => ['Featured', 'Featured'],
        'finding_home_videos' => ['Finding a Home', 'Guide'],
        'selling_home_videos' => ['Selling a Home', 'Guide'],
        'testimonial_videos'  => ['Testimonials', 'Testimonial'],
    ];

    /**
     * Combined, newest-first feed for "Latest from the Newsroom".
     *
     * @return list<array{cat: string, label: string, meta: string, title: string, href: string, external: bool, image: string, ts: int}>
     */
    public static function feed(int $perSource = 12): array
    {
        return self::remember('cms_media_feed_' . $perSource, static function () use ($perSource): array {
            $out = [];
            foreach ([['coldwell_banker_news', 'news', 'CB India News'], ['coldwell_banker_industry_news', 'industry', 'Industry News']] as [$table, $cat, $label]) {
                foreach (self::rows($table, 'title, external_link, created_date', $perSource) as $r) {
                    $p     = self::parseTitle((string) $r['title'], (string) $r['created_date']);
                    $link  = trim((string) $r['external_link']);
                    $href  = preg_match('#^https?://#i', $link) ? $link : site_url($cat === 'news' ? 'media-center' : 'industry-news');
                    $out[] = self::item($cat, $label, $p['title'], $p['source'] ?: ($cat === 'news' ? 'Press' : 'Industry'), $p['ts'], $href);
                }
            }
            foreach (self::rows('awards', 'name, created_date', $perSource) as $r) {
                $out[] = self::item('awards', 'Awards', self::clean((string) $r['name']), 'Award', (int) strtotime((string) $r['created_date']), site_url('awards'));
            }
            foreach (self::rows('research', 'type, title, pdf_name, created_date', $perSource, true) as $r) {
                $out[] = self::item('research', 'Research', trim((string) $r['type']) . ' — ' . self::clean((string) $r['title']), 'Report', (int) strtotime((string) $r['created_date']), self::pdfUrl((string) $r['pdf_name']) ?: site_url('research'));
            }
            foreach (self::rows('cb_india_videos', 'title, video_url, created_date', $perSource, true) as $r) {
                $v     = self::youtube((string) $r['video_url']);
                $out[] = self::item('videos', 'Videos', self::clean((string) $r['title']), 'Video', (int) strtotime((string) $r['created_date']), $v['watch'] ?: site_url('cb-india-videos'));
            }
            foreach (self::rows('coldwell_banker_newsletter', 'id, title, created_date', $perSource) as $r) {
                $p     = self::parseTitle((string) $r['title'], (string) $r['created_date']);
                $out[] = self::item('newsletter', 'Newsletter', $p['title'], 'Edition', $p['ts'], site_url('iframe-newsletter/' . (int) $r['id']));
            }
            usort($out, static fn ($a, $b) => $b['ts'] <=> $a['ts']);
            foreach ($out as $i => &$o) {
                $o['image'] = 'assets/images/home-v2/' . self::PHOTOS[$i % count(self::PHOTOS)] . '.jpg';
            }

            return $out;
        });
    }

    /** One news row by id (featured story), or the newest one. */
    public static function newsItem(?int $id): ?array
    {
        return self::remember('cms_media_news_' . (int) $id, static function () use ($id): ?array {
            try {
                $b = db_connect()->table('coldwell_banker_news')->select('id, title, description, external_link, created_date');
                $id ? $b->where('id', $id) : $b->orderBy('created_date', 'DESC');
                $r = $b->get(1)->getRowArray();
            } catch (\Throwable $e) {
                log_message('error', 'CMS media: {msg}', ['msg' => $e->getMessage()]);

                return null;
            }
            if (! $r) {
                return null;
            }
            $p    = self::parseTitle((string) $r['title'], (string) $r['created_date']);
            $link = trim((string) $r['external_link']);

            return [
                'title'    => $p['title'],
                'meta'     => ($p['ts'] ? date('d M Y', $p['ts']) : '') . ($p['source'] !== '' ? ' · ' . $p['source'] : ''),
                'excerpt'  => self::clean((string) $r['description'], 260),
                'href'     => preg_match('#^https?://#i', $link) ? $link : site_url('media-center'),
                'external' => (bool) preg_match('#^https?://#i', $link),
            ];
        });
    }

    /** @return list<array{name: string, image: string}> newest first, with a real photo when uploaded */
    public static function awards(int $limit): array
    {
        return self::remember('cms_media_awards_' . $limit, static function () use ($limit): array {
            $out = [];
            foreach (self::rows('awards', 'name, image, created_date', $limit) as $r) {
                $img   = (string) $r['image'];
                $out[] = ['name' => self::clean((string) $r['name'], 160), 'image' => preg_match('/^[A-Za-z0-9_.-]+\.(jpe?g|png|webp)$/i', $img) && is_file(FCPATH . 'uploads/awards/' . $img) ? base_url('uploads/awards/' . $img) : ''];
            }

            return $out;
        });
    }

    /** @return array<string, list<array{title: string, watch: string, thumb: string, kind: string}>> tab => videos */
    public static function videos(int $perSource): array
    {
        return self::remember('cms_media_videos_' . $perSource, static function () use ($perSource): array {
            $out = [];
            foreach (self::VIDEO_SOURCES as $table => [$tab, $kind]) {
                foreach (self::rows($table, 'title, video_url, created_date', $perSource, true) as $r) {
                    $v = self::youtube((string) $r['video_url']);
                    if ($v['watch'] === '') {
                        continue;
                    }
                    $out[$tab][] = ['title' => self::clean((string) $r['title'], 120), 'watch' => $v['watch'], 'thumb' => $v['thumb'], 'kind' => $kind];
                }
            }

            return $out;
        });
    }

    /** @return list<array{title: string, meta: string, href: string}> */
    public static function research(int $limit): array
    {
        return self::remember('cms_media_research_' . $limit, static function () use ($limit): array {
            $out = [];
            foreach (self::rows('research', 'type, title, pdf_name, created_date', $limit, true) as $r) {
                $type  = trim((string) $r['type']) ?: 'Report';
                $out[] = ['title' => ($type === 'Area Insights' ? 'Area Insights — ' : '') . self::titleCase(self::clean((string) $r['title'], 120)), 'meta' => $type . ' · PDF', 'href' => self::pdfUrl((string) $r['pdf_name']) ?: site_url('research')];
            }

            return $out;
        });
    }

    /** @return list<array{title: string, meta: string, href: string}> */
    public static function newsletters(int $limit): array
    {
        return self::remember('cms_media_newsletters_' . $limit, static function () use ($limit): array {
            $out = [];
            foreach (self::rows('coldwell_banker_newsletter', 'id, title, created_date', $limit) as $r) {
                $p     = self::parseTitle((string) $r['title'], (string) $r['created_date']);
                $out[] = ['title' => 'Newsletter — ' . ($p['ts'] ? date('j M Y', $p['ts']) : $p['title']), 'meta' => 'Edition · Read online', 'href' => site_url('iframe-newsletter/' . (int) $r['id'])];
            }

            return $out;
        });
    }

    /** YouTube embed / watch / youtu.be URL -> watch link + thumbnail ('' when not YouTube). */
    public static function youtube(string $url): array
    {
        if (preg_match('#(?:youtube\.com/(?:embed/|watch\?v=|v/)|youtu\.be/)([A-Za-z0-9_-]{11})#', $url, $m)) {
            return ['watch' => 'https://www.youtube.com/watch?v=' . $m[1], 'thumb' => 'https://i.ytimg.com/vi/' . $m[1] . '/hqdefault.jpg'];
        }

        return ['watch' => '', 'thumb' => ''];
    }

    /** "Title - Publication - dd.mm.yyyy" -> parts; falls back to created_date. */
    public static function parseTitle(string $raw, string $created): array
    {
        $t = self::clean($raw, 300);
        if (preg_match('/^(.*?)\s+-\s+([^-]{2,60}?)\s+-\s+(\d{1,2})[.\/](\d{1,2})[.\/](\d{4})\s*$/u', $t, $m)) {
            return ['title' => trim($m[1]), 'source' => trim($m[2]), 'ts' => (int) mktime(0, 0, 0, (int) $m[4], (int) $m[3], (int) $m[5])];
        }
        if (preg_match('/^(.*?)\s+-\s+(\d{1,2})[.\/](\d{1,2})[.\/](\d{4})\s*$/u', $t, $m)) {
            return ['title' => trim($m[1]), 'source' => '', 'ts' => (int) mktime(0, 0, 0, (int) $m[3], (int) $m[2], (int) $m[4])];
        }

        return ['title' => $t, 'source' => '', 'ts' => (int) strtotime($created)];
    }

    private static function item(string $cat, string $label, string $title, string $source, int $ts, string $href): array
    {
        return [
            'cat' => $cat, 'label' => $label, 'title' => $title,
            'meta' => ($ts > 0 ? date('d M Y', $ts) . ' · ' : '') . $source,
            'href' => $href, 'external' => CmsUrl::isExternal($href), 'image' => '', 'ts' => $ts,
        ];
    }

    /** Normalise titles typed in all lower case or ALL CAPS ("CRESCENT BAY" -> "Crescent Bay"). */
    private static function titleCase(string $t): string
    {
        return ($t === mb_strtolower($t) || $t === mb_strtoupper($t)) ? mb_convert_case(mb_strtolower($t), MB_CASE_TITLE, 'UTF-8') : $t;
    }

    private static function pdfUrl(string $file): string
    {
        return preg_match('/^[A-Za-z0-9_.-]+\.pdf$/i', $file) && is_file(FCPATH . 'uploads/research/' . $file) ? base_url('uploads/research/' . $file) : '';
    }

    private static function clean(string $s, int $max = 200): string
    {
        $s = trim((string) preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags($s), ENT_QUOTES | ENT_HTML5, 'UTF-8')));

        return mb_strlen($s) > $max ? rtrim(mb_substr($s, 0, $max - 1)) . '…' : $s;
    }

    /** @return list<array<string, mixed>> */
    private static function rows(string $table, string $cols, int $limit, bool $activeOnly = false): array
    {
        try {
            $b = db_connect()->table($table)->select($cols);
            if ($activeOnly) {
                $b->where('is_active', 1);
            }

            return $b->orderBy('created_date', 'DESC')->limit(max(1, min(100, $limit)))->get()->getResultArray();
        } catch (\Throwable $e) {
            log_message('error', 'CMS media {t}: {msg}', ['t' => $table, 'msg' => $e->getMessage()]);

            return [];
        }
    }

    /** @param callable(): mixed $fn */
    private static function remember(string $key, callable $fn): mixed
    {
        $hit = cache($key);
        if ($hit !== null) {
            return $hit;
        }
        $val = $fn();
        if ($val !== null) {
            cache()->save($key, $val, 3600);
        }

        return $val;
    }
}
