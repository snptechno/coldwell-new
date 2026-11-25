<?php
/**
 * Block "video_hub" (Figma 140:248 "Section/Videos" + 140:301 "Section/More Videos"):
 * Featured video + 3 side items per category tab, plus the soft rounded "More From ..."
 * 3-column video grid with client-side Load More. Data comes from the 5 Video admin modules.
 *
 * @var array<string, string> $s   block settings
 * @var string                $sfx id suffix for repeated blocks
 */
use App\Libraries\Cms\Media;

helper('cb');
$tabs = Media::videos(30);
if ($tabs === []) {
    return;
}
$tabNames   = array_keys($tabs);
$defaultTab = trim($s['default_tab'] ?? 'Finding a Home');
$activeIdx  = array_search($defaultTab, $tabNames, true);
if ($activeIdx === false) {
    $activeIdx = 0;
}
$perPage   = max(3, min(24, (int) ($s['per_page'] ?? 6) ?: 6));
$title     = trim($s['title'] ?? '') ?: 'Videos';
$lead      = trim($s['lead'] ?? '');
$moreTitle = trim($s['more_title'] ?? '') ?: 'More From CB India';
$loadLabel = trim($s['load_label'] ?? '') ?: 'Load More';
$id        = 'cb-vhub-title' . $sfx;
$moreId    = 'cb-vmore-title' . $sfx;

// Build combined "More" pool across all categories (newest/ordered), deduplicated by watch URL
$allVideos = [];
$seenWatch = [];
foreach ($tabs as $tabLabel => $list) {
    foreach ($list as $v) {
        if (isset($seenWatch[$v['watch']])) {
            continue;
        }
        $seenWatch[$v['watch']] = true;
        $v['tab'] = $tabLabel;
        $allVideos[] = $v;
    }
}
$totalAll = count($allVideos);
$initShow = min($perPage, $totalAll);
?>
<section class="cb-videos cb-vhub" aria-labelledby="<?= esc($id, 'attr') ?>" data-vhub>
  <div class="cb-wrap">
    <div class="cb-head cb-head--split cb-reveal">
      <div>
        <h2 class="cb-h2" id="<?= esc($id, 'attr') ?>"><?= esc($title) ?></h2>
        <?php if ($lead !== ''): ?><p class="cb-p"><?= esc($lead) ?></p><?php endif ?>
      </div>
      <div class="cb-tabs cb-tabs--left cb-tabs--sm" role="tablist" aria-label="Video categories">
        <?php foreach ($tabNames as $i => $tab): ?>
        <button type="button" class="cb-tab" role="tab" id="cb-vh-t-<?= $i . esc($sfx, 'attr') ?>" aria-controls="cb-vh-p-<?= $i . esc($sfx, 'attr') ?>" aria-selected="<?= $i === $activeIdx ? 'true' : 'false' ?>"<?= $i === $activeIdx ? '' : ' tabindex="-1"' ?>><?= esc($tab) ?></button>
        <?php endforeach ?>
      </div>
    </div>

    <?php foreach ($tabs as $tab => $vids): $idx = array_search($tab, $tabNames, true); $main = $vids[0]; $rest = array_slice($vids, 1, 3); ?>
    <div class="cb-videos__grid" role="tabpanel" id="cb-vh-p-<?= (int) $idx . esc($sfx, 'attr') ?>" aria-labelledby="cb-vh-t-<?= (int) $idx . esc($sfx, 'attr') ?>"<?= $idx === $activeIdx ? '' : ' hidden' ?>>
      <a class="cb-vmain" href="<?= esc($main['watch'], 'attr') ?>" target="_blank" rel="noopener noreferrer">
        <img src="<?= esc($main['thumb'], 'attr') ?>" alt="" loading="lazy" decoding="async" width="760" height="460">
        <span class="cb-vmain__badge" aria-hidden="true"><?= esc(strtoupper($main['kind'])) ?></span>
        <span class="cb-vplay" aria-hidden="true"><?= cb_icon('play') ?></span>
        <span class="cb-vmain__txt"><small><?= esc($tab) ?></small><b><?= esc($main['title']) ?></b><span class="cb-sr"> (opens YouTube in a new tab)</span></span>
      </a>
      <div class="cb-vside">
        <ul>
          <?php foreach ($rest as $v): ?>
          <li><a class="cb-vitem" href="<?= esc($v['watch'], 'attr') ?>" target="_blank" rel="noopener noreferrer">
            <span class="cb-vitem__img"><img src="<?= esc($v['thumb'], 'attr') ?>" alt="" loading="lazy" decoding="async" width="220" height="140"><span class="cb-vplay cb-vplay--sm" aria-hidden="true"><?= cb_icon('play') ?></span></span>
            <span><small><?= esc($v['kind']) ?></small><b><?= esc($v['title']) ?></b><span class="cb-sr"> (opens YouTube in a new tab)</span></span>
          </a></li>
          <?php endforeach ?>
        </ul>
      </div>
    </div>
    <?php endforeach ?>
  </div>
</section>

<?php if ($totalAll > 0): ?>
<section class="cb-vmore" aria-labelledby="<?= esc($moreId, 'attr') ?>" data-vmore data-page="<?= $perPage ?>">
  <div class="cb-vmore__panel">
    <div class="cb-vmore__head">
      <h2 class="cb-h2" id="<?= esc($moreId, 'attr') ?>"><?= esc($moreTitle) ?></h2>
      <p class="cb-vmore__count" data-vmore-count>Showing <?= $initShow ?> of <?= $totalAll ?></p>
    </div>
    <ul class="cb-vgrid" data-vmore-list>
      <?php foreach ($allVideos as $i => $v): ?>
      <li<?= $i >= $perPage ? ' hidden' : '' ?>>
        <a class="cb-vcard" href="<?= esc($v['watch'], 'attr') ?>" target="_blank" rel="noopener noreferrer">
          <span class="cb-vcard__img">
            <img src="<?= esc($v['thumb'], 'attr') ?>" alt="" loading="lazy" decoding="async" width="384" height="220">
            <span class="cb-vplay cb-vplay--md" aria-hidden="true"><?= cb_icon('play') ?></span>
          </span>
          <span class="cb-vcard__kind"><?= esc(strtoupper($v['tab'])) ?></span>
          <span class="cb-vcard__title"><?= esc($v['title']) ?><span class="cb-sr"> (opens YouTube in a new tab)</span></span>
        </a>
      </li>
      <?php endforeach ?>
    </ul>
    <?php if ($totalAll > $perPage): ?>
    <div class="cb-vmore__foot">
      <button type="button" class="cb-pill cb-pill--light cb-pill--on-section" data-vmore-btn>
        <span><?= esc($loadLabel) ?></span><span class="cb-pill__icon"><?= cb_icon('arrow') ?></span>
      </button>
    </div>
    <?php endif ?>
  </div>
</section>
<?php endif ?>

<script>
(function () {
  document.querySelectorAll('[data-vhub]').forEach(function (root) {
    var tabs = Array.prototype.slice.call(root.querySelectorAll('[role="tab"]'));
    var panels = Array.prototype.slice.call(root.querySelectorAll('[role="tabpanel"]'));
    tabs.forEach(function (t, i) {
      t.addEventListener('click', function () {
        tabs.forEach(function (b, j) {
          var on = j === i;
          b.setAttribute('aria-selected', on ? 'true' : 'false');
          b.tabIndex = on ? 0 : -1;
        });
        panels.forEach(function (p, j) { p.hidden = j !== i; });
      });
    });
  });

  document.querySelectorAll('[data-vmore]').forEach(function (root) {
    var per = parseInt(root.getAttribute('data-page') || '6', 10);
    var items = Array.prototype.slice.call(root.querySelectorAll('[data-vmore-list] > li'));
    var btn = root.querySelector('[data-vmore-btn]');
    var count = root.querySelector('[data-vmore-count]');
    var shown = Math.min(per, items.length);
    if (!btn) return;
    btn.addEventListener('click', function () {
      shown = Math.min(items.length, shown + per);
      items.forEach(function (li, i) { li.hidden = i >= shown; });
      if (count) count.textContent = 'Showing ' + shown + ' of ' + items.length;
      if (shown >= items.length) {
        btn.parentElement ? btn.parentElement.remove() : btn.remove();
      }
    });
  });
})();
</script>
