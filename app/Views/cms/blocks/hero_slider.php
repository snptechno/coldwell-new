<?php
/**
 * Block "hero_slider" (Figma 41:41 "Hero"): full-height photo slider with the site
 * header inside, eyebrow / headline / lead / button per slide, stat chips, dots + pause.
 *
 * @var array<string, string>                 $s      block settings
 * @var array<string, list<array>>            $items  slide, stat
 * @var string                                $page_active
 * @var string                                $current_url
 */
helper('cb');
$slides = $items['slide'] ?? [];
$stats  = $items['stat'] ?? [];
$n      = count($slides);
$video  = trim($s['video_label'] ?? '');
$vUrl   = trim($s['video_url'] ?? '') !== '' ? $s['video_url'] : 'cb-india-videos';
if (\App\Libraries\Cms\CmsUrl::href($vUrl) === '') {
    $video = ''; // unsafe link: no "Watch" link at all
}
?>
<div class="cb-hero">
  <div class="cb-hero__card" data-hero>
    <?= view('cms/partials/nav', ['page_active' => $page_active, 'current_url' => $current_url], ['saveData' => false]) ?>

    <div class="cb-hero__slides" aria-hidden="true">
      <?php foreach ($slides as $i => $sl): ?>
      <div class="cb-slide<?= $i === 0 ? ' is-active' : '' ?>">
        <div class="cb-slide__img"><?= cb_picture($sl['image'], '', ['loading' => $i === 0 ? 'eager' : 'lazy', 'fetchpriority' => $i === 0 ? 'high' : 'low', 'width' => '1536', 'height' => '864', 'sizes' => '100vw']) ?></div>
        <div class="cb-slide__shade"></div>
      </div>
      <?php endforeach ?>
    </div>

    <div class="cb-hero__body" id="main" tabindex="-1" <?= $n > 1 ? 'role="region" aria-roledescription="carousel" aria-label="Highlights"' : '' ?>>
      <div class="cb-hero__copy" aria-live="off">
        <?php foreach ($slides as $i => $sl): ?>
        <div class="cb-hero__text<?= $i === 0 ? ' is-active' : '' ?>" <?= $n > 1 ? 'role="group" aria-roledescription="slide" aria-label="' . ($i + 1) . ' of ' . $n . '"' : '' ?><?= $i > 0 ? ' aria-hidden="true"' : '' ?>>
          <?php if (trim((string) $sl['kicker']) !== ''):
              $parts = preg_split('/\s+·\s+/u', trim((string) $sl['kicker']), 2); ?>
          <p class="cb-eyebrow"><span><?= esc($parts[0]) ?><?php if (isset($parts[1])): ?><span class="cb-eyebrow__more">&nbsp; · &nbsp;<?= esc($parts[1]) ?></span><?php endif ?></span></p>
          <?php endif ?>
          <?php if ($i === 0): ?>
          <h1 class="cb-display-xl cb-pre"><?= esc($sl['title']) ?></h1>
          <?php else: ?>
          <h2 class="cb-display-xl cb-pre"><?= esc($sl['title']) ?></h2>
          <?php endif ?>
          <?php if (trim((string) $sl['body']) !== ''): ?><p class="cb-lead"><?= esc($sl['body']) ?></p><?php endif ?>
          <div class="cb-hero__ctas">
            <?= cb_pill((string) $sl['cta_label'], $sl['cta_url'], 'gold') ?>
            <?php if ($i === 0 && $video !== ''): ?>
            <a class="cb-watch" <?= cb_link_attrs($vUrl) ?>>
              <span class="cb-watch__disc cb-glass"><?= cb_icon('play') ?></span><?= esc($video) ?>
            </a>
            <?php endif ?>
          </div>
        </div>
        <?php endforeach ?>
      </div>

      <?php if ($stats !== []): ?>
      <ul class="cb-stats" aria-label="Coldwell Banker at a glance">
        <?php foreach ($stats as $st): ?>
        <li class="cb-stat cb-glass"><b><?= esc($st['title']) ?></b><span><?= esc($st['subtitle']) ?></span></li>
        <?php endforeach ?>
      </ul>
      <?php endif ?>
    </div>

    <?php if ($n > 1): ?>
    <div class="cb-dots" data-hero-dots>
      <span class="cb-dots__num" aria-hidden="true">01</span>
      <?php foreach ($slides as $i => $sl): ?>
      <button type="button" class="cb-dots__bar" data-go="<?= $i ?>" aria-label="Show slide <?= $i + 1 ?> of <?= $n ?>"<?= $i === 0 ? ' aria-current="true"' : '' ?>></button>
      <?php endforeach ?>
      <span class="cb-dots__num cb-dots__num--total" aria-hidden="true"><?= sprintf('%02d', $n) ?></span>
      <button type="button" class="cb-disc cb-disc--glass" data-hero-toggle aria-pressed="false" aria-label="Pause slideshow"><?= cb_icon('pause') ?></button>
    </div>
    <?php endif ?>
  </div>
</div>
