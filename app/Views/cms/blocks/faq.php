<?php
/**
 * Block "faq" (Figma Franchise 77:58 "Frequently Asked Questions"): intro on the left,
 * native <details> accordion on the right (works without JavaScript); the first item is open.
 *
 * @var array<string, string>      $s      block settings
 * @var array<string, list<array>> $items  qa
 * @var string                     $sfx    id suffix for repeated blocks
 */
helper('cb');
$qas = $items['qa'] ?? [];
$id  = 'cb-faq-title' . $sfx;
if ($qas === []) {
    return;
}
?>
<section class="cb-faq" aria-labelledby="<?= esc($id, 'attr') ?>">
  <div class="cb-faq__in">
    <div class="cb-faq__intro cb-reveal">
      <h2 class="cb-h2 cb-pre" id="<?= esc($id, 'attr') ?>"><?= esc(trim($s['title'] ?? '')) ?></h2>
      <?php if (trim($s['lead'] ?? '') !== ''): ?><p class="cb-p"><?= esc(trim($s['lead'])) ?></p><?php endif ?>
    </div>
    <div class="cb-faq__list">
      <?php foreach ($qas as $i => $qa): ?>
      <details class="cb-qa cb-reveal"<?= $i === 0 ? ' open' : '' ?>>
        <summary><span><?= esc((string) $qa['title']) ?></span><span class="cb-qa__icon" aria-hidden="true"><?= cb_icon('plus') ?><?= cb_icon('minus') ?></span></summary>
        <div class="cb-qa__a"><?php foreach (preg_split('/\R{2,}/u', trim((string) $qa['body'])) as $para): ?><p><?= nl2br(esc(trim($para)), false) ?></p><?php endforeach ?></div>
      </details>
      <?php endforeach ?>
    </div>
  </div>
</section>
