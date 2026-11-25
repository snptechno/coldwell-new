<nav class="blog-breadcrumb" aria-label="Breadcrumb">
    <ol>
        <?php $n = count($crumbs); foreach ($crumbs as $i => [$name, $url]): ?>
            <?php if ($i === $n - 1): ?>
                <li aria-current="page"><?= esc($name) ?></li>
            <?php else: ?>
                <li><a href="<?= esc($url, 'attr') ?>"><?= esc($name) ?></a></li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ol>
</nav>
