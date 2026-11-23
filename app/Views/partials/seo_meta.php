<?php
/**
 * SEO head tags. Pass $seo = [
 *   'title', 'description', 'keywords'?, 'canonical', 'type' (website|article), 'image'?, 'image_alt'?,
 *   'robots'?, 'prev'?, 'next'?, 'article'? => [published_time, modified_time, section, author], 'jsonld'? => list<array>
 * ]
 */
$seo  = $seo ?? [];
$site = 'Coldwell Banker India';
?>
<title><?= esc($seo['title'] ?? $site) ?></title>
<meta name="description" content="<?= esc($seo['description'] ?? '') ?>" />
<?php if (! empty($seo['keywords'])): ?>
<meta name="keywords" content="<?= esc($seo['keywords']) ?>" />
<?php endif; ?>
<meta name="robots" content="<?= esc($seo['robots'] ?? 'index, follow') ?>" />
<?php if (! empty($seo['canonical'])): ?>
<link rel="canonical" href="<?= esc($seo['canonical']) ?>" />
<?php endif; ?>
<?php if (! empty($seo['prev'])): ?>
<link rel="prev" href="<?= esc($seo['prev']) ?>" />
<?php endif; ?>
<?php if (! empty($seo['next'])): ?>
<link rel="next" href="<?= esc($seo['next']) ?>" />
<?php endif; ?>
<link rel="alternate" type="application/rss+xml" title="<?= esc($site) ?> Blog" href="<?= base_url('blog/feed') ?>" />
<meta property="og:site_name" content="<?= esc($site) ?>" />
<meta property="og:locale" content="en_IN" />
<meta property="og:type" content="<?= esc($seo['type'] ?? 'website') ?>" />
<meta property="og:title" content="<?= esc($seo['title'] ?? $site) ?>" />
<meta property="og:description" content="<?= esc($seo['description'] ?? '') ?>" />
<meta property="og:url" content="<?= esc($seo['canonical'] ?? current_url()) ?>" />
<?php if (! empty($seo['image'])): ?>
<meta property="og:image" content="<?= esc($seo['image']) ?>" />
<meta property="og:image:alt" content="<?= esc($seo['image_alt'] ?? ($seo['title'] ?? '')) ?>" />
<?php endif; ?>
<?php foreach (($seo['article'] ?? []) as $k => $v): if ($v === null || $v === '') { continue; } ?>
<meta property="article:<?= esc($k) ?>" content="<?= esc($v) ?>" />
<?php endforeach; ?>
<meta name="twitter:card" content="<?= ! empty($seo['image']) ? 'summary_large_image' : 'summary' ?>" />
<meta name="twitter:site" content="@CBIndiatweet" />
<meta name="twitter:title" content="<?= esc($seo['title'] ?? $site) ?>" />
<meta name="twitter:description" content="<?= esc($seo['description'] ?? '') ?>" />
<?php if (! empty($seo['image'])): ?>
<meta name="twitter:image" content="<?= esc($seo['image']) ?>" />
<?php endif; ?>
<?php foreach (($seo['jsonld'] ?? []) as $ld): ?>
<script type="application/ld+json"><?= json_encode($ld, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?></script>
<?php endforeach; ?>
