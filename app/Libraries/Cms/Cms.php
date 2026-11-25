<?php

namespace App\Libraries\Cms;

use App\Models\CmsSettingsModel;

/**
 * Read side of the CMS for public pages: pages with their blocks and items, menus and
 * settings. Results are cached (file cache, prefix "cms_"); every admin save calls
 * Cms::clearCache(). See docs/CMS.md.
 */
final class Cms
{
    private const TTL = 3600;

    /** @var array<string, mixed> per-request memo */
    private static array $memo = [];

    public static function clearCache(): void
    {
        self::$memo = [];
        try {
            cache()->deleteMatching('cms_*');
        } catch (\Throwable) {
            // a cache backend without deleteMatching: entries expire after TTL
        }
    }

    /**
     * Published page by slug (or the home page when $slug is null), with active blocks
     * and their active items grouped: ['page' => row, 'blocks' => [row + 'settings' => [...], 'items' => [group => rows]]].
     * Returns null when not found / not published (or published in the future).
     */
    public static function page(?string $slug, bool $preview = false): ?array
    {
        $key = 'cms_page_' . ($slug === null ? '__home' : md5($slug)) . ($preview ? '_p' : '');

        return self::remember($key, static function () use ($slug, $preview): ?array {
            $db = db_connect();
            $b  = $db->table('cms_pages');
            $slug === null ? $b->where('is_home', 1) : $b->where('slug', $slug);
            if (! $preview) {
                $b->where('status', 1)->groupStart()->where('published_at IS NULL')->orWhere('published_at <=', date('Y-m-d H:i:s'))->groupEnd();
            }
            $page = $b->get(1)->getRowArray();
            if (! $page) {
                return null;
            }

            $bq = $db->table('cms_blocks')->where('page_id', $page['id']);
            if (! $preview) {
                $bq->where('status', 1);
            }
            $blocks = $bq->orderBy('sort_order', 'ASC')->orderBy('id', 'ASC')->get()->getResultArray();
            $blocks = array_values(array_filter($blocks, static fn ($r) => BlockTypes::has($r['type'])));

            $items = [];
            if ($blocks !== []) {
                $rows = $db->table('cms_block_items')->whereIn('block_id', array_column($blocks, 'id'))->where('status', 1)
                    ->orderBy('sort_order', 'ASC')->orderBy('id', 'ASC')->get()->getResultArray();
                foreach ($rows as $r) {
                    $items[$r['block_id']][$r['item_group']][] = $r;
                }
            }
            foreach ($blocks as &$blk) {
                $blk['settings'] = BlockTypes::settings($blk['type'], $blk['settings']);
                $blk['items']    = $items[$blk['id']] ?? [];
            }
            unset($blk);

            return ['page' => $page, 'blocks' => $blocks];
        }, ! $preview);
    }

    /**
     * Menu tree for a location: list of ['label','href','new_tab','active_keys'=>[],'children'=>[...]].
     * page_id links resolve to the page's current URL; unpublished pages drop out.
     *
     * @return list<array<string, mixed>>
     */
    public static function menu(string $location): array
    {
        $all = self::remember('cms_menus', static function (): array {
            $db    = db_connect();
            $rows  = $db->table('cms_menu_items mi')
                ->select('mi.*, m.location, p.slug AS page_slug, p.is_home AS page_is_home, p.status AS page_status')
                ->join('cms_menus m', 'm.id = mi.menu_id')
                ->join('cms_pages p', 'p.id = mi.page_id', 'left')
                ->where('mi.status', 1)
                ->orderBy('mi.sort_order', 'ASC')->orderBy('mi.id', 'ASC')
                ->get()->getResultArray();
            $nodes = [];
            foreach ($rows as $r) {
                if ($r['page_id'] !== null) {
                    if ((int) $r['page_status'] !== 1) {
                        continue;
                    }
                    $href = (int) $r['page_is_home'] === 1 ? site_url('/') : site_url($r['page_slug']);
                } else {
                    $href = CmsUrl::href($r['url']);
                    if ($href === '') {
                        continue;
                    }
                }
                $nodes[$r['id']] = [
                    'id'          => (int) $r['id'],
                    'parent_id'   => $r['parent_id'] !== null ? (int) $r['parent_id'] : null,
                    'location'    => $r['location'],
                    'label'       => (string) $r['label'],
                    'href'        => $href,
                    'new_tab'     => (int) $r['new_tab'] === 1 || CmsUrl::isExternal($href),
                    'active_keys' => array_values(array_filter(array_map('trim', explode(',', (string) $r['active_keys'])), 'strlen')),
                    'children'    => [],
                ];
            }
            $tree = [];
            foreach ($nodes as $id => $n) {
                if ($n['parent_id'] !== null && isset($nodes[$n['parent_id']])) {
                    $nodes[$n['parent_id']]['children'][] = &$nodes[$id];
                }
            }
            foreach ($nodes as $id => $n) {
                if ($n['parent_id'] === null || ! isset($nodes[$n['parent_id']])) {
                    $tree[$n['location']][] = &$nodes[$id];
                }
            }

            return json_decode(json_encode($tree), true) ?: []; // break references before caching
        });

        return $all[$location] ?? [];
    }

    /** @return array<string, string> */
    public static function settings(): array
    {
        return self::remember('cms_settings', static fn (): array => model(CmsSettingsModel::class)->map());
    }

    public static function setting(string $key, string $default = ''): string
    {
        $v = trim(self::settings()[$key] ?? '');

        return $v !== '' ? $v : $default;
    }

    /** @param callable(): mixed $fn */
    private static function remember(string $key, callable $fn, bool $persist = true): mixed
    {
        if (array_key_exists($key, self::$memo)) {
            return self::$memo[$key];
        }
        $hit = $persist ? cache($key) : null;
        if ($hit === null) {
            $hit = $fn();
            if ($persist && $hit !== null) {
                cache()->save($key, $hit, self::TTL);
            }
        }

        return self::$memo[$key] = $hit;
    }
}
