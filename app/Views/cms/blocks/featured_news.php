<?php
/**
 * Block "featured_news" (Figma Media 79:58 "Featured Story"): photo left, badge + date/source,
 * headline, excerpt and "Read Article" right. Story from CB India News (pinned id or newest).
 *
 * @var array<string, string> $s    block settings
 * @var string                $sfx  id suffix for repeated blocks
 */
use App\Libraries\Cms\Media;

helper('cb');
$n = Media::newsItem((int) ($s['news_id'] ?? 0) ?: null);
if ($n === null) {
    return;
}
$id = 'cb-feat-title' . $sfx;
?>
<section class="cb-feat" aria-labelledby="<?= esc($id, 'attr') ?>">
  <article class="cb-feat__in">
    <div class="cb-feat__img cb-reveal"><?= cb_picture($s['image'] ?? '', (string) ($s['image_alt'] ?? ''), ['width' => '960', 'height' => '960', 'sizes' => '(max-width: 1199px) 100vw, 640px']) ?></div>
    <div class="cb-feat__copy cb-reveal">
      <p class="cb-ncard__meta"><span class="cb-chip"><?= esc(trim($s['eyebrow'] ?? '') ?: 'Featured') ?></span><?php if ($n['meta'] !== ''): ?><span><?= esc($n['meta']) ?></span><?php endif ?></p>
      <h2 id="<?= esc($id, 'attr') ?>"><?= esc($n['title']) ?></h2>
      <?php if ($n['excerpt'] !== ''): ?><p class="cb-p"><?= esc($n['excerpt']) ?></p><?php endif ?>
      <a class="cb-pill cb-pill--light" href="<?= esc($n['href'], 'attr') ?>"<?= $n['external'] ? ' target="_blank" rel="noopener"' : '' ?>><span><?= esc(trim($s['cta_label'] ?? '') ?: 'Read Article') ?></span><span class="cb-pill__icon"><?= cb_icon('arrow') ?></span><?php if ($n['external']): ?><span class="cb-sr"> (opens in a new tab)</span><?php endif ?></a>
    </div>
  </article>
</section>
