<?php

namespace App\Commands;

use App\Libraries\BlogContent;
use App\Models\BlogCategoriesModel;
use App\Models\BlogPostsModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;

/**
 * php spark blog:import-drupal [--host 127.0.0.1] [--port 3306] [--db coldwellblog] [--user root] [--password ""]
 *                              [--files C:/xampp/htdocs/coldwell/blogs/sites/default/files] [--dry-run]
 *
 * Imports the Drupal 7 "blog" nodes and the "Categories" vocabulary into blog_posts /
 * blog_categories. The Drupal DB is only read (SELECT). Idempotent: rows are matched on
 * legacy_drupal_nid / legacy_drupal_tid and updated in place, so it can be re-run.
 * Featured images are copied into public/uploads/blog/.
 */
class BlogImportDrupal extends BaseCommand
{
    protected $group       = 'Blog';
    protected $name        = 'blog:import-drupal';
    protected $description = 'Import blog posts and categories from the legacy Drupal 7 database (read-only).';
    protected $usage       = 'blog:import-drupal [options]';
    protected $options     = [
        '--host'     => 'Drupal DB host (default env drupal.hostname or 127.0.0.1)',
        '--port'     => 'Drupal DB port (default 3306)',
        '--db'       => 'Drupal DB name (default coldwellblog)',
        '--user'     => 'Drupal DB user (default root)',
        '--password' => 'Drupal DB password (default empty)',
        '--files'    => 'Drupal public files directory to copy images from',
        '--dry-run'  => 'Show what would happen, write nothing',
        '--force'    => 'Re-apply Drupal data to posts imported before (overwrites title/content/image edits made in admin)',
    ];

    public function run(array $params)
    {
        $opt = static fn (string $k, string $env, string $def) => (string) (CLI::getOption($k) ?? env($env, $def));
        $dry   = CLI::getOption('dry-run') !== null || in_array('--dry-run', $_SERVER['argv'] ?? [], true);
        $force = CLI::getOption('force') !== null || in_array('--force', $_SERVER['argv'] ?? [], true);

        $drupal = Database::connect([
            'DSN'      => '',
            'hostname' => $opt('host', 'drupal.hostname', '127.0.0.1'),
            'port'     => (int) $opt('port', 'drupal.port', '3306'),
            'database' => $opt('db', 'drupal.database', 'coldwellblog'),
            'username' => $opt('user', 'drupal.username', 'root'),
            'password' => $opt('password', 'drupal.password', ''),
            'DBDriver' => 'MySQLi',
            'DBPrefix' => '',
            'charset'  => 'utf8mb4',
            'DBCollat' => 'utf8mb4_general_ci',
            'DBDebug'  => true,
        ], false);
        $filesDir = rtrim(str_replace('\\', '/', $opt('files', 'drupal.filesDir', ROOTPATH . '../coldwell/blogs/sites/default/files')), '/');

        $catModel  = new BlogCategoriesModel();
        $postModel = new BlogPostsModel();
        $stats     = ['cat_new' => 0, 'cat_upd' => 0, 'post_new' => 0, 'post_upd' => 0, 'post_skip' => 0, 'img_copied' => 0, 'img_missing' => 0];

        // ---- categories -------------------------------------------------
        $terms = $drupal->query(
            "SELECT t.tid, t.name, t.description, t.weight, ua.alias
               FROM taxonomy_term_data t
               JOIN taxonomy_vocabulary v ON v.vid = t.vid AND v.name = 'Categories'
          LEFT JOIN url_alias ua ON ua.source = CONCAT('taxonomy/term/', t.tid)
           ORDER BY t.weight, t.tid"
        )->getResultArray();

        $catIdByTid = [];
        foreach ($terms as $t) {
            $alias = $t['alias'] ?: null;
            $slug  = $alias ? BlogContent::slugify(basename($alias)) : BlogContent::slugify($t['name']);
            $row   = [
                'name'              => html_entity_decode($t['name'], ENT_QUOTES, 'UTF-8'),
                'description'       => trim(strip_tags((string) $t['description'])) ?: null,
                'sort_order'        => (int) $t['weight'],
                'legacy_drupal_tid' => (int) $t['tid'],
                'legacy_alias'      => $alias,
                'status'            => 1,
            ];
            $existing = $catModel->byLegacyTid((int) $t['tid']);
            if ($existing) {
                $catIdByTid[(int) $t['tid']] = (int) $existing['id'];
                $stats['cat_upd']++;
                if (! $dry) {
                    unset($row['status']); // keep admin's choice
                    $catModel->update((int) $existing['id'], $row);
                }
            } else {
                $base = $slug !== '' ? $slug : 'category';
                $s = $base;
                for ($i = 2; $catModel->slugExists($s); $i++) {
                    $s = $base . '-' . $i;
                }
                $row['slug'] = $s;
                $stats['cat_new']++;
                if (! $dry) {
                    $catIdByTid[(int) $t['tid']] = (int) $catModel->insert($row);
                }
            }
        }

        // ---- posts ------------------------------------------------------
        $nodes = $drupal->query(
            "SELECT n.nid, n.title, n.status, n.created, n.changed, u.name AS author,
                    b.body_value, b.body_summary, b.body_format,
                    fm.uri AS image_uri, fi.field_images_alt AS image_alt,
                    (SELECT fc.field_categories_tid FROM field_data_field_categories fc
                      WHERE fc.entity_type = 'node' AND fc.entity_id = n.nid AND fc.deleted = 0 ORDER BY fc.delta LIMIT 1) AS tid,
                    (SELECT ua.alias FROM url_alias ua WHERE ua.source = CONCAT('node/', n.nid) ORDER BY ua.pid DESC LIMIT 1) AS alias
               FROM node n
          LEFT JOIN users u ON u.uid = n.uid
          LEFT JOIN field_data_body b ON b.entity_type = 'node' AND b.entity_id = n.nid AND b.deleted = 0 AND b.delta = 0
          LEFT JOIN field_data_field_images fi ON fi.entity_type = 'node' AND fi.entity_id = n.nid AND fi.deleted = 0 AND fi.delta = 0
          LEFT JOIN file_managed fm ON fm.fid = fi.field_images_fid
              WHERE n.type = 'blog'
           ORDER BY n.nid"
        )->getResultArray();

        $uploadDir = rtrim(FCPATH, '\\/') . '/uploads/blog';
        if (! $dry && ! is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        foreach ($nodes as $n) {
            $nid   = (int) $n['nid'];
            $alias = $n['alias'] ?: null;

            // Featured image: public://foo.jpg -> uploads/blog/foo.jpg
            $image = null;
            if (! empty($n['image_uri']) && str_starts_with($n['image_uri'], 'public://')) {
                $rel = substr($n['image_uri'], 9);
                $src = $filesDir . '/' . $rel;
                $image = basename($rel);
                if (is_file($src)) {
                    if (! $dry && (! is_file($uploadDir . '/' . $image) || filesize($uploadDir . '/' . $image) !== filesize($src))) {
                        copy($src, $uploadDir . '/' . $image);
                    }
                    $stats['img_copied']++;
                } else {
                    $stats['img_missing']++;
                    CLI::write("  image missing for nid {$nid}: {$src}", 'yellow');
                    $image = null;
                }
            }

            $body = BlogContent::autoParagraph((string) $n['body_value']);
            // Inline Drupal file paths -> new upload folder (none in the 2017 data, kept for safety).
            $body = (string) preg_replace('#(?:https?://[^"\']+)?/?blogs/sites/default/files/(?:[^"\'/]+/)*([^"\'/]+)#i', '/uploads/blog/$1', $body);
            $body = BlogContent::sanitize($body);

            $summary = trim(strip_tags((string) $n['body_summary']));
            $excerpt = $summary !== '' ? BlogContent::excerpt($summary, 300) : BlogContent::excerpt($body, 300);
            $title   = html_entity_decode($n['title'], ENT_QUOTES, 'UTF-8');

            $row = [
                'title'             => $title,
                'excerpt'           => $excerpt,
                'content'           => $body,
                'featured_image'    => $image,
                'image_alt'         => trim((string) $n['image_alt']) ?: $title,
                'category_id'       => $n['tid'] !== null ? ($catIdByTid[(int) $n['tid']] ?? null) : null,
                'author'            => in_array(strtolower((string) $n['author']), ['', 'admin', 'administrator'], true) ? 'Coldwell Banker India' : $n['author'],
                'status'            => (int) $n['status'] === 1 ? 1 : 0,
                'published_at'      => date('Y-m-d H:i:s', (int) $n['created']),
                'meta_title'        => mb_substr($title, 0, 70),
                'meta_description'  => BlogContent::excerpt($summary !== '' ? $summary : $body, 160),
                'legacy_drupal_nid' => $nid,
                'legacy_alias'      => $alias,
            ];

            $existing = $postModel->byLegacyNid($nid);
            if ($existing && ! $force) {
                $stats['post_skip']++;
                CLI::write("  skipped  #{$nid} (already imported as blog/{$existing['slug']}; use --force to overwrite)");

                continue;
            }
            if ($existing) {
                $stats['post_upd']++;
                if (! $dry) {
                    // Do not overwrite what an editor may have changed since the first import.
                    foreach (['status', 'meta_title', 'meta_description'] as $keep) {
                        if ((string) ($existing[$keep] ?? '') !== '') {
                            unset($row[$keep]);
                        }
                    }
                    $postModel->updatePost((int) $existing['id'], $row);
                }
                CLI::write("  updated  #{$nid} -> blog/{$existing['slug']}");
            } else {
                $wanted      = $alias ? basename($alias) : $title;
                $row['slug'] = $postModel->uniqueSlug($wanted);
                $stats['post_new']++;
                if (! $dry) {
                    $row['created_at'] = date('Y-m-d H:i:s', (int) $n['created']);
                    $postModel->insert($row);
                }
                CLI::write("  imported #{$nid} -> blog/{$row['slug']}", 'green');
            }
        }

        CLI::newLine();
        CLI::write(sprintf(
            '%sCategories: %d new, %d updated. Posts: %d new, %d updated, %d skipped. Images: %d copied, %d missing.',
            $dry ? '[dry run] ' : '',
            $stats['cat_new'], $stats['cat_upd'], $stats['post_new'], $stats['post_upd'], $stats['post_skip'], $stats['img_copied'], $stats['img_missing']
        ), 'green');
        if (! $dry) {
            \App\Libraries\BlogCache::clear(); // new/updated posts show immediately
        }
    }
}
