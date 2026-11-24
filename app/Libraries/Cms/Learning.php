<?php

namespace App\Libraries\Cms;

/**
 * Read side of the existing Learning admin modules for the L&D CMS blocks:
 * training events (events) and the photo albums (gallery_thumbs = album covers,
 * gallery = album photos, joined on group_id). Cached, never throws.
 */
final class Learning
{
    /**
     * Upcoming (or running) training events only — past batches are never presented as upcoming.
     *
     * @return list<array{title: string, mon: string, day: string, dates: string, duration: string, seats: string}>
     */
    public static function upcomingEvents(int $limit): array
    {
        $key = 'cms_learning_events_' . $limit . '_' . date('Ymd');

        return self::remember($key, static function () use ($limit): array {
            try {
                $rows = db_connect()->table('events')->select('title, start_date, end_date, duration, seats')
                    ->where('status', 1)->where('end_date >=', date('Y-m-d'))
                    ->orderBy('start_date', 'ASC')->limit(max(1, min(20, $limit)))->get()->getResultArray();
            } catch (\Throwable $e) {
                log_message('error', 'CMS learning events: {msg}', ['msg' => $e->getMessage()]);

                return [];
            }
            $out = [];
            foreach ($rows as $r) {
                $s = strtotime((string) $r['start_date']);
                $e = strtotime((string) $r['end_date']) ?: $s;
                if (! $s) {
                    continue;
                }
                $dates = date('M', $s) === date('M', $e) ? date('j', $s) . '–' . date('j M Y', $e) : date('j M', $s) . ' – ' . date('j M Y', $e);
                $dur   = trim(strip_tags((string) $r['duration']));
                $out[] = [
                    'title'    => trim(html_entity_decode(strip_tags((string) $r['title']), ENT_QUOTES | ENT_HTML5, 'UTF-8')),
                    'mon'      => strtoupper(date('M', $s)),
                    'day'      => date('d', $s),
                    'dates'    => $s === $e ? date('j M Y', $s) : $dates,
                    'duration' => ctype_digit($dur) ? $dur . ' ' . ((int) $dur === 1 ? 'Day' : 'Days') : $dur,
                    'seats'    => ctype_digit(trim((string) $r['seats'])) && (int) $r['seats'] > 0 ? (int) $r['seats'] . ' Seats' : '',
                ];
            }

            return $out;
        });
    }

    /**
     * Published albums, newest event first, each with its cover and photos.
     * Titles like "Mumbai FSP Feb-2016" are split into name + "Feb 2016".
     *
     * @return list<array{id: int, name: string, date: string, cover: string, photos: list<string>}>
     */
    public static function albums(int $limit): array
    {
        return self::remember('cms_learning_albums_' . $limit, static function () use ($limit): array {
            try {
                $db     = db_connect();
                $covers = $db->table('gallery_thumbs')->select('title, image_name, group_id')->where('status', 1)
                    ->orderBy('id', 'DESC')->limit(max(1, min(24, $limit)))->get()->getResultArray();
                $photos = $db->table('gallery')->select('image_name, group_id')->where('status', 1)->orderBy('id', 'ASC')->get()->getResultArray();
            } catch (\Throwable $e) {
                log_message('error', 'CMS learning albums: {msg}', ['msg' => $e->getMessage()]);

                return [];
            }
            $byGroup = [];
            foreach ($photos as $p) {
                if (($u = self::file('learning_gallery', (string) $p['image_name'])) !== '') {
                    $byGroup[(int) $p['group_id']][] = $u;
                }
            }
            $out = [];
            foreach ($covers as $c) {
                $cover = self::webThumb((string) $c['image_name']) ?: self::file('learning_gallery_thumb', (string) $c['image_name']);
                if ($cover === '') {
                    continue;
                }
                $title = trim(html_entity_decode(strip_tags((string) $c['title']), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                $date  = '';
                if (preg_match('/^(.*?)\s+([A-Za-z]{3,9})[- ]?(\d{4})$/', $title, $m) && strtotime('1 ' . $m[2] . ' ' . $m[3])) {
                    // keep the month in one-word names ("FSP July-2016" -> "FSP July"), else two albums read "FSP"
                    $title = str_word_count(trim($m[1])) < 2 ? trim($m[1]) . ' ' . ucfirst(strtolower($m[2])) : trim($m[1]);
                    $date  = date('M Y', strtotime('1 ' . $m[2] . ' ' . $m[3]));
                }
                $out[] = ['id' => (int) $c['group_id'], 'name' => $title, 'date' => $date, 'cover' => $cover, 'photos' => $byGroup[(int) $c['group_id']] ?? []];
            }
            usort($out, static fn ($a, $b) => strtotime('1 ' . ($b['date'] ?: 'Jan 1970')) <=> strtotime('1 ' . ($a['date'] ?: 'Jan 1970')));

            return $out;
        });
    }

    /**
     * 600px WEBP copy of an album cover (the legacy uploads are ~170KB 900px JPGs shown at
     * 285-590px). Made once with GD next to the originals in uploads/learning_gallery_thumb/web/
     * and reused; '' when it cannot be made (the caller falls back to the original).
     */
    private static function webThumb(string $name): string
    {
        if (! preg_match('/^[A-Za-z0-9_-]+\.(jpe?g|png)$/i', $name)) {
            return '';
        }
        $src = FCPATH . 'uploads/learning_gallery_thumb/' . $name;
        $dir = FCPATH . 'uploads/learning_gallery_thumb/web/';
        $out = $dir . pathinfo($name, PATHINFO_FILENAME) . '.webp';
        if (! is_file($out)) {
            if (! is_file($src) || ! function_exists('imagewebp') || filesize($src) > 15_000_000) {
                return '';
            }
            try {
                if (! is_dir($dir) && ! @mkdir($dir, 0755, true)) {
                    return '';
                }
                $im = @imagecreatefromstring((string) file_get_contents($src));
                if ($im === false) {
                    return '';
                }
                if (imagesx($im) > 600) {
                    $im = imagescale($im, 600, (int) round(imagesy($im) * 600 / imagesx($im)), IMG_BICUBIC);
                }
                if (! @imagewebp($im, $out, 78)) {
                    return '';
                }
            } catch (\Throwable) {
                return '';
            }
        }

        return base_url('uploads/learning_gallery_thumb/web/' . basename($out));
    }

    private static function file(string $dir, string $name): string
    {
        return preg_match('/^[A-Za-z0-9_.-]+\.(jpe?g|png|webp)$/i', $name) && is_file(FCPATH . 'uploads/' . $dir . '/' . $name)
            ? base_url('uploads/' . $dir . '/' . $name) : '';
    }

    /** @param callable(): mixed $fn */
    private static function remember(string $key, callable $fn): mixed
    {
        $hit = cache($key);
        if ($hit !== null) {
            return $hit;
        }
        $val = $fn();
        cache()->save($key, $val, 3600);

        return $val;
    }
}
