<?php
/**
 * Block "album_gallery" (Figma L&D 83:58 "Photo Gallery"): heading + pill, album bento (newest
 * large, next four in a 2x2 grid). Each album is a button opening a native <dialog> viewer with
 * that album's photos (lazy-loaded); without JavaScript the album links to its cover image.
 *
 * @var array<string, string> $s    block settings
 * @var string                $sfx  id suffix for repeated blocks
 */
use App\Libraries\Cms\Learning;

helper('cb');
$albums = array_slice(Learning::albums(12), 0, 5);
if ($albums === []) {
    return;
}
$id = 'cb-albums-title' . $sfx;
?>
<section class="cb-albums" aria-labelledby="<?= esc($id, 'attr') ?>" data-albums>
  <div class="cb-wrap">
    <div class="cb-head cb-head--split cb-reveal">
      <div>
        <h2 class="cb-h2" id="<?= esc($id, 'attr') ?>"><?= esc(trim($s['title'] ?? '')) ?></h2>
        <?php if (trim($s['lead'] ?? '') !== ''): ?><p class="cb-p"><?= esc(trim($s['lead'])) ?></p><?php endif ?>
      </div>
      <?= cb_pill($s['cta_label'] ?? '', $s['cta_url'] ?? '', 'light', 'cb-pill--on-section') ?>
    </div>
    <ul class="cb-albums__bento">
      <?php foreach ($albums as $i => $a): $dlg = 'cb-album-' . $a['id'] . $sfx; ?>
      <li class="cb-reveal<?= $i === 0 ? ' cb-album--big' : '' ?>">
        <a class="cb-album" href="<?= esc($a['cover'], 'attr') ?>" data-album="<?= esc($dlg, 'attr') ?>">
          <img src="<?= esc($a['cover'], 'attr') ?>" alt="" loading="lazy" decoding="async">
          <span class="cb-album__txt"><b><?= esc($a['name']) ?></b><?php if ($a['date'] !== ''): ?><span><?= esc($a['date']) ?></span><?php endif ?><?php if (count($a['photos']) > 0): ?><span class="cb-sr">, <?= count($a['photos']) ?> photos, opens a photo viewer</span><?php endif ?></span>
        </a>
        <?php if ($a['photos'] !== []): ?>
        <dialog class="cb-viewer" id="<?= esc($dlg, 'attr') ?>" aria-label="<?= esc($a['name'], 'attr') ?> photos">
          <div class="cb-viewer__bar"><b><?= esc($a['name']) ?><?= $a['date'] !== '' ? ' · ' . esc($a['date']) : '' ?></b><button type="button" class="cb-disc cb-disc--light" data-close aria-label="Close"><?= cb_icon('close') ?></button></div>
          <ul class="cb-viewer__grid">
            <?php foreach ($a['photos'] as $k => $ph): ?><li><img data-src="<?= esc($ph, 'attr') ?>" alt="<?= esc($a['name'] . ' — photo ' . ($k + 1), 'attr') ?>" decoding="async"></li><?php endforeach ?>
          </ul>
        </dialog>
        <?php endif ?>
      </li>
      <?php endforeach ?>
    </ul>
  </div>
</section>
