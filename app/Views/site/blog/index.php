<!doctype html>
<html lang="en">

<head>
	<?= $this->include('partials/seo_meta') ?>
	<?= $this->include('partials/header') ?>
	<link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/blog.css') ?>" />
	<?= $this->include('partials/ga_code_header') ?>
</head>

<body>
    <article class="blog-page aos-all">
        <section>
            <div class="container">
                <?php $this->setVar('act', $page_active); ?><?= $this->include('partials/navigation') ?>
            </div>
        </section>
        <section class="inner_banner_container">
            <div class="">
                <div class="slide-txt">
                    <div class="fr-main-left algn-left">
                        <h1><?= esc($category['name'] ?? 'Blog') ?></h1>
                        <span class="txt-para"><?= esc($category['description'] ?? 'Real estate news, advice and stories from Coldwell Banker India.') ?></span>
                    </div>
                </div>
            </div>
        </section>

        <section class="blog-sec">
            <div class="container">
                <?= $this->include('site/blog/_breadcrumb') ?>
                <div class="blog-layout">
                    <div class="blog-main">
                        <?php if ($posts === []): ?>
                            <p class="blog-empty">No posts yet. Please check back soon.</p>
                        <?php endif; ?>
                        <div class="blog-grid">
                        <?php foreach ($posts as $i => $p): $url = base_url('blog/' . $p['slug']); ?>
                            <article class="blog-card">
                                <a href="<?= $url ?>" class="blog-card-img">
                                    <?php if ($p['featured_image']): ?>
                                        <img src="<?= base_url('uploads/blog/' . rawurlencode($p['featured_image'])) ?>" alt="<?= esc($p['image_alt'] ?: $p['title'], 'attr') ?>" <?= $i > 2 ? 'loading="lazy"' : '' ?> width="400" height="250">
                                    <?php else: ?>
                                        <span class="blog-card-noimg"></span>
                                    <?php endif; ?>
                                </a>
                                <div class="blog-card-body">
                                    <div class="blog-meta">
                                        <time datetime="<?= esc(date('c', strtotime($p['published_at'])), 'attr') ?>"><?= esc(date('d M Y', strtotime($p['published_at']))) ?></time>
                                        <?php if (! empty($p['category_slug'])): ?>
                                            &middot; <a href="<?= base_url('blog/category/' . $p['category_slug']) ?>"><?= esc($p['category_name']) ?></a>
                                        <?php endif; ?>
                                    </div>
                                    <h2><a href="<?= $url ?>"><?= esc($p['title']) ?></a></h2>
                                    <p><?= esc(\App\Libraries\BlogContent::excerpt((string) ($p['excerpt'] ?: $p['content']), 170)) ?></p>
                                    <a href="<?= $url ?>" class="blog-more">Read more <span aria-hidden="true">&rarr;</span></a>
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
                    </div>
                    <?= $this->include('site/blog/_sidebar') ?>
                </div>
            </div>
        </section>

        <div class="clear"></div>
        <?= $this->include('partials/sub_footer') ?>
    </article>
    <?= $this->include('partials/footer') ?>
</body>
</html>
