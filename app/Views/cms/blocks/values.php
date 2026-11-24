<?php
/**
 * Block "values" (Figma About 73:58 "Our Values"): centred heading and white icon cards.
 *
 * @var array<string, string>      $s      block settings
 * @var array<string, list<array>> $items  value
 * @var string                     $sfx    id suffix for repeated blocks
 */
helper('cb');
$vals  = $items['value'] ?? [];
$id    = 'cb-values-title' . $sfx;
$icons = \App\Libraries\Cms\BlockTypes::ICONS;
?>
<section class="cb-values" aria-labelledby="<?= esc($id, 'attr') ?>">
  <div class="cb-wrap">
    <div class="cb-head cb-reveal">
      <h2 class="cb-h2" id="<?= esc($id, 'attr') ?>"><?= esc(trim($s['title'] ?? '')) ?></h2>
      <?php if (trim($s['lead'] ?? '') !== ''): ?><p class="cb-p"><?= esc(trim($s['lead'])) ?></p><?php endif ?>
    </div>
    <ul class="cb-values__row" style="--n:<?= max(1, min(5, count($vals))) ?>">
      <?php foreach ($vals as $v): $ic = isset($icons[$v['kicker'] ?? '']) ? $v['kicker'] : 'star'; ?>
      <li class="cb-vcard cb-reveal">
        <span class="cb-vcard__icon"><?= cb_icon($ic) ?></span>
        <h3><?= esc((string) $v['title']) ?></h3>
        <?php if (trim((string) $v['subtitle']) !== ''): ?><p><?= esc((string) $v['subtitle']) ?></p><?php endif ?>
      </li>
      <?php endforeach ?>
    </ul>
  </div>
</section>
