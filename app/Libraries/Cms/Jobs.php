<?php

namespace App\Libraries\Cms;

/**
 * Open jobs (Admin > Job Openings) for the careers CMS blocks, grouped by type. The legacy
 * descriptions are messy HTML ("·&nbsp;&nbsp;" pseudo-bullets, empty <p>s); duties are
 * extracted as clean text bullets. Cached, never throws.
 */
final class Jobs
{
    /** Job type value => tab label. */
    public const TYPES = ['COLDWELL BANKER INDIA OPENINGS' => 'Coldwell Banker India', 'FRANCHISEES OPENINGS' => 'Franchise Openings'];

    /** @return array<string, list<array{id: int, title: string, qualification: string, exp: string, location: string, duties: list<string>, href: string}>> */
    public static function open(): array
    {
        $hit = cache('cms_jobs_open');
        if (is_array($hit)) {
            return $hit;
        }
        try {
            $rows = db_connect()->table('jobs')->select('id, type, position, qualification, min_experience, max_experience, location, description')
                ->where('status', 1)->orderBy('id', 'DESC')->get()->getResultArray();
        } catch (\Throwable $e) {
            log_message('error', 'CMS jobs: {msg}', ['msg' => $e->getMessage()]);

            return [];
        }
        $out = [];
        foreach ($rows as $r) {
            $label = self::TYPES[strtoupper(trim((string) $r['type']))] ?? 'Other Openings';
            $min   = (int) $r['min_experience'];
            $max   = (int) $r['max_experience'];
            $exp   = $min > 0 && $max > $min ? $min . '–' . $max . ' yrs' : ($min > 0 ? $min . '+ yrs' : '');
            $title = self::text((string) $r['position']);
            $out[$label][] = [
                'id'            => (int) $r['id'],
                'title'         => (string) preg_replace('/\s+-\s+/u', ' – ', $title),
                'qualification' => self::firstLine((string) $r['qualification']),
                'exp'           => $exp,
                'location'      => self::location((string) $r['location']),
                'duties'        => self::duties((string) $r['description'], 8),
                'href'          => site_url('apply-job-opening/' . (int) $r['id']),
            ];
        }
        $ordered = [];
        foreach (self::TYPES as $label) {
            if (isset($out[$label])) {
                $ordered[$label] = $out[$label];
            }
        }
        $ordered += $out;
        cache()->save('cms_jobs_open', $ordered, 3600);

        return $ordered;
    }

    /** Clean bullet texts from legacy description HTML. */
    public static function duties(string $html, int $max): array
    {
        // source HTML is hard-wrapped: collapse raw newlines first, then split only on block tags
        $html = (string) preg_replace('/\s+/u', ' ', $html);
        $html = (string) preg_replace('#<(br|/p|/li|/div|/h\d)\b[^>]*>#i', "\n", $html);
        $out  = [];
        foreach (preg_split('/\R/u', html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8')) as $line) {
            $line = trim((string) preg_replace('/[\s\x{00A0}]+/u', ' ', $line));
            $line = trim((string) preg_replace('/^[·•\-*–]\s*/u', '', $line));
            // skip headings ("Business Development:", "ESSENTIAL DUTIES & RESPONSIBILITIES:") and fragments
            if (mb_strlen($line) < 12 || str_ends_with($line, ':')) {
                continue;
            }
            $out[] = mb_strlen($line) > 180 ? rtrim(mb_substr($line, 0, 179)) . '…' : $line;
            if (count($out) >= $max) {
                break;
            }
        }

        return $out;
    }

    private static function text(string $s): string
    {
        return trim((string) preg_replace('/[\s\x{00A0}]+/u', ' ', html_entity_decode(strip_tags($s), ENT_QUOTES | ENT_HTML5, 'UTF-8')));
    }

    private static function firstLine(string $html): string
    {
        $parts = array_values(array_filter(array_map([self::class, 'text'], preg_split('#<(br|/p|/li)\b[^>]*>#i', $html)), 'strlen'));
        $first = $parts[0] ?? '';
        $first = (string) preg_replace('/^Graduation:\s*Any degree$/i', 'Graduation (any degree)', $first);

        return mb_strlen($first) > 60 ? rtrim(mb_substr($first, 0, 59)) . '…' : $first;
    }

    private static function location(string $s): string
    {
        $s = self::text($s);
        if (preg_match('/^(.+?)\s+(west|east)\s+(mumbai)$/i', $s, $m)) {
            return ucwords(strtolower($m[1] . ' ' . $m[2])) . ', ' . ucfirst(strtolower($m[3]));
        }

        return $s;
    }
}
