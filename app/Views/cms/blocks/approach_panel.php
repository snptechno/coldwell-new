<?php
/**
 * Block "approach_panel" (Figma 135:726 "Section/Approach"): soft rounded surface panel
 * with centred eyebrow, medium navy statement, and gold pill CTA button.
 *
 * @var array<string, string> $s   block settings
 * @var string                $sfx id suffix for repeated blocks
 */
helper('cb');
$eyebrow   = trim($s['eyebrow'] ?? '') ?: 'OUR APPROACH';
$statement = trim($s['statement'] ?? '');
$ctaLabel  = trim($s['cta_label'] ?? '') ?: 'Talk to an Advisor';
$ctaUrl    = trim($s['cta_url'] ?? '');
$headingId = 'cb-approach-heading' . $sfx;
?>
<section class="cb-approach" aria-labelledby="<?= esc($headingId, 'attr') ?>">
  <div class="cb-approach__panel">
    <p class="cb-eyebrow cb-eyebrow--center"><?= esc($eyebrow) ?></p>
    <h2 id="<?= esc($headingId, 'attr') ?>" class="cb-approach__statement"><?= esc($statement) ?></h2>
    <?php if (($pill = cb_pill($ctaLabel, $ctaUrl, 'gold')) !== ''): ?>
      <?= $pill ?>
    <?php endif ?>
  </div>
</section>
