<?php
/**
 * Block "expertise" (Figma 41:41 "A century of expertise"): photo stack beside a heading,
 * text, milestone chips and a button.
 *
 * @var array<string, string>      $s      block settings
 * @var array<string, list<array>> $items  photo, milestone
 * @var string                     $sfx
 */
helper('cb');
$stack = array_slice($items['photo'] ?? [], 0, 3);
$miles = $items['milestone'] ?? [];
$id    = 'cb-expertise-title' . $sfx;
?>
<section class="cb-expertise" aria-labelledby="<?= esc($id, 'attr') ?>">
  <div class="cb-expertise__in">
    <?php if ($stack !== []): ?>
    <div class="cb-stack cb-reveal" data-stack>
      <?php foreach ($stack as $p): ?>
      <figure class="cb-stack__img"><?= cb_picture($p['image'], (string) $p['image_alt'], ['width' => '440', 'height' => '600', 'sizes' => '(max-width: 767px) 160px, 250px']) ?></figure>
      <?php endforeach ?>
      <?php if (count($stack) > 1): ?>
      <div class="cb-stack__dots">
        <?php foreach ($stack as $i => $p): ?>
        <button type="button" data-go="<?= $i ?>" aria-label="Bring photo <?= $i + 1 ?> to the front"<?= $i === count($stack) - 1 ? ' aria-current="true"' : '' ?>></button>
        <?php endforeach ?>
      </div>
      <?php endif ?>
    </div>
    <?php endif ?>
    <div class="cb-expertise__copy cb-reveal">
      <?php if (($logo = cb_picture($s['logo'] ?? '', 'Coldwell Banker University', ['height' => '53'])) !== ''): ?><span class="cb-fcard__logo cb-logo-chip"><?= $logo ?></span><?php endif ?>
      <h2 class="cb-h2 cb-pre" id="<?= esc($id, 'attr') ?>"><?= esc(trim($s['title'] ?? '')) ?></h2>
      <?php if (trim($s['quote'] ?? '') !== ''): ?><blockquote class="cb-quote-rule"><p><?= esc(trim($s['quote'])) ?></p></blockquote><?php endif ?>
      <?php if (trim($s['body'] ?? '') !== ''): ?><p class="cb-p"><?= esc(trim($s['body'])) ?></p><?php endif ?>
      <?php if ($miles !== []): ?>
      <ul class="cb-milestones">
        <?php foreach ($miles as $m): ?><li><b><?= esc($m['title']) ?></b> <?= esc($m['subtitle']) ?></li><?php endforeach ?>
      </ul>
      <?php endif ?>
      <?= cb_pill($s['cta_label'] ?? '', $s['cta_url'] ?? '', ($s['cta_tone'] ?? 'gold') === 'light' ? 'light' : 'gold') ?>
    </div>
  </div>
</section>
