<?php
/**
 * Block "mission" (Figma 41:41 "Who we are"): centred eyebrow, statement, photo strip, button.
 *
 * @var array<string, string>      $s      block settings
 * @var array<string, list<array>> $items  photo
 * @var string                     $sfx    id suffix for repeated blocks
 */
helper('cb');
$thumbs = array_slice($items['photo'] ?? [], 0, 5);
$id     = 'cb-mission-title' . $sfx;
?>
<section class="cb-mission" aria-labelledby="<?= esc($id, 'attr') ?>">
  <p class="cb-eyebrow cb-eyebrow--center cb-reveal" id="<?= esc($id, 'attr') ?>"><?= esc(trim($s['eyebrow'] ?? '') !== '' ? trim($s['eyebrow']) : 'Who we are') ?></p>
  <p class="cb-statement cb-reveal"><?= esc(trim($s['statement'] ?? '')) ?></p>
  <?php if ($thumbs !== []): ?>
  <ul class="cb-thumbs cb-reveal">
    <?php foreach ($thumbs as $t): ?>
    <li><?= cb_picture($t['image'], (string) $t['image_alt'], ['width' => '150', 'height' => '120']) ?></li>
    <?php endforeach ?>
  </ul>
  <?php endif ?>
  <div class="cb-reveal"><?= cb_pill($s['cta_label'] ?? '', $s['cta_url'] ?? '', 'gold') ?></div>
</section>
