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
<?= view('cms/partials/header_bar', ['page_active' => 'media center', 'current_url' => current_url()], ['saveData' => false]) ?>
<main id="main" tabindex="-1">
  <section class="cb-blog-sec">
    <div class="cb-wrap">
      <?= $this->include('site/blog/_breadcrumb') ?>
      <div class="blog-layout">
        <div class="blog-main">
          <article class="blog-post">
            <header class="cb-blog-post__head">
              <?php if (! empty($post['category_slug'])): ?>
                <a class="cb-chip" href="<?= esc(base_url('blog/category/' . $post['category_slug']), 'attr') ?>"><?= esc($post['category_name']) ?></a>
              <?php endif; ?>
              <h1 class="cb-h2"><?= esc($post['title']) ?></h1>
              <div class="blog-meta">
                <time datetime="<?= esc(date('c', strtotime($post['published_at'])), 'attr') ?>"><?= esc(date('d F Y', strtotime($post['published_at']))) ?></time>
                <?php if ($post['author']): ?> &middot; <?= esc($post['author']) ?><?php endif; ?>
              </div>
            </header>
            <?php if ($post['featured_image']): ?>
              <figure class="blog-hero">
                <img src="<?= esc(base_url('uploads/blog/' . rawurlencode($post['featured_image'])), 'attr') ?>" alt="<?= esc($post['image_alt'] ?: $post['title'], 'attr') ?>" decoding="async">
              </figure>
            <?php endif; ?>
            <div class="blog-content">
              <?= $post['content'] /* sanitised on save/import by App\Libraries\BlogContent::sanitize */ ?>
            </div>
            <div class="blog-share">
              <span>Share:</span>
              <?php $u = rawurlencode(base_url('blog/' . $post['slug'])); $t = rawurlencode($post['title']); ?>
              <a href="https://www.facebook.com/sharer/sharer.php?u=<?= $u ?>" target="_blank" rel="noopener noreferrer nofollow">Facebook</a>
              <a href="https://twitter.com/intent/tweet?url=<?= $u ?>&amp;text=<?= $t ?>" target="_blank" rel="noopener noreferrer nofollow">X / Twitter</a>
              <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= $u ?>" target="_blank" rel="noopener noreferrer nofollow">LinkedIn</a>
              <a href="https://wa.me/?text=<?= $t ?>%20<?= $u ?>" target="_blank" rel="noopener noreferrer nofollow">WhatsApp</a>
            </div>
          </article>

          <?php if (! empty($related)): ?>
          <section class="blog-related">
            <h2 class="cb-h2 cb-blog-related__head">Related Posts</h2>
            <div class="blog-grid">
              <?php foreach ($related as $r): $url = base_url('blog/' . $r['slug']); ?>
              <article class="blog-card">
                <a href="<?= esc($url, 'attr') ?>" class="blog-card-img">
                  <?php if ($r['featured_image']): ?>
                    <img src="<?= esc(base_url('uploads/blog/' . rawurlencode($r['featured_image'])), 'attr') ?>" alt="<?= esc($r['image_alt'] ?: $r['title'], 'attr') ?>" loading="lazy" decoding="async" width="400" height="250">
                  <?php else: ?><span class="blog-card-noimg"></span><?php endif; ?>
                </a>
                <div class="blog-card-body">
                  <div class="blog-meta"><?= esc(date('d M Y', strtotime($r['published_at']))) ?></div>
                  <h3><a href="<?= esc($url, 'attr') ?>"><?= esc($r['title']) ?></a></h3>
                </div>
              </article>
              <?php endforeach; ?>
            </div>
          </section>
          <?php endif; ?>
          <p class="cb-blog-back">
            <a href="<?= esc(base_url('blog'), 'attr') ?>" class="cb-pill cb-pill--light cb-pill--on-section cb-pill--sm">
              <span>Back to All Posts</span><span class="cb-pill__icon"><?= cb_icon('prev') ?></span>
            </a>
          </p>
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
