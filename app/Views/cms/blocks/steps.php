<?php
/**
 * Block "steps" (Figma Franchise 77:58 "Your Journey to Ownership"): soft panel, centred heading,
 * numbered discs joined by a line; the last disc is gold.
 *
 * @var array<string, string>      $s      block settings
 * @var array<string, list<array>> $items  step
 * @var string                     $sfx    id suffix for repeated blocks
 */
$steps = $items['step'] ?? [];
$id    = 'cb-steps-title' . $sfx;
$plain = ($s['style'] ?? 'panel') === 'plain'; // NRI › How It Works sits on white after a soft panel
?>
<section class="cb-steps<?= $plain ? ' cb-steps--plain' : '' ?>" aria-labelledby="<?= esc($id, 'attr') ?>">
  <div class="cb-steps__panel">
    <div class="cb-head cb-reveal">
      <h2 class="cb-h2" id="<?= esc($id, 'attr') ?>"><?= esc(trim($s['title'] ?? '')) ?></h2>
      <?php if (trim($s['lead'] ?? '') !== ''): ?><p class="cb-p"><?= esc(trim($s['lead'])) ?></p><?php endif ?>
    </div>
    <ol class="cb-steps__row" style="--n:<?= max(1, count($steps)) ?>">
      <?php foreach ($steps as $i => $st): ?>
      <li class="cb-step cb-reveal">
        <span class="cb-step__num" aria-hidden="true"><?= sprintf('%02d', $i + 1) ?></span>
        <h3><span class="cb-sr">Step <?= $i + 1 ?>: </span><?= esc((string) $st['title']) ?></h3>
        <?php if (trim((string) $st['subtitle']) !== ''): ?><p><?= esc((string) $st['subtitle']) ?></p><?php endif ?>
      </li>
      <?php endforeach ?>
    </ol>
  </div>
</section>
