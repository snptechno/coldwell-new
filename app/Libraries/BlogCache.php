<?php

namespace App\Libraries;

/**
 * Short-lived cache for blog database results (not rendered HTML).
 *
 * HTML is not cached because every page carries a per-visitor CSRF token in the
 * footer newsletter form; a cached page would hand one visitor's token to all.
 * Caching the query results removes the database work while the page is still
 * rendered per request.
 *
 * - Entries live for TTL seconds, so a post scheduled for a future time appears
 *   within TTL seconds of that time.
 * - Admin\Blog and the Drupal import call clear() after every write, so edits show
 *   immediately.
 * - View counts use the separate "blogviews_" prefix, so clear() never drops them.
 */
final class BlogCache
{
    public const TTL = 300;

    private const PREFIX       = 'blog_';
    private const VIEWS_PREFIX = 'blogviews_';
    /** Pending views are written to the database once a post reaches this many. */
    public const VIEWS_FLUSH_AT = 20;

    /**
     * @template T
     *
     * @param callable(): T $fn
     *
     * @return T
     */
    public static function remember(string $key, callable $fn): mixed
    {
        // ENVIRONMENT in the key: PHPUnit (testing DB) and the site share writable/cache,
        // so test rows must never be served to the site, or the other way round.
        return cache()->remember(self::PREFIX . md5(ENVIRONMENT . ':' . $key), self::TTL, $fn);
    }

    public static function clear(): void
    {
        cache()->deleteMatching(self::PREFIX . '*');
    }

    /**
     * Counts a post view without a database write on every request.
     * Views are buffered in the cache and flushed in batches of VIEWS_FLUSH_AT.
     * Under heavy concurrency a few views may be lost; the counter is approximate.
     *
     * @param callable(int): void $flush receives the number of views to add
     */
    public static function countView(int $postId, callable $flush): void
    {
        // Counting is best effort and must never break the page: two visitors can hit the
        // flush at the same moment, and the file cache is not atomic.
        try {
            $key = self::VIEWS_PREFIX . substr(md5(ENVIRONMENT), 0, 6) . '_' . $postId;
            $n   = (int) (cache()->get($key) ?? 0) + 1;
            if ($n >= self::VIEWS_FLUSH_AT) {
                cache()->save($key, 0, 86400); // overwrite, not delete: a concurrent delete fails on a missing file
                $flush($n);

                return;
            }
            cache()->save($key, $n, 86400);
        } catch (\Throwable $e) {
            log_message('warning', 'Blog view counter skipped: ' . $e->getMessage());
        }
    }
}
