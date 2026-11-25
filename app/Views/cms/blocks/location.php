<?php
/**
 * Block "location" (Figma Contact 85:58 "Corporate Office"): soft panel, map left, address /
 * hours / Get Directions right. The map is a Google Maps embed only when the admin pasted a
 * https://www.google.com/maps/embed link; otherwise a styled navy map placeholder with a pin.
 * "Get Directions" opens Google Maps searching the address.
 *
 * @var array<string, string> $s    block settings
 * @var string                $sfx  id suffix for repeated blocks
 */
helper('cb');
$id      = 'cb-loc-title' . $sfx;
$address = trim($s['address'] ?? '');
$embed   = trim($s['map_embed'] ?? '');
$embedOk = (bool) preg_match('#^https://(www\.)?google\.[a-z.]+/maps/embed\?[^\s"<>]+$#i', $embed);
$dirUrl  = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode(preg_replace('/\s+/', ' ', $address));
?>
<section class="cb-loc" aria-labelledby="<?= esc($id, 'attr') ?>">
  <div class="cb-loc__panel">
    <div class="cb-loc__map cb-reveal">
      <?php if ($embedOk): ?>
      <iframe src="<?= esc($embed, 'attr') ?>" title="Map: <?= esc(trim($s['title'] ?? ''), 'attr') ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
      <?php else: ?>
      <div class="cb-loc__drawn" aria-hidden="true">
        <span class="cb-loc__pin"><?= cb_icon('pin') ?></span>
        <?php if (trim($s['eyebrow'] ?? '') !== ''): ?><span class="cb-loc__tag"><?= esc(trim($s['eyebrow'])) ?></span><?php endif ?>
      </div>
      <?php endif ?>
    </div>
    <div class="cb-loc__copy cb-reveal">
      <h2 id="<?= esc($id, 'attr') ?>"><?= esc(trim($s['title'] ?? '')) ?></h2>
      <address class="cb-p cb-pre"><?= esc($address) ?></address>
      <?php if (trim($s['hours'] ?? '') !== ''): ?>
      <p class="cb-loc__hours"><span aria-hidden="true"><?= cb_icon('clock') ?></span><?= esc(trim($s['hours'])) ?></p>
      <?php endif ?>
      <?php if ($address !== ''): ?>
      <a class="cb-pill cb-pill--navy" href="<?= esc($dirUrl, 'attr') ?>" target="_blank" rel="noopener"><span><?= esc(trim($s['cta_label'] ?? '') ?: 'Get Directions') ?></span><span class="cb-pill__icon"><?= cb_icon('arrow') ?></span><span class="cb-sr"> (opens Google Maps)</span></a>
      <?php endif ?>
    </div>
  </div>
</section>
