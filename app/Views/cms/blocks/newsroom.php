<?php
/**
 * Block "newsroom" (Figma 41:41 "Newsroom & Insights"): latest Research / News / Video,
 * filled automatically by App\Libraries\Cms\Newsroom. Nothing renders without cards.
 *
 * @var array<string, string> $s   block settings
 * @var string                $sfx
 */
use App\Libraries\Cms\Newsroom;

helper('cb');
$news = Newsroom::cards();
$id   = 'cb-news-title' . $sfx;
if ($news === []) {
    return;
}
?>
<section class="cb-news" aria-labelledby="<?= esc($id, 'attr') ?>" data-carousel>
  <div class="cb-wrap">
    <div class="cb-head cb-head--split cb-reveal">
      <div>
        <h2 class="cb-h2" id="<?= esc($id, 'attr') ?>"><?= esc(trim($s['title'] ?? '') !== '' ? trim($s['title']) : 'Newsroom & Insights') ?></h2>
        <?php if (trim($s['lead'] ?? '') !== ''): ?><p class="cb-p"><?= esc(trim($s['lead'])) ?></p><?php endif ?>
      </div>
      <div class="cb-news__actions"><?= cb_pill($s['cta_label'] ?? '', $s['cta_url'] ?? '', 'light', 'cb-pill--on-section') ?></div>
    </div>
    <ul class="cb-track cb-news-track" data-track aria-label="Latest news">
      <?php foreach ($news as $n): ?>
      <li>
        <a class="cb-ncard" href="<?= esc($n['href'], 'attr') ?>"<?= $n['external'] ? ' target="_blank" rel="noopener"' : '' ?>>
          <div class="cb-ncard__img"><?= cb_picture($n['image'], $n['alt'], ['width' => '960', 'height' => '536']) ?></div>
          <p class="cb-ncard__meta"><span class="cb-chip"><?= esc($n['category']) ?></span><span><?= esc($n['meta']) ?></span></p>
          <h3><?= esc($n['title']) ?><?php if ($n['external']): ?><span class="cb-sr"> (opens in a new tab)</span><?php endif ?></h3>
        </a>
      </li>
      <?php endforeach ?>
    </ul>
    <?= view('cms/partials/slider_controls', ['total' => count($news), 'label' => 'news'], ['saveData' => false]) ?>
  </div>
</section>
