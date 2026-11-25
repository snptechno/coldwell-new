<?php
require 'vendor/autoload.php';
$dotenv = \CodeIgniter\Config\DotEnv::createImmutable(FCPATH);
$dotenv->load();
$kernel = new \CodeIgniter\Boot\Booter();
$kernel->boot();

$db = db_connect();
$page = $db->table('cms_pages')->where('slug', 'research')->get()->getRow();
echo 'Page: ' . ($page ? $page->id . ' - ' . $page->title : 'NOT FOUND') . PHP_EOL;
if ($page) {
    $blocks = $db->table('cms_blocks')->where('page_id', $page->id)->get()->getResult();
    echo 'Blocks: ' . count($blocks) . PHP_EOL;
    foreach ($blocks as $b) {
        echo '  - ' . $b->type . ' (' . $b->label . ')' . PHP_EOL;
    }
}