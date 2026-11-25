<?php
helper('cb');
$asset = static fn (string $f): string => base_url($f) . '?v=' . @filemtime(FCPATH . $f);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>404 — Page Not Found | Coldwell Banker India</title>
  <meta name="robots" content="noindex, nofollow">
  <meta name="theme-color" content="#0F1F3D">
  <link rel="shortcut icon" href="<?= esc(base_url('assets/images/favicon.ico'), 'attr') ?>">
  <link rel="stylesheet" href="<?= esc($asset('assets/css/fonts.css'), 'attr') ?>">
  <link rel="stylesheet" href="<?= esc($asset('assets/css/cb-site.css'), 'attr') ?>">
</head>
<body class="cb-page">
<?= view('cms/partials/header_bar', ['page_active' => '', 'current_url' => current_url()], ['saveData' => false]) ?>
<main id="main" tabindex="-1">
  <section class="cb-err404" aria-labelledby="cb-err404-title">
    <div class="cb-err404__panel">
      <p class="cb-eyebrow cb-eyebrow--center">ERROR 404</p>
      <p class="cb-err404__code" aria-hidden="true">404</p>
      <h1 class="cb-h2" id="cb-err404-title">Page Not Found</h1>
      <p class="cb-p cb-err404__lead">
        <?php if (ENVIRONMENT !== 'production' && ! empty($message)) : ?>
          <?= nl2br(esc($message)) ?>
        <?php else : ?>
          The page you are looking for may have been moved, renamed, or is temporarily unavailable.
        <?php endif; ?>
      </p>
      <div class="cb-err404__actions">
        <a href="<?= esc(site_url('/'), 'attr') ?>" class="cb-pill cb-pill--gold">
          <span>Back to Home</span><span class="cb-pill__icon"><?= cb_icon('arrow') ?></span>
        </a>
        <a href="<?= esc(site_url('our-franchise'), 'attr') ?>" class="cb-pill cb-pill--light cb-pill--on-section">
          <span>Explore Our Network</span><span class="cb-pill__icon"><?= cb_icon('arrow') ?></span>
        </a>
      </div>
    </div>
  </section>
</main>
<?= view('cms/partials/footer', [], ['saveData' => false]) ?>
<script src="<?= esc($asset('assets/js/cb-site.js'), 'attr') ?>" defer></script>
</body>
</html>
