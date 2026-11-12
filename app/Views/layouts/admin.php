<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Coldwell Banker - Admin') ?></title>
    <meta name="robots" content="noindex, nofollow">
    <?= csrf_meta() ?>
    <?= $this->renderSection('head') ?>
</head>
<body>
<?php
/*
 * Admin layout skeleton (step 3).
 * Step 6 ports views/admin/_layout_main.php + components/ (AdminLTE 2.3.7) here.
 * Every value echoed from the DB must use esc(): enquiry lists contain
 * attacker-supplied text (audit ADM-01, stored XSS).
 */
?>
<?= $this->renderSection('content') ?>
<?= $this->renderSection('scripts') ?>
</body>
</html>
