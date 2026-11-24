<?php
/**
 * Slider Controls (Figma "Slider Controls"): progress 01 ─── 0N + prev/next discs.
 * Driven by assets/js/cb-site.js; hidden by JS when every card already fits.
 * Used by the city_slider, card_grid and newsroom blocks.
 *
 * @var int    $total
 * @var string $label  what the slider shows, for the button labels
 */
helper('cb');
?>
<div class="cb-controls" data-controls>
  <div class="cb-progress" aria-hidden="true">
    <span data-current>01</span>
    <span class="cb-progress__track"><span class="cb-progress__bar" data-bar style="width:<?= round(100 / max(1, (int) $total), 2) ?>%"></span></span>
    <span class="cb-progress__total"><?= sprintf('%02d', (int) $total) ?></span>
  </div>
  <div class="cb-arrows">
    <button type="button" class="cb-disc cb-disc--light" data-prev aria-label="Previous <?= esc($label, 'attr') ?>"><?= cb_icon('prev') ?></button>
    <button type="button" class="cb-disc cb-disc--navy" data-next aria-label="Next <?= esc($label, 'attr') ?>"><?= cb_icon('arrow') ?></button>
  </div>
  <p class="cb-sr" aria-live="polite" data-live></p>
</div>
