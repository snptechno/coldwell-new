<?php
/**
 * Block "leaders" (Figma About 73:58 "Our Leadership"): heading row + button, featured first
 * person (wide photo on navy, gold role eyebrow, bio), then a 3-column grid of the others.
 *
 * @var array<string, string>      $s      block settings
 * @var array<string, list<array>> $items  person
 * @var string                     $sfx    id suffix for repeated blocks
 */
helper('cb');
$people   = $items['person'] ?? [];
$featured = array_shift($people);
$id       = 'cb-leaders-title' . $sfx;
?>
<section class="cb-leaders" aria-labelledby="<?= esc($id, 'attr') ?>">
  <div class="cb-wrap">
    <div class="cb-head cb-head--split cb-reveal">
      <div>
        <h2 class="cb-h2" id="<?= esc($id, 'attr') ?>"><?= esc(trim($s['title'] ?? '')) ?></h2>
        <?php if (trim($s['lead'] ?? '') !== ''): ?><p class="cb-p"><?= esc(trim($s['lead'])) ?></p><?php endif ?>
      </div>
      <?= cb_pill($s['cta_label'] ?? '', $s['cta_url'] ?? '', 'light', 'cb-pill--on-section') ?>
    </div>

    <?php if ($featured): ?>
    <article class="cb-leader-feat cb-reveal">
      <div class="cb-leader-feat__img"><?= cb_picture($featured['image'], (string) ($featured['image_alt'] ?: $featured['title']), ['width' => '1323', 'height' => '487']) ?></div>
      <div class="cb-leader-feat__copy">
        <p class="cb-eyebrow cb-eyebrow--gold"><?= esc((string) $featured['subtitle']) ?></p>
        <h3><?= esc((string) $featured['title']) ?></h3>
        <?php if (trim((string) $featured['body']) !== ''): ?><p><?= esc(trim((string) $featured['body'])) ?></p><?php endif ?>
      </div>
    </article>
    <?php endif ?>

    <?php if ($people !== []): ?>
    <ul class="cb-leader-grid">
      <?php foreach ($people as $p): ?>
      <li class="cb-leader cb-reveal">
        <div class="cb-leader__img"><?= cb_picture($p['image'], (string) ($p['image_alt'] ?: $p['title']), ['width' => '661', 'height' => '519']) ?></div>
        <h3><?= esc((string) $p['title']) ?></h3>
        <p><?= esc((string) $p['subtitle']) ?></p>
      </li>
      <?php endforeach ?>
    </ul>
    <?php endif ?>
  </div>
</section>
