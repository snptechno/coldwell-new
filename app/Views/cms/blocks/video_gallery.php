<?php
/**
 * Block "video_gallery" (Figma Media 79:58 "Videos"): heading + category tabs, a large
 * featured video and a list of the next two per category. Opens on YouTube.
 *
 * @var array<string, string> $s    block settings
 * @var string                $sfx  id suffix for repeated blocks
 */
use App\Libraries\Cms\Media;

helper('cb');
$tabs = Media::videos(6);
if ($tabs === []) {
    return;
}
$id = 'cb-videos-title' . $sfx;
$k  = 0;
?>
<section class="cb-videos" aria-labelledby="<?= esc($id, 'attr') ?>" data-vtabs>
  <div class="cb-wrap">
    <div class="cb-head cb-head--split cb-reveal">
      <div>
        <h2 class="cb-h2" id="<?= esc($id, 'attr') ?>"><?= esc(trim($s['title'] ?? '')) ?></h2>
        <?php if (trim($s['lead'] ?? '') !== ''): ?><p class="cb-p"><?= esc(trim($s['lead'])) ?></p><?php endif ?>
      </div>
      <div class="cb-tabs cb-tabs--left cb-tabs--sm" role="tablist" aria-label="Video categories">
        <?php foreach (array_keys($tabs) as $i => $tab): ?>
        <button type="button" class="cb-tab" role="tab" id="cb-vt-<?= $i . esc($sfx, 'attr') ?>" aria-controls="cb-vp-<?= $i . esc($sfx, 'attr') ?>" aria-selected="<?= $i === 0 ? 'true' : 'false' ?>"<?= $i ? ' tabindex="-1"' : '' ?>><?= esc($tab) ?></button>
        <?php endforeach ?>
      </div>
    </div>
    <?php foreach ($tabs as $tab => $vids): $main = $vids[0]; $rest = array_slice($vids, 1, 2); ?>
    <div class="cb-videos__grid" role="tabpanel" id="cb-vp-<?= $k . esc($sfx, 'attr') ?>" aria-labelledby="cb-vt-<?= $k . esc($sfx, 'attr') ?>"<?= $k++ ? ' hidden' : '' ?>>
      <a class="cb-vmain" href="<?= esc($main['watch'], 'attr') ?>" target="_blank" rel="noopener">
        <img src="<?= esc($main['thumb'], 'attr') ?>" alt="" loading="lazy" decoding="async" width="480" height="360">
        <span class="cb-vplay" aria-hidden="true"><?= cb_icon('play') ?></span>
        <span class="cb-vmain__txt"><small><?= esc($main['kind']) ?></small><b><?= esc($main['title']) ?></b><span class="cb-sr"> (opens YouTube in a new tab)</span></span>
      </a>
      <div class="cb-vside">
        <ul>
          <?php foreach ($rest as $v): ?>
          <li><a class="cb-vitem" href="<?= esc($v['watch'], 'attr') ?>" target="_blank" rel="noopener">
            <span class="cb-vitem__img"><img src="<?= esc($v['thumb'], 'attr') ?>" alt="" loading="lazy" decoding="async" width="480" height="360"><span class="cb-vplay cb-vplay--sm" aria-hidden="true"><?= cb_icon('play') ?></span></span>
            <span><small><?= esc($v['kind']) ?></small><b><?= esc($v['title']) ?></b><span class="cb-sr"> (opens YouTube in a new tab)</span></span>
          </a></li>
          <?php endforeach ?>
        </ul>
        <?= cb_pill($s['cta_label'] ?? '', $s['cta_url'] ?? '', 'light', 'cb-pill--on-section') ?>
      </div>
    </div>
    <?php endforeach ?>
  </div>
</section>
