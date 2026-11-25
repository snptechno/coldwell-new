<?php
helper('cb');
$asset = static fn (string $f): string => base_url($f) . '?v=' . @filemtime(FCPATH . $f);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?= $this->include('partials/seo_meta') ?>
  <meta name="theme-color" content="#0F1F3D">
  <link rel="shortcut icon" href="<?= esc(base_url('assets/images/favicon.ico'), 'attr') ?>">
  <link rel="stylesheet" href="<?= esc($asset('assets/css/fonts.css'), 'attr') ?>">
  <link rel="stylesheet" href="<?= esc($asset('assets/css/cb-site.css'), 'attr') ?>">
  <link rel="stylesheet" href="<?= esc($asset('assets/css/blog.css'), 'attr') ?>">
  <?= $this->include('partials/tracking') ?>
  <?= $this->include('partials/ga_code_header') ?>
</head>
<body class="cb-page">
<a class="cb-skip" href="#main">Skip to content</a>
<main>
  <div class="cb-hero">
    <div class="cb-hero__card cb-hero__card--page">
      <?= view('cms/partials/nav', ['page_active' => 'media center', 'current_url' => current_url()], ['saveData' => false]) ?>
      <div class="cb-hero__slides" aria-hidden="true">
        <div class="cb-slide is-active">
          <div class="cb-slide__img"><?= cb_picture('assets/images/home-v2/news-press.jpg', '', ['loading' => 'eager', 'fetchpriority' => 'high', 'width' => '1536', 'height' => '864']) ?></div>
          <div class="cb-slide__shade"></div>
        </div>
      </div>
      <div class="cb-hero__body" id="main" tabindex="-1">
        <div class="cb-phero__copy">
          <p class="cb-eyebrow"><span>Home  ·  Media  ·  Blog</span></p>
          <h1 class="cb-display-l"><?= esc($category['name'] ?? 'Blog & Insights') ?></h1>
          <p class="cb-lead"><?= esc($category['description'] ?? 'Real estate news, advice and stories from Coldwell Banker India.') ?></p>
        </div>
      </div>
    </div>
  </div>

  <section class="cb-blog-sec">
    <div class="cb-wrap">
      <?= $this->include('site/blog/_breadcrumb') ?>
      <div class="blog-layout">
        <div class="blog-main">
          <?php if ($posts === []): ?>
            <p class="cb-p cb-research-empty">No posts yet. Please check back soon.</p>
          <?php else: ?>
          <div class="blog-grid">
            <?php foreach ($posts as $i => $p): $url = base_url('blog/' . $p['slug']); ?>
            <article class="blog-card">
              <a href="<?= esc($url, 'attr') ?>" class="blog-card-img">
                <?php if ($p['featured_image']): ?>
                  <img src="<?= esc(base_url('uploads/blog/' . rawurlencode($p['featured_image'])), 'attr') ?>" alt="<?= esc($p['image_alt'] ?: $p['title'], 'attr') ?>" <?= $i > 2 ? 'loading="lazy" ' : '' ?>decoding="async" width="400" height="250">
                <?php else: ?>
                  <span class="blog-card-noimg"></span>
                <?php endif; ?>
              </a>
              <div class="blog-card-body">
                <div class="blog-meta">
                  <?php if (! empty($p['category_slug'])): ?>
                    <a class="cb-chip" href="<?= esc(base_url('blog/category/' . $p['category_slug']), 'attr') ?>"><?= esc($p['category_name']) ?></a>
                  <?php endif; ?>
                  <time datetime="<?= esc(date('c', strtotime($p['published_at'])), 'attr') ?>"><?= esc(date('d M Y', strtotime($p['published_at']))) ?></time>
                </div>
                <h2><a href="<?= esc($url, 'attr') ?>"><?= esc($p['title']) ?></a></h2>
                <p><?= esc(\App\Libraries\BlogContent::excerpt((string) ($p['excerpt'] ?: $p['content']), 160)) ?></p>
                <a href="<?= esc($url, 'attr') ?>" class="cb-pill cb-pill--light cb-pill--on-section cb-pill--sm">
                  <span>Read More</span><span class="cb-pill__icon"><?= cb_icon('arrow') ?></span>
                </a>
              </div>
            </article>
            <?php endforeach; ?>
          </div>

          <?php if ($last_page > 1): ?>
          <nav class="blog-pager" aria-label="Pagination">
            <?php if ($page > 1): ?><a href="<?= esc($page_url($page - 1), 'attr') ?>" rel="prev">&larr; Newer</a><?php endif; ?>
            <?php for ($n = 1; $n <= $last_page; $n++): ?>
              <?php if ($n === $page): ?><span class="current" aria-current="page"><?= $n ?></span>
              <?php else: ?><a href="<?= esc($page_url($n), 'attr') ?>"><?= $n ?></a><?php endif; ?>
            <?php endfor; ?>
            <?php if ($page < $last_page): ?><a href="<?= esc($page_url($page + 1), 'attr') ?>" rel="next">Older &rarr;</a><?php endif; ?>
          </nav>
          <?php endif; ?>
          <?php endif; ?>
        </div>
        <?= $this->include('site/blog/_sidebar') ?>
      </div>
    </div>
  </section>
</main>
<?= view('cms/partials/footer', [], ['saveData' => false]) ?>
<script src="<?= esc($asset('assets/js/cb-site.js'), 'attr') ?>" defer></script>
</body>
</html>
