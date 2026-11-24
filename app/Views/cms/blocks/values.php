<?php
/**
 * Block "values": centred (eyebrow +) heading and white icon cards.
 *   layout "row"   (About › Our Values): up to 5 cards in one row.
 *   layout "grid3" (Franchise › Why partner with us): 3 columns, first card navy with a gold icon.
 *
 * @var array<string, string>      $s      block settings
 * @var array<string, list<array>> $items  value
 * @var string                     $sfx    id suffix for repeated blocks
 */
helper('cb');
$vals  = $items['value'] ?? [];
$id    = 'cb-values-title' . $sfx;
$icons = \App\Libraries\Cms\BlockTypes::ICONS;
$grid3 = ($s['layout'] ?? 'row') === 'grid3';
?>
<section class="cb-values<?= $grid3 ? ' cb-values--grid3' : '' ?>" aria-labelledby="<?= esc($id, 'attr') ?>">
  <div class="cb-wrap">
    <div class="cb-head cb-reveal">
      <?php if (trim($s['eyebrow'] ?? '') !== ''): ?><p class="cb-eyebrow cb-eyebrow--center"><?= esc(trim($s['eyebrow'])) ?></p><?php endif ?>
      <h2 class="cb-h2" id="<?= esc($id, 'attr') ?>"><?= esc(trim($s['title'] ?? '')) ?></h2>
      <?php if (trim($s['lead'] ?? '') !== ''): ?><p class="cb-p"><?= esc(trim($s['lead'])) ?></p><?php endif ?>
    </div>
    <ul class="cb-values__row" style="--n:<?= $grid3 ? 3 : max(1, min(5, count($vals))) ?>">
      <?php foreach ($vals as $i => $v): $ic = isset($icons[$v['kicker'] ?? '']) ? $v['kicker'] : 'star'; ?>
      <li class="cb-vcard cb-reveal<?= $grid3 && $i === 0 ? ' cb-vcard--feature' : '' ?>">
        <span class="cb-vcard__icon"><?= cb_icon($ic) ?></span>
        <h3><?= esc((string) $v['title']) ?></h3>
        <?php if (trim((string) $v['subtitle']) !== ''): ?><p><?= esc((string) $v['subtitle']) ?></p><?php endif ?>
        <?php if (($pill = cb_pill((string) ($v['cta_label'] ?? ''), $v['cta_url'] ?? '', $grid3 && $i === 0 ? 'gold' : 'light', 'cb-pill--sm')) !== ''): ?><div class="cb-vcard__cta"><?= $pill ?></div><?php endif ?>
      </li>
      <?php endforeach ?>
    </ul>
  </div>
</section>
