<?php
/**
 * Public CMS page / article (docs/CMS.md "Public rendering"). Rendered by Site\CmsPage.
 * Design: Figma "Home – Elegant v2" 41:41 (+ responsive frames 100:57); styles in
 * assets/css/cb-site.css, behaviour in assets/js/cb-site.js.
 *
 * @var array<string, mixed>             $page        cms_pages row
 * @var list<array<string, mixed>>       $blocks      active blocks (settings + items)
 * @var bool                             $is_home
 * @var bool                             $preview
 * @var string                           $page_active menu highlight key
 * @var string                           $current_url
 * @var array<string, mixed>             $seo
 */
use App\Libraries\Cms\BlockTypes;
use App\Libraries\Cms\Cms;
use App\Libraries\Cms\CmsUrl;

helper('cb');
$asset     = static fn (string $f): string => base_url($f) . '?v=' . @filemtime(FCPATH . $f);
$first     = $blocks[0] ?? null;
$heroFirst = $first !== null && BlockTypes::isHero($first['type']);
$isArticle = $page['type'] === 'article';
$canonical = $is_home ? site_url('/') : site_url($page['slug']);
$nav       = ['page_active' => $page_active, 'current_url' => $current_url];

// Preload the LCP image: the first slide / page hero photo (WEBP when a sibling exists).
$heroImg = '';
if ($heroFirst) {
    $heroImg = $first['type'] === 'hero_slider' ? (string) ($first['items']['slide'][0]['image'] ?? '') : (string) ($first['settings']['image'] ?? '');
}

$sameAs = [];
foreach (Cms::menu('social') as $s) {
    if (preg_match('#^https?://#i', $s['href'])) {
        $sameAs[] = $s['href'];
    }
}

// Ids stay as in the approved design for the first block of a type; repeats get a suffix.
$seen   = [];
$render = static function (array $block, int $index) use (&$seen, $page, $is_home, $nav): string {
    $type          = $block['type'];
    $sfx           = isset($seen[$type]) ? '-' . (int) $block['id'] : '';
    $seen[$type]   = true;

    return view('cms/blocks/' . $type, $nav + [
        'block' => $block, 's' => $block['settings'], 'items' => $block['items'], 'sfx' => $sfx,
        'index' => $index, 'page' => $page, 'is_home' => $is_home,
    ], ['saveData' => false]);
};
?>
<!doctype html>
<html lang="en" class="no-js">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= esc($seo['title']) ?></title>
  <meta name="description" content="<?= esc($seo['description'], 'attr') ?>">
  <meta name="keywords" content="<?= esc($seo['keywords'], 'attr') ?>">
  <?php if ($seo['noindex'] || $preview): ?>
  <meta name="robots" content="noindex, nofollow">
  <?php endif ?>
  <meta property="og:title" content="<?= esc($seo['title'], 'attr') ?>">
  <meta property="og:description" content="<?= esc($seo['description'], 'attr') ?>">
  <?php if ($seo['image'] !== ''): ?>
  <meta property="og:image" content="<?= esc($seo['image'], 'attr') ?>">
  <?php endif ?>
  <?php if ($isArticle): ?>
  <meta property="og:type" content="article">
  <?php endif ?>
  <meta name="theme-color" content="#0F1F3D">
  <link rel="canonical" href="<?= esc($canonical, 'attr') ?>">
  <link rel="shortcut icon" href="<?= esc(base_url('assets/images/favicon.ico'), 'attr') ?>">
  <?php if ($heroImg !== '' && ($hw = CmsUrl::webpUrl($heroImg)) !== ''): ?>
  <?php /* same srcset/sizes as the hero <picture>, so phones preload the small variant instead of the full image */ ?>
  <link rel="preload" as="image" href="<?= esc($hw, 'attr') ?>" imagesrcset="<?= esc(cb_webp_srcset($heroImg, $hw), 'attr') ?>" imagesizes="100vw" type="image/webp" fetchpriority="high">
  <?php endif ?>
  <link rel="preload" as="font" href="<?= esc(base_url('assets/fonts/raleway-light-webfont.woff2'), 'attr') ?>" type="font/woff2" crossorigin>
  <link rel="preload" as="font" href="<?= esc(base_url('assets/fonts/raleway-bold-webfont.woff2'), 'attr') ?>" type="font/woff2" crossorigin>
  <link rel="stylesheet" href="<?= esc($asset('assets/css/fonts.css'), 'attr') ?>">
  <link rel="stylesheet" href="<?= esc($asset('assets/css/cb-site.css'), 'attr') ?>">
  <script>document.documentElement.classList.remove('no-js');</script>
  <script type="application/ld+json"><?= json_encode([
      '@context' => 'https://schema.org', '@type' => 'Organization', 'name' => 'Coldwell Banker India',
      'url' => site_url('/'), 'logo' => base_url('assets/images/home-v2/logo.png'),
      'sameAs' => $sameAs,
  ], JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP) ?></script>
  <?= $this->include('partials/tracking') ?>
  <?= $this->include('partials/ga_code_header') ?>
</head>
<body class="cb-page">
<a class="cb-skip" href="#main">Skip to content</a>
<?php if ($preview): ?>
<p class="cb-preview" role="status">Preview<?= (int) $page['status'] !== 1 ? ' · draft' : '' ?></p>
<?php endif ?>

<?php if ($heroFirst): ?>
<main>
<?= $render($first, 0) ?>
<?php else: ?>
<?= view('cms/partials/header_bar', $nav, ['saveData' => false]) ?>
<main id="main" tabindex="-1">
<?php if (! $isArticle): ?>
<h1 class="cb-sr"><?= esc($page['title']) ?></h1>
<?php endif ?>
<?php endif ?>
<?php if ($isArticle): ?>
<?= view('cms/partials/article', ['page' => $page], ['saveData' => false]) ?>
<?php endif ?>
<?php
// The newsletter band sits outside <main>, directly above the footer (as in the design).
$tail = [];
foreach ($blocks as $i => $block) {
    if ($heroFirst && $i === 0) {
        continue;
    }
    if ($block['type'] === 'newsletter_cta' && $i === array_key_last($blocks)) {
        $tail[] = [$block, $i];

        continue;
    }
    echo $render($block, $i), "\n";
}
?>
</main>

<?php foreach ($tail as [$block, $i]): ?>
<?= $render($block, $i) ?>
<?php endforeach ?>
<?= view('cms/partials/footer', [], ['saveData' => false]) ?>

<script src="<?= esc($asset('assets/js/cb-site.js'), 'attr') ?>" defer></script>
</body>
</html>
