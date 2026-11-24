<?php
/**
 * Block "card_grid" (Figma 41:41 "Ways to grow"): soft panel, heading, 3-up photo cards.
 *
 * @var array<string, string>      $s      block settings
 * @var array<string, list<array>> $items  card
 * @var string                     $sfx
 */
helper('cb');
$grow = $items['card'] ?? [];
$id   = 'cb-grow-title' . $sfx;
if ($grow === []) {
    return;
}
?>
<section class="cb-grow" aria-labelledby="<?= esc($id, 'attr') ?>" data-carousel>
  <div class="cb-grow__panel">
    <div class="cb-head cb-reveal">
      <h2 class="cb-h2" id="<?= esc($id, 'attr') ?>"><?= esc(trim($s['title'] ?? '')) ?></h2>
      <?php if (trim($s['lead'] ?? '') !== ''): ?><p class="cb-p"><?= esc(trim($s['lead'])) ?></p><?php endif ?>
    </div>
    <div class="cb-wrap" style="padding:0;max-width:var(--cb-content)">
      <ul class="cb-track cb-grow-track" data-track aria-label="Opportunities">
        <?php foreach ($grow as $g): ?>
        <li class="cb-gcard">
          <div class="cb-gcard__img">
            <?= cb_picture($g['image'], (string) $g['image_alt'], ['width' => '928', 'height' => '1152', 'sizes' => '(max-width: 767px) 280px, (max-width: 1199px) 300px, 363px']) ?>
            <?php if (trim((string) $g['badge']) !== ''): ?><span class="cb-badge<?= stripos((string) $g['badge'], 'flagship') !== false ? ' cb-badge--flagship' : '' ?>"><?= esc($g['badge']) ?></span><?php endif ?>
          </div>
          <div class="cb-gcard__body">
            <?php if (trim((string) $g['kicker']) !== ''): ?><p class="cb-kicker"><?= esc($g['kicker']) ?></p><?php endif ?>
            <h3 class="cb-h3"><?= esc($g['title']) ?></h3>
          </div>
          <div class="cb-gcard__foot">
            <span><?= esc((string) $g['subtitle']) ?></span>
            <?= cb_pill((string) $g['cta_label'], $g['cta_url'], 'light') ?>
          </div>
        </li>
        <?php endforeach ?>
      </ul>
      <?= view('cms/partials/slider_controls', ['total' => count($grow), 'label' => 'opportunities'], ['saveData' => false]) ?>
    </div>
  </div>
</section>
