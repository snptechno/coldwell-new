<?= '<?xml version="1.0" encoding="UTF-8"?>' . "\n" ?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:content="http://purl.org/rss/1.0/modules/content/">
<channel>
    <title>Coldwell Banker India Blog</title>
    <link><?= esc(base_url('blog')) ?></link>
    <atom:link href="<?= esc(base_url('blog/feed')) ?>" rel="self" type="application/rss+xml" />
    <description>Real estate news, advice and stories from Coldwell Banker India.</description>
    <language>en-in</language>
    <?php if (! empty($posts)): ?><lastBuildDate><?= date(DATE_RSS, strtotime($posts[0]['updated_at'] ?: $posts[0]['published_at'])) ?></lastBuildDate><?php endif; ?>

<?php foreach ($posts as $p): $url = base_url('blog/' . $p['slug']); ?>
    <item>
        <title><?= esc($p['title']) ?></title>
        <link><?= esc($url) ?></link>
        <guid isPermaLink="true"><?= esc($url) ?></guid>
        <pubDate><?= date(DATE_RSS, strtotime($p['published_at'])) ?></pubDate>
        <?php if (! empty($p['category_name'])): ?><category><?= esc($p['category_name']) ?></category><?php endif; ?>
        <description><?= esc(\App\Libraries\BlogContent::excerpt((string) ($p['excerpt'] ?: $p['content']), 300)) ?></description>
        <?php if ($p['featured_image']): ?><enclosure url="<?= esc(base_url('uploads/blog/' . rawurlencode($p['featured_image']))) ?>" length="0" type="image/<?= strtolower(pathinfo($p['featured_image'], PATHINFO_EXTENSION)) === 'png' ? 'png' : 'jpeg' ?>" /><?php endif; ?>
        <content:encoded><![CDATA[<?= str_replace(']]>', ']]]]><![CDATA[>', (string) $p['content']) ?>]]></content:encoded>
    </item>
<?php endforeach; ?>
</channel>
</rss>
