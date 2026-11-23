<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Blog module (replaces the Drupal 7 install under /blogs).
 *
 * blog_categories and blog_posts are new tables owned by CI4. The legacy_* columns
 * keep the Drupal node/term id and URL alias so the importer is idempotent and old
 * /blogs/... URLs can 301 to the new ones. Drupal did not use tags on blog posts,
 * so there is no tags table.
 */
class CreateBlogTables extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'                 => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'name'               => ['type' => 'VARCHAR', 'constraint' => 150],
            'slug'               => ['type' => 'VARCHAR', 'constraint' => 190],
            'description'        => ['type' => 'TEXT', 'null' => true],
            'meta_title'         => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'meta_description'   => ['type' => 'VARCHAR', 'constraint' => 320, 'null' => true],
            'status'             => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'sort_order'         => ['type' => 'INT', 'default' => 0],
            'legacy_drupal_tid'  => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'legacy_alias'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at'         => ['type' => 'DATETIME', 'null' => true],
            'updated_at'         => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('slug');
        $this->forge->addUniqueKey('legacy_drupal_tid');
        $this->forge->addKey('legacy_alias');
        $this->forge->createTable('blog_categories', true, ['ENGINE' => 'InnoDB']);

        $this->forge->addField([
            'id'                 => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'title'              => ['type' => 'VARCHAR', 'constraint' => 255],
            'slug'               => ['type' => 'VARCHAR', 'constraint' => 190],
            'excerpt'            => ['type' => 'TEXT', 'null' => true],
            'content'            => ['type' => 'MEDIUMTEXT', 'null' => true],
            'featured_image'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'image_alt'          => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'category_id'        => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'author'             => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'status'             => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0, 'comment' => '0 = draft, 1 = published'],
            'published_at'       => ['type' => 'DATETIME', 'null' => true],
            'meta_title'         => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'meta_description'   => ['type' => 'VARCHAR', 'constraint' => 320, 'null' => true],
            'meta_keywords'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'canonical_url'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'og_image'           => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'noindex'            => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'legacy_drupal_nid'  => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'legacy_alias'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'views'              => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
            'created_at'         => ['type' => 'DATETIME', 'null' => true],
            'updated_at'         => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('slug');
        $this->forge->addUniqueKey('legacy_drupal_nid');
        $this->forge->addKey(['status', 'published_at']);
        $this->forge->addKey('category_id');
        $this->forge->addKey('legacy_alias');
        $this->forge->createTable('blog_posts', true, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('blog_posts', true);
        $this->forge->dropTable('blog_categories', true);
    }
}
