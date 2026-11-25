<?php
/**
 * Block "testimonials" (Figma Franchise 77:58 "Partners Who Grew With Us"): soft panel,
 * centred heading and 2-column quote cards with gold quote marks.
 *
 * @var array<string, string>      $s      block settings
 * @var array<string, list<array>> $items  quote
 * @var string                     $sfx    id suffix for repeated blocks
 */
helper('cb');
$quotes = $items['quote'] ?? [];
$id     = 'cb-quotes-title' . $sfx;
if ($quotes === []) {
    return;
}
?>
<section class="cb-quotes" aria-labelledby="<?= esc($id, 'attr') ?>">
  <div class="cb-quotes__panel">
    <div class="cb-head cb-reveal">
      <h2 class="cb-h2" id="<?= esc($id, 'attr') ?>"><?= esc(trim($s['title'] ?? '')) ?></h2>
      <?php if (trim($s['lead'] ?? '') !== ''): ?><p class="cb-p"><?= esc(trim($s['lead'])) ?></p><?php endif ?>
    </div>
    <ul class="cb-quotes__grid">
      <?php foreach ($quotes as $q): ?>
      <li class="cb-reveal">
        <figure class="cb-quote">
          <blockquote><p><?= esc(trim((string) $q['body'])) ?></p></blockquote>
          <figcaption>
            <?php if (($ph = cb_picture($q['image'], (string) ($q['image_alt'] ?: $q['title']), ['width' => '52', 'height' => '52'])) !== ''): ?>
            <span class="cb-quote__ph"><?= $ph ?></span>
            <?php else: ?>
            <span class="cb-quote__ph cb-quote__ph--initial" aria-hidden="true"><?= esc(mb_strtoupper(mb_substr(trim((string) $q['title']), 0, 1))) ?></span>
            <?php endif ?>
            <span><b><?= esc((string) $q['title']) ?></b><?php if (trim((string) $q['subtitle']) !== ''): ?><span><?= esc((string) $q['subtitle']) ?></span><?php endif ?></span>
          </figcaption>
        </figure>
      </li>
      <?php endforeach ?>
    </ul>
  </div>
</section>
