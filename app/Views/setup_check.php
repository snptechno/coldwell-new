<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>
<main style="font-family: sans-serif; max-width: 640px; margin: 40px auto; padding: 0 16px;">
    <h1>Coldwell Banker India &mdash; CodeIgniter 4</h1>
    <p>Step 3 environment check. This page is replaced when the public site is ported.</p>
    <?php if ($check === null): ?>
        <p>Diagnostics are hidden in production.</p>
    <?php elseif (isset($check['error'])): ?>
        <p><strong>Database error:</strong> <?= esc($check['error']) ?></p>
    <?php else: ?>
        <table cellpadding="6">
            <tr><th align="left">PHP</th><td><?= esc($check['php']) ?></td></tr>
            <tr><th align="left">CodeIgniter</th><td><?= esc($check['ci']) ?></td></tr>
            <tr><th align="left">Database server</th><td><?= esc($check['db']) ?></td></tr>
            <tr><th align="left">Database</th><td><?= esc($check['database']) ?> (<?= esc($check['tables']) ?> tables)</td></tr>
            <tr><th align="left">Sample query</th><td>cities: <?= esc($check['cities']) ?> rows</td></tr>
        </table>
    <?php endif ?>
</main>
<?= $this->endSection() ?>
