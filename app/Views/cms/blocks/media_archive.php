<?php
/**
 * Block "media_archive": 3-column live-data archive grid with Load More for
 * Industry News, Knowledge Series, and Newsletter editions.
 *
 * @var array<string, string> $s   block settings
 * @var string                $sfx id suffix for repeated blocks
 */
use App\Libraries\Cms\Media;

helper('cb');
$source    = strtolower(trim($s['source'] ?? 'industry'));
$perPage   = max(3, min(24, (int) ($s['per_page'] ?? 6) ?: 6));
$title     = trim($s['title'] ?? '');
$loadLabel = trim($s['load_label'] ?? '') ?: 'Load More';
$id        = 'cb-marchive-title' . $sfx;

$photos = ['city-mumbai', 'towers', 'boardroom', 'news-research', 'family', 'villa', 'news-press', 'handshake', 'living', 'city-delhi', 'news-video', 'city-bengaluru'];
$items  = [];
$badge  = 'News';

try {
    $db = db_connect();
    if ($source === 'knowledge') {
        $badge = 'Knowledge Series';
        $rows  = $db->table('coldwell_banker_knowledge_series')
            ->select('id, title, external_link, created_date')
            ->orderBy('id', 'DESC')->limit(60)->get()->getResultArray();
        foreach ($rows as $i => $r) {
            $link    = trim((string) ($r['external_link'] ?? ''));
            $isExt   = (bool) preg_match('#^https?://#i', $link);
            $items[] = [
                'title'    => trim(html_entity_decode(strip_tags((string) ($r['title'] ?? '')), ENT_QUOTES | ENT_HTML5, 'UTF-8')),
                'meta'     => ! empty($r['created_date']) ? date('d M Y', strtotime((string) $r['created_date'])) : 'Knowledge Series',
                'href'     => $isExt ? $link : site_url('iframe-knowledge-image/' . (int) $r['id']),
                'external' => $isExt,
                'image'    => 'assets/images/home-v2/' . $photos[$i % count($photos)] . '.jpg',
            ];
        }
    } elseif ($source === 'newsletter') {
        $badge = 'Newsletter';
        $rows  = $db->table('coldwell_banker_newsletter')
            ->select('id, title, created_date')
            ->orderBy('id', 'DESC')->limit(60)->get()->getResultArray();
        foreach ($rows as $i => $r) {
            $items[] = [
                'title'    => trim(html_entity_decode(strip_tags((string) ($r['title'] ?? '')), ENT_QUOTES | ENT_HTML5, 'UTF-8')),
                'meta'     => ! empty($r['created_date']) ? date('M Y', strtotime((string) $r['created_date'])) : 'Edition',
                'href'     => site_url('iframe-newsletter/' . (int) $r['id']),
                'external' => false,
                'image'    => 'assets/images/home-v2/' . $photos[$i % count($photos)] . '.jpg',
            ];
        }
    } else {
        $badge = 'Industry News';
        $rows  = $db->table('coldwell_banker_industry_news')
            ->select('id, title, external_link, created_date')
            ->orderBy('id', 'DESC')->limit(60)->get()->getResultArray();
        foreach ($rows as $i => $r) {
            $link    = trim((string) ($r['external_link'] ?? ''));
            $isExt   = (bool) preg_match('#^https?://#i', $link);
            $items[] = [
                'title'    => trim(html_entity_decode(strip_tags((string) ($r['title'] ?? '')), ENT_QUOTES | ENT_HTML5, 'UTF-8')),
                'meta'     => ! empty($r['created_date']) ? date('d M Y', strtotime((string) $r['created_date'])) : 'Industry',
                'href'     => $isExt ? $link : site_url('iframe-industry-news/' . (int) $r['id']),
                'external' => $isExt,
                'image'    => 'assets/images/home-v2/' . $photos[$i % count($photos)] . '.jpg',
            ];
        }
    }
} catch (\Throwable $e) {
    log_message('error', 'CMS media_archive: {msg}', ['msg' => $e->getMessage()]);
}

$total    = count($items);
$initShow = min($perPage, $total);
?>
<section class="cb-marchive" aria-labelledby="<?= esc($id, 'attr') ?>" data-vmore data-page="<?= $perPage ?>">
  <div class="cb-wrap">
    <div class="cb-vmore__head">
      <h2 class="cb-h2" id="<?= esc($id, 'attr') ?>"><?= esc($title !== '' ? $title : $badge) ?></h2>
      <?php if ($total > 0): ?>
      <p class="cb-vmore__count" data-vmore-count>Showing <?= $initShow ?> of <?= $total ?></p>
      <?php endif ?>
    </div>
    <?php if ($total === 0): ?>
      <p class="cb-p cb-research-empty">No <?= esc(strtolower($badge)) ?> items available right now.</p>
    <?php else: ?>
    <ul class="cb-marchive__grid" data-vmore-list>
      <?php foreach ($items as $i => $item): ?>
      <li<?= $i >= $perPage ? ' hidden' : '' ?>>
        <a class="cb-ncard" href="<?= esc($item['href'], 'attr') ?>"<?= $item['external'] ? ' target="_blank" rel="noopener noreferrer"' : '' ?>>
          <div class="cb-ncard__img"><?= cb_picture($item['image'], '', ['width' => '960', 'height' => '536', 'sizes' => '(max-width: 767px) 100vw, (max-width: 1199px) 50vw, 384px']) ?></div>
          <p class="cb-ncard__meta"><span class="cb-chip"><?= esc($badge) ?></span><span><?= esc($item['meta']) ?></span></p>
          <h3><?= esc($item['title']) ?><?php if ($item['external']): ?><span class="cb-sr"> (opens in a new tab)</span><?php endif ?></h3>
        </a>
      </li>
      <?php endforeach ?>
    </ul>
    <?php if ($total > $perPage): ?>
    <div class="cb-vmore__foot">
      <button type="button" class="cb-pill cb-pill--light cb-pill--on-section" data-vmore-btn>
        <span><?= esc($loadLabel) ?></span><span class="cb-pill__icon"><?= cb_icon('arrow') ?></span>
      </button>
    </div>
    <?php endif ?>
    <?php endif ?>
  </div>
</section>
<script>
(function () {
  document.querySelectorAll('.cb-marchive[data-vmore]').forEach(function (root) {
    if (root.dataset.vmoreBound) return;
    root.dataset.vmoreBound = '1';
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
