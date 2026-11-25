<aside class="blog-sidebar">
    <div class="blog-widget">
        <h4>Categories</h4>
        <ul>
            <li><a href="<?= base_url('blog') ?>" class="<?= empty($category) && empty($post) ? 'active' : '' ?>">All posts</a></li>
            <?php foreach ($categories as $c): ?>
                <li><a href="<?= base_url('blog/category/' . $c['slug']) ?>" class="<?= (! empty($category) && $category['id'] == $c['id']) || (! empty($post['category_id']) && $post['category_id'] == $c['id']) ? 'active' : '' ?>"><?= esc($c['name']) ?> <span>(<?= (int) $c['post_count'] ?>)</span></a></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php if (! empty($recent)): ?>
    <div class="blog-widget">
        <h4>Recent posts</h4>
        <ul class="blog-recent">
            <?php foreach ($recent as $r): ?>
                <li>
                    <a href="<?= base_url('blog/' . $r['slug']) ?>">
                        <?php if ($r['featured_image']): ?><img src="<?= base_url('uploads/blog/' . rawurlencode($r['featured_image'])) ?>" alt="" loading="lazy" width="64" height="48"><?php endif; ?>
                        <span><?= esc($r['title']) ?><small><?= esc(date('d M Y', strtotime($r['published_at']))) ?></small></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>
    <div class="blog-widget">
        <a href="<?= base_url('blog/feed') ?>" class="blog-rss">RSS feed</a>
    </div>
</aside>
