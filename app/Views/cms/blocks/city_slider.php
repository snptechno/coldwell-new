<?php
/**
 * Block "city_slider" (Figma 41:41 "Our presence"): heading, city tabs, portrait city cards.
 *
 * @var array<string, string>      $s      block settings
 * @var array<string, list<array>> $items  city
 * @var string                     $sfx
 */
helper('cb');
$cities = $items['city'] ?? [];
$id     = 'cb-presence-title' . $sfx;
$cid    = 'cb-city' . $sfx . '-';
if ($cities === []) {
    return;
}
?>
<section class="cb-presence" aria-labelledby="<?= esc($id, 'attr') ?>" data-carousel data-carousel-tabs>
  <div class="cb-wrap">
    <div class="cb-head cb-reveal">
      <h2 class="cb-h2" id="<?= esc($id, 'attr') ?>"><?= esc(trim($s['title'] ?? '') !== '' ? trim($s['title']) : 'Our Presence Across India') ?></h2>
      <?php if (trim($s['lead'] ?? '') !== ''): ?><p class="cb-p"><?= esc(trim($s['lead'])) ?></p><?php endif ?>
    </div>
    <div class="cb-tabs" role="tablist" aria-label="Cities">
      <?php foreach ($cities as $i => $c): ?>
      <button type="button" class="cb-tab" role="tab" data-go="<?= $i ?>" aria-controls="<?= esc($cid . $i, 'attr') ?>" aria-selected="<?= $i === 0 ? 'true' : 'false' ?>"<?= $i === 0 ? '' : ' tabindex="-1"' ?>><?= esc($c['title']) ?></button>
      <?php endforeach ?>
    </div>
    <ul class="cb-track cb-cities" data-track aria-label="City offices">
      <?php foreach ($cities as $i => $c): ?>
      <li id="<?= esc($cid . $i, 'attr') ?>">
        <a class="cb-city" <?= cb_link_attrs(trim((string) $c['cta_url']) !== '' ? $c['cta_url'] : 'our-franchise') ?>>
          <?= cb_picture($c['image'], (string) $c['image_alt'], ['width' => '896', 'height' => '1200', 'sizes' => '(max-width: 1199px) 300px, 387px']) ?>
          <div><h3><?= esc($c['title']) ?></h3><?php if (trim((string) $c['subtitle']) !== ''): ?><p><?= esc($c['subtitle']) ?></p><?php endif ?></div>
          <div class="cb-city__foot"><span><?= esc((string) $c['body']) ?></span><span class="cb-city__go" aria-hidden="true"><?= cb_icon('diag') ?></span></div>
        </a>
      </li>
      <?php endforeach ?>
    </ul>
    <?= view('cms/partials/slider_controls', ['total' => count($cities), 'label' => 'cities'], ['saveData' => false]) ?>
  </div>
</section>
