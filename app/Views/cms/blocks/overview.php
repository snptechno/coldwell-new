<?php
/**
 * Block "overview" (Figma L&D 83:58 "Overview"): centred eyebrow, statement, text, audience chips.
 *
 * @var array<string, string> $s    block settings
 * @var string                $sfx  id suffix for repeated blocks
 */
$id    = 'cb-overview-title' . $sfx;
$chips = array_values(array_filter(array_map('trim', preg_split('/\R/u', (string) ($s['chips'] ?? ''))), 'strlen'));
?>
<section class="cb-overview" aria-labelledby="<?= esc($id, 'attr') ?>">
  <?php if (trim($s['eyebrow'] ?? '') !== ''): ?><h2 class="cb-eyebrow cb-eyebrow--center cb-reveal" id="<?= esc($id, 'attr') ?>"><?= esc(trim($s['eyebrow'])) ?></h2><?php endif ?>
  <p class="cb-statement cb-reveal"<?= trim($s['eyebrow'] ?? '') === '' ? ' id="' . esc($id, 'attr') . '"' : '' ?>><?= esc(trim($s['statement'] ?? '')) ?></p>
  <?php if (trim($s['body'] ?? '') !== ''): ?><p class="cb-p cb-reveal"><?= esc(trim($s['body'])) ?></p><?php endif ?>
  <?php if ($chips !== []): ?>
  <ul class="cb-milestones cb-overview__chips cb-reveal" aria-label="Who it is for">
    <?php foreach ($chips as $c): ?><li><b><?= esc($c) ?></b></li><?php endforeach ?>
  </ul>
  <?php endif ?>
</section>
