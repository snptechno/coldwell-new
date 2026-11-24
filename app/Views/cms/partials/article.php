<?php
/**
 * Article header (eyebrow "Article · date", H1, excerpt, featured image) and body.
 * The body was sanitised on save (BlogContent::sanitize); it is sanitised again here
 * so a row written outside the admin can never inject markup.
 *
 * @var array<string, mixed> $page
 */
use App\Libraries\BlogContent;

helper('cb');
$date = (string) ($page['published_at'] ?: $page['created_at']);
$ts   = $date !== '' ? strtotime($date) : false;
$body = BlogContent::sanitize((string) $page['body']);
$img  = cb_picture((string) $page['featured_image'], (string) $page['image_alt'], ['loading' => 'eager', 'fetchpriority' => 'high', 'width' => '1200', 'height' => '640']);
?>
<article class="cb-article" aria-labelledby="cb-article-title">
  <header class="cb-wrap cb-article__head">
    <p class="cb-eyebrow">Article<?php if ($ts): ?>&nbsp; · &nbsp;<time datetime="<?= esc(date('Y-m-d', $ts), 'attr') ?>"><?= esc(date('j M Y', $ts)) ?></time><?php endif ?></p>
    <h1 class="cb-display-l" id="cb-article-title"><?= esc($page['title']) ?></h1>
    <?php if (trim((string) $page['excerpt']) !== ''): ?><p class="cb-lead cb-article__lead"><?= esc(trim((string) $page['excerpt'])) ?></p><?php endif ?>
  </header>
  <?php if ($img !== ''): ?>
  <div class="cb-wrap"><figure class="cb-article__media"><?= $img ?></figure></div>
  <?php endif ?>
  <?php if ($body !== ''): ?>
  <div class="cb-wrap"><div class="cb-prose cb-article__body"><?= $body ?></div></div>
  <?php endif ?>
</article>
