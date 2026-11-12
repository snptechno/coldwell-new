<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Coldwell Banker India') ?></title>
    <?php if (! empty($metaDescription)): ?>
    <meta name="description" content="<?= esc($metaDescription, 'attr') ?>">
    <?php endif ?>
    <?= csrf_meta() ?>
    <?= $this->renderSection('head') ?>
</head>
<body>
<?php
/*
 * Public layout skeleton (step 3).
 * Step 5 ports the existing header.php / navigation.php / footer.php / sub_footer.php
 * here unchanged visually, replacing CI3's per-view $this->load->view('header') calls.
 * All dynamic output goes through esc() (audit ADM-01 / SEC-06).
 */
?>
<?= $this->renderSection('content') ?>
<?= $this->renderSection('scripts') ?>
</body>
</html>
