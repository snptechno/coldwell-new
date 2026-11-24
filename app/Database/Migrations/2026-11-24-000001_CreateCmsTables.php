<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * CMS for the redesigned site (Figma "Home – Elegant v2" 41:41 and later pages).
 *
 *   cms_pages        pages and articles (home = the row with is_home = 1)
 *   cms_blocks       ordered content blocks of a page; `type` is a key of
 *                    App\Libraries\Cms\BlockTypes, block-level fields live in `settings` (JSON)
 *   cms_block_items  repeatable cards inside a block, grouped by `item_group`
 *                    (e.g. a hero block has "slide" and "stat" items)
 *   cms_menus        one row per menu location (App\Libraries\Cms\MenuLocations)
 *   cms_menu_items   links of a menu, optionally nested one level (parent_id)
 *   cms_settings     site-wide key/value settings (contacts, nav button)
 *
 * See docs/CMS.md.
 */
class CreateCmsTables extends Migration
{
    public function up(): void
    {
        $ts = [
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ];

        $this->forge->addField([
            'id'               => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'type'             => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'page', 'comment' => 'page | article'],
            'title'            => ['type' => 'VARCHAR', 'constraint' => 255],
            'slug'             => ['type' => 'VARCHAR', 'constraint' => 190],
            'is_home'          => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'nav_key'          => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true, 'comment' => 'menu highlight key (page_active)'],
            'excerpt'          => ['type' => 'TEXT', 'null' => true],
            'body'             => ['type' => 'MEDIUMTEXT', 'null' => true, 'comment' => 'sanitised HTML (articles)'],
            'featured_image'   => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'image_alt'        => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'status'           => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0, 'comment' => '0 draft, 1 published'],
            'published_at'     => ['type' => 'DATETIME', 'null' => true],
            'meta_title'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'meta_description' => ['type' => 'VARCHAR', 'constraint' => 320, 'null' => true],
            'meta_keywords'    => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'og_image'         => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'noindex'          => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
        ] + $ts);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('slug');
        $this->forge->addKey(['type', 'status', 'published_at']);
        $this->forge->createTable('cms_pages', true, ['ENGINE' => 'InnoDB']);

        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'page_id'    => ['type' => 'INT', 'unsigned' => true],
            'type'       => ['type' => 'VARCHAR', 'constraint' => 40],
            'label'      => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => true, 'comment' => 'admin-only name'],
            'settings'   => ['type' => 'MEDIUMTEXT', 'null' => true, 'comment' => 'JSON of block fields'],
            'sort_order' => ['type' => 'INT', 'default' => 0],
            'status'     => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
        ] + $ts);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey(['page_id', 'status', 'sort_order']);
        $this->forge->addForeignKey('page_id', 'cms_pages', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('cms_blocks', true, ['ENGINE' => 'InnoDB']);

        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'block_id'   => ['type' => 'INT', 'unsigned' => true],
            'item_group' => ['type' => 'VARCHAR', 'constraint' => 32, 'default' => 'item'],
            'kicker'     => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => true],
            'badge'      => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true],
            'title'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'subtitle'   => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'body'       => ['type' => 'TEXT', 'null' => true],
            'cta_label'  => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true],
            'cta_url'    => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'image'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'image_alt'  => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'sort_order' => ['type' => 'INT', 'default' => 0],
            'status'     => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
        ] + $ts);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey(['block_id', 'item_group', 'status', 'sort_order']);
        $this->forge->addForeignKey('block_id', 'cms_blocks', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('cms_block_items', true, ['ENGINE' => 'InnoDB']);

        $this->forge->addField([
            'id'       => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'location' => ['type' => 'VARCHAR', 'constraint' => 40],
            'name'     => ['type' => 'VARCHAR', 'constraint' => 120],
        ] + $ts);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('location');
        $this->forge->createTable('cms_menus', true, ['ENGINE' => 'InnoDB']);

        $this->forge->addField([
            'id'           => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'menu_id'      => ['type' => 'INT', 'unsigned' => true],
            'parent_id'    => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'label'        => ['type' => 'VARCHAR', 'constraint' => 120],
            'page_id'      => ['type' => 'INT', 'unsigned' => true, 'null' => true, 'comment' => 'link to a CMS page (wins over url)'],
            'url'          => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'comment' => 'site path or http(s) URL'],
            'new_tab'      => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'active_keys'  => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'comment' => 'comma list of page_active keys that highlight this item'],
            'sort_order'   => ['type' => 'INT', 'default' => 0],
            'status'       => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
        ] + $ts);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey(['menu_id', 'parent_id', 'status', 'sort_order']);
        $this->forge->addForeignKey('menu_id', 'cms_menus', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('cms_menu_items', true, ['ENGINE' => 'InnoDB']);

        $this->forge->addField([
            'setting_key' => ['type' => 'VARCHAR', 'constraint' => 64],
            'value'       => ['type' => 'TEXT', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('setting_key');
        $this->forge->createTable('cms_settings', true, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        foreach (['cms_settings', 'cms_menu_items', 'cms_menus', 'cms_block_items', 'cms_blocks', 'cms_pages'] as $t) {
            $this->forge->dropTable($t, true);
        }
    }
}
