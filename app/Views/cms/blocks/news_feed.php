<?php
/**
 * Block "news_feed" (Figma Media 79:58 "Latest from the Newsroom"): filter tabs + 3-column
 * news cards + Load More. All items are in the HTML (works without JavaScript); cb-site.js
 * filters by tab and reveals page_size cards at a time.
 *
 * @var array<string, string> $s    block settings
 * @var string                $sfx  id suffix for repeated blocks
 */
use App\Libraries\Cms\Media;

helper('cb');
$per   = max(1, min(30, (int) ($s['per_source'] ?? 12) ?: 12));
$page  = max(3, min(24, (int) ($s['page_size'] ?? 6) ?: 6));
$items = Media::feed($per);
if ($items === []) {
    return;
}
$id   = 'cb-feed-title' . $sfx;
// Tabs in the design's order, only for categories that have items.
$order = ['news' => 'CB India News', 'industry' => 'Industry News', 'awards' => 'Awards', 'research' => 'Research', 'videos' => 'Videos', 'newsletter' => 'Newsletter'];
$have  = array_flip(array_column($items, 'cat'));
$cats  = array_intersect_key($order, $have);
?>
<section class="cb-feed" aria-labelledby="<?= esc($id, 'attr') ?>" data-feed data-page="<?= $page ?>">
  <div class="cb-wrap">
    <h2 class="cb-h2 cb-reveal" id="<?= esc($id, 'attr') ?>"><?= esc(trim($s['title'] ?? '')) ?></h2>
    <div class="cb-tabs cb-tabs--left" role="tablist" aria-label="Filter news">
      <button type="button" class="cb-tab" role="tab" aria-selected="true" data-cat="">All</button>
      <?php foreach ($cats as $c => $label): ?><button type="button" class="cb-tab" role="tab" aria-selected="false" tabindex="-1" data-cat="<?= esc($c, 'attr') ?>"><?= esc($label) ?></button><?php endforeach ?>
    </div>
    <ul class="cb-feed__grid" data-feed-list aria-live="polite">
      <?php foreach ($items as $i => $n): ?>
      <li data-cat="<?= esc($n['cat'], 'attr') ?>"<?= $i >= $page ? ' class="is-more"' : '' ?>>
        <a class="cb-ncard" href="<?= esc($n['href'], 'attr') ?>"<?= $n['external'] ? ' target="_blank" rel="noopener"' : '' ?>>
          <div class="cb-ncard__img"><?= cb_picture($n['image'], '', ['width' => '960', 'height' => '536', 'sizes' => '(max-width: 767px) 100vw, (max-width: 1199px) 50vw, 384px']) ?></div>
          <p class="cb-ncard__meta"><span class="cb-chip"><?= esc($n['label']) ?></span><span><?= esc($n['meta']) ?></span></p>
          <h3><?= esc($n['title']) ?><?php if ($n['external']): ?><span class="cb-sr"> (opens in a new tab)</span><?php endif ?></h3>
        </a>
      </li>
      <?php endforeach ?>
    </ul>
    <div class="cb-feed__more">
      <button type="button" class="cb-pill cb-pill--light cb-pill--on-section" data-feed-more><span>Load More</span><span class="cb-pill__icon"><?= cb_icon('arrow') ?></span></button>
    </div>
  </div>
</section>
