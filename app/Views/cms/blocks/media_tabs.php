<?php
/**
 * Block "media_tabs" (Figma 140:232 "Section/Media Tabs"): centred pill navigation bar
 * linking across the Media Center sections.
 *
 * @var array<string, string> $s block settings
 */
$active = strtolower(trim($s['active'] ?? 'videos'));
$tabs   = [
    'news'        => ['CB India News', site_url('media-center')],
    'industry'    => ['Industry News', site_url('industry-news')],
    'knowledge'   => ['Knowledge Series', site_url('knowledge-series')],
    'awards'      => ['Awards', site_url('awards')],
    'videos'      => ['Videos', site_url('finding-a-home')],
    'newsletter'  => ['Newsletter', site_url('newsletter')],
    'testimonials'=> ['Testimonials', site_url('testimonials')],
];
?>
<nav class="cb-media-tabs" aria-label="Media sections">
  <div class="cb-wrap">
    <div class="cb-tabs">
      <?php foreach ($tabs as $key => [$label, $href]): ?>
      <a class="cb-tab" href="<?= esc($href, 'attr') ?>"<?= $key === $active ? ' aria-current="page" aria-selected="true"' : ' aria-selected="false"' ?>><?= esc($label) ?></a>
      <?php endforeach ?>
    </div>
  </div>
</nav>
