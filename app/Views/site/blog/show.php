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
        <section class="inner_banner_container blog-banner-slim">
            <div class=""><div class="slide-txt"><div class="fr-main-left algn-left"></div></div></div>
        </section>

        <section class="blog-sec">
            <div class="container">
                <?= $this->include('site/blog/_breadcrumb') ?>
                <div class="blog-layout">
                    <div class="blog-main">
                        <div class="blog-post">
                            <header>
                                <?php if (! empty($post['category_slug'])): ?>
                                    <a class="blog-cat" href="<?= base_url('blog/category/' . $post['category_slug']) ?>"><?= esc($post['category_name']) ?></a>
                                <?php endif; ?>
                                <h1><?= esc($post['title']) ?></h1>
                                <div class="blog-meta">
                                    <time datetime="<?= esc(date('c', strtotime($post['published_at'])), 'attr') ?>"><?= esc(date('d F Y', strtotime($post['published_at']))) ?></time>
                                    <?php if ($post['author']): ?> &middot; <?= esc($post['author']) ?><?php endif; ?>
                                </div>
                            </header>
                            <?php if ($post['featured_image']): ?>
                                <figure class="blog-hero">
                                    <img src="<?= base_url('uploads/blog/' . rawurlencode($post['featured_image'])) ?>" alt="<?= esc($post['image_alt'] ?: $post['title'], 'attr') ?>">
                                </figure>
                            <?php endif; ?>
                            <div class="blog-content">
                                <?= $post['content'] /* sanitised on save/import by App\Libraries\BlogContent::sanitize */ ?>
                            </div>
                            <div class="blog-share">
                                <span>Share:</span>
                                <?php $u = rawurlencode(base_url('blog/' . $post['slug'])); $t = rawurlencode($post['title']); ?>
                                <a href="https://www.facebook.com/sharer/sharer.php?u=<?= $u ?>" target="_blank" rel="noopener nofollow">Facebook</a>
                                <a href="https://twitter.com/intent/tweet?url=<?= $u ?>&amp;text=<?= $t ?>" target="_blank" rel="noopener nofollow">X / Twitter</a>
                                <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= $u ?>" target="_blank" rel="noopener nofollow">LinkedIn</a>
                                <a href="https://wa.me/?text=<?= $t ?>%20<?= $u ?>" target="_blank" rel="noopener nofollow">WhatsApp</a>
                            </div>
                        </div>

                        <?php if (! empty($related)): ?>
                        <section class="blog-related">
                            <h3>Related posts</h3>
                            <div class="blog-grid">
                                <?php foreach ($related as $r): $url = base_url('blog/' . $r['slug']); ?>
                                    <article class="blog-card">
                                        <a href="<?= $url ?>" class="blog-card-img">
                                            <?php if ($r['featured_image']): ?>
                                                <img src="<?= base_url('uploads/blog/' . rawurlencode($r['featured_image'])) ?>" alt="<?= esc($r['image_alt'] ?: $r['title'], 'attr') ?>" loading="lazy" width="400" height="250">
                                            <?php else: ?><span class="blog-card-noimg"></span><?php endif; ?>
                                        </a>
                                        <div class="blog-card-body">
                                            <div class="blog-meta"><?= esc(date('d M Y', strtotime($r['published_at']))) ?></div>
                                            <h2><a href="<?= $url ?>"><?= esc($r['title']) ?></a></h2>
                                        </div>
                                    </article>
                                <?php endforeach; ?>
                            </div>
                        </section>
                        <?php endif; ?>
                        <p><a href="<?= base_url('blog') ?>" class="blog-more">&larr; Back to all posts</a></p>
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
