<?php
/**
 * Block "numbered_cards" (Figma L&D 83:58 "Training Programs"): soft panel, centred heading and
 * numbered white cards (3 per row); the first number disc is gold; optional tag chips.
 *
 * @var array<string, string>      $s      block settings
 * @var array<string, list<array>> $items  card
 * @var string                     $sfx    id suffix for repeated blocks
 */
$cards = $items['card'] ?? [];
$id    = 'cb-ncards-title' . $sfx;
?>
<section class="cb-steps cb-ncards" aria-labelledby="<?= esc($id, 'attr') ?>">
  <div class="cb-steps__panel">
    <div class="cb-head cb-reveal">
      <h2 class="cb-h2" id="<?= esc($id, 'attr') ?>"><?= esc(trim($s['title'] ?? '')) ?></h2>
      <?php if (trim($s['lead'] ?? '') !== ''): ?><p class="cb-p"><?= esc(trim($s['lead'])) ?></p><?php endif ?>
    </div>
    <ol class="cb-ncards__grid">
      <?php foreach ($cards as $i => $c): $tags = array_values(array_filter(array_map('trim', explode(',', (string) $c['kicker'])), 'strlen')); ?>
      <li class="cb-ncard2 cb-reveal">
        <span class="cb-step__num" aria-hidden="true"><?= sprintf('%02d', $i + 1) ?></span>
        <h3><?= esc((string) $c['title']) ?></h3>
        <?php if (trim((string) $c['subtitle']) !== ''): ?><p><?= esc((string) $c['subtitle']) ?></p><?php endif ?>
        <?php if ($tags !== []): ?><ul class="cb-tags"><?php foreach ($tags as $t): ?><li class="cb-chip"><?= esc($t) ?></li><?php endforeach ?></ul><?php endif ?>
      </li>
      <?php endforeach ?>
    </ol>
  </div>
</section>
