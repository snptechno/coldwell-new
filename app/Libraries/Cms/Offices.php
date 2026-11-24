<?php

namespace App\Libraries\Cms;

/**
 * Open franchise offices for the "office_finder" block: the same rows the Our Network page
 * lists (franchises.is_active = 1 AND franchise_status = 'Exits'), with city names.
 * Cached with the CMS cache; never throws (the page renders without the list instead).
 */
final class Offices
{
    /** @return list<array{id: int, name: string, area: string, city: string, phone: string, href: string}> */
    public static function all(): array
    {
        $cached = cache('cms_offices_v2');
        if (is_array($cached)) {
            return $cached;
        }
        try {
            $rows = db_connect()->table('franchises f')
                ->select('f.id, f.title, f.location, f.mobile_no, c.city_name')
                ->join('cities c', 'c.id = f.city', 'left')
                ->where('f.is_active', 1)->where('f.franchise_status', 'Exits')
                ->orderBy('c.city_name', 'ASC')->orderBy('f.title', 'ASC')
                ->get()->getResultArray();
        } catch (\Throwable $e) {
            log_message('error', 'CMS offices: {msg}', ['msg' => $e->getMessage()]);

            return [];
        }
        $out = [];
        foreach ($rows as $r) {
            $name = trim(html_entity_decode(strip_tags((string) $r['title']), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            $city = trim(str_replace(['(NCR)', '  '], ['NCR', ' '], (string) $r['city_name']));
            $slug = trim((string) preg_replace('/[^a-z0-9]+/', '-', strtolower($name)), '-');
            $out[] = [
                'id'   => (int) $r['id'],
                'name' => $name,
                'area' => trim(html_entity_decode(strip_tags((string) $r['location']), ENT_QUOTES | ENT_HTML5, 'UTF-8')),
                'city' => $city !== '' ? $city : 'Other',
                'phone' => trim((string) preg_replace('/\s+/', ' ', str_replace('+ ', '+', strip_tags((string) $r['mobile_no'])))),
                'href' => site_url('our-franchise-details/coldwell-banker-' . ($slug ?: 'office') . '/' . (int) $r['id']),
            ];
        }
        cache()->save('cms_offices_v2', $out, 3600);

        return $out;
    }

    /** @return array<string, int> city => office count, busiest first */
    public static function cities(array $offices): array
    {
        $c = [];
        foreach ($offices as $o) {
            $c[$o['city']] = ($c[$o['city']] ?? 0) + 1;
        }
        arsort($c);

        return $c;
    }
}
