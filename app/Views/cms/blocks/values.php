<?php
/**
 * Block "values": centred (eyebrow +) heading and white icon cards.
 *   layout "row"   (About › Our Values): up to 5 cards in one row.
 *   layout "grid3" (Franchise › Why partner with us): 3 columns, first card navy with a gold icon.
 *   background "panel" (NRI › Services for Every Step): the cards sit in a soft inset panel.
 *   A card's body lines (optional) render as a gold-tick checklist under its text.
 *
 * @var array<string, string>      $s      block settings
 * @var array<string, list<array>> $items  value
 * @var string                     $sfx    id suffix for repeated blocks
 */
helper('cb');
$vals  = $items['value'] ?? [];
$id    = 'cb-values-title' . $sfx;
$icons = \App\Libraries\Cms\BlockTypes::ICONS;
$grid4 = ($s['layout'] ?? 'row') === 'grid4';
$grid3 = ($s['layout'] ?? 'row') === 'grid3' || $grid4;
$panel = ($s['background'] ?? 'white') === 'panel';
?>
<section class="cb-values<?= $grid3 ? ' cb-values--grid3' : '' ?><?= $grid4 ? ' cb-values--grid4' : '' ?><?= $panel ? ' cb-values--panel' : '' ?>" aria-labelledby="<?= esc($id, 'attr') ?>">
  <div class="<?= $panel ? 'cb-values__panel' : 'cb-wrap' ?>">
    <div class="cb-head cb-reveal">
      <?php if (trim($s['eyebrow'] ?? '') !== ''): ?><p class="cb-eyebrow cb-eyebrow--center"><?= esc(trim($s['eyebrow'])) ?></p><?php endif ?>
      <h2 class="cb-h2" id="<?= esc($id, 'attr') ?>"><?= esc(trim($s['title'] ?? '')) ?></h2>
      <?php if (trim($s['lead'] ?? '') !== ''): ?><p class="cb-p"><?= esc(trim($s['lead'])) ?></p><?php endif ?>
    </div>
    <ul class="cb-values__row" style="--n:<?= $grid4 ? 4 : ($grid3 ? 3 : max(1, min(5, count($vals)))) ?>">
      <?php foreach ($vals as $i => $v): $ic = isset($icons[$v['kicker'] ?? '']) ? $v['kicker'] : 'star'; ?>
      <li class="cb-vcard cb-reveal<?= $grid3 && $i === 0 ? ' cb-vcard--feature' : '' ?>">
        <span class="cb-vcard__icon"><?= cb_icon($ic) ?></span>
        <h3><?= esc((string) $v['title']) ?></h3>
        <?php if (trim((string) $v['subtitle']) !== ''): ?><p><?= esc((string) $v['subtitle']) ?></p><?php endif ?>
        <?php $checks = array_slice(array_values(array_filter(array_map(static fn ($l) => trim(ltrim(trim($l), '-•')), preg_split('/\R/u', (string) ($v['body'] ?? ''))), 'strlen')), 0, 10); ?>
        <?php if ($checks !== []): ?>
        <ul class="cb-checklist cb-vcard__list">
          <?php foreach ($checks as $c): ?><li><span aria-hidden="true"><?= cb_icon('check') ?></span><?= esc($c) ?></li><?php endforeach ?>
        </ul>
        <?php endif ?>
        <?php if (($pill = cb_pill((string) ($v['cta_label'] ?? ''), $v['cta_url'] ?? '', $grid3 && $i === 0 ? 'gold' : 'light', 'cb-pill--sm')) !== ''): ?><div class="cb-vcard__cta"><?= $pill ?></div><?php endif ?>
      </li>
      <?php endforeach ?>
    </ul>
  </div>
</section>
