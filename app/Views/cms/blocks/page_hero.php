<?php
/**
 * Block "page_hero": compact version of the home hero card for inner pages — the site
 * header inside, one photo, eyebrow, H1 (display-l), lead and an optional button.
 *
 * @var array<string, string> $s  block settings
 * @var string                $page_active
 * @var string                $current_url
 */
helper('cb');
$img = cb_picture($s['image'] ?? '', '', ['loading' => 'eager', 'fetchpriority' => 'high', 'width' => '1536', 'height' => '864']);
?>
<div class="cb-hero">
  <div class="cb-hero__card cb-hero__card--page">
    <?= view('cms/partials/nav', ['page_active' => $page_active, 'current_url' => $current_url], ['saveData' => false]) ?>

    <?php if ($img !== ''): ?>
    <div class="cb-hero__slides" aria-hidden="true">
      <div class="cb-slide is-active">
        <div class="cb-slide__img"><?= $img ?></div>
        <div class="cb-slide__shade"></div>
      </div>
    </div>
    <?php endif ?>

    <div class="cb-hero__body" id="main" tabindex="-1">
      <div class="cb-phero__copy">
        <?php if (trim($s['eyebrow'] ?? '') !== ''): ?><p class="cb-eyebrow"><span><?= esc(trim($s['eyebrow'])) ?></span></p><?php endif ?>
        <h1 class="cb-display-l cb-pre"><?= esc(trim($s['title'] ?? '')) ?></h1>
        <?php if (trim($s['lead'] ?? '') !== ''): ?><p class="cb-lead"><?= esc(trim($s['lead'])) ?></p><?php endif ?>
        <?php if (($pill = cb_pill($s['cta_label'] ?? '', $s['cta_url'] ?? '', 'gold')) !== ''): ?>
        <div class="cb-hero__ctas"><?= $pill ?></div>
        <?php endif ?>
      </div>
    </div>
  </div>
</div>
