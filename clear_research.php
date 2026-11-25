<?php
require 'vendor/autoload.php';
$db = \Config\Database::connect();
$db->table('cms_pages')->where('slug', 'research')->delete();
echo 'Page deleted';