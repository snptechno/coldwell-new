<?php
/**
 * Block "archive_lists" (Figma Media 79:58 "Research & Newsletter"): two white cards on a soft
 * panel listing the newest research PDFs and newsletter editions.
 *
 * @var array<string, string> $s    block settings
 * @var string                $sfx  id suffix for repeated blocks
 */
use App\Libraries\Cms\Media;

helper('cb');
$limit = max(1, min(12, (int) ($s['limit'] ?? 4) ?: 4));
$cols  = [
    ['title' => trim($s['left_title'] ?? '') ?: 'Research & Reports', 'lead' => trim($s['left_lead'] ?? ''), 'items' => Media::research($limit), 'icon' => 'download', 'ext' => true],
    ['title' => trim($s['right_title'] ?? '') ?: 'Newsletter Archive', 'lead' => trim($s['right_lead'] ?? ''), 'items' => Media::newsletters($limit), 'icon' => 'arrow', 'ext' => false],
];
if ($cols[0]['items'] === [] && $cols[1]['items'] === []) {
    return;
}
?>
<section class="cb-archive" aria-label="Research and newsletter archive">
  <div class="cb-archive__panel">
    <?php foreach ($cols as $i => $c): if ($c['items'] === []) { continue; } $hid = 'cb-arch-' . $i . $sfx; ?>
    <div class="cb-archive__card cb-reveal" aria-labelledby="<?= esc($hid, 'attr') ?>" role="region">
      <h2 id="<?= esc($hid, 'attr') ?>"><?= esc($c['title']) ?></h2>
      <?php if ($c['lead'] !== ''): ?><p class="cb-archive__lead"><?= esc($c['lead']) ?></p><?php endif ?>
      <ul>
        <?php foreach ($c['items'] as $it): ?>
        <li><a class="cb-doc" href="<?= esc($it['href'], 'attr') ?>"<?= $c['ext'] ? ' target="_blank" rel="noopener"' : '' ?>>
          <span class="cb-doc__icon" aria-hidden="true"><?= cb_icon($i === 0 ? 'chart' : 'mail') ?></span>
          <span class="cb-doc__txt"><b><?= esc($it['title']) ?></b><span><?= esc($it['meta']) ?></span></span>
          <span class="cb-doc__go" aria-hidden="true"><?= cb_icon($c['icon']) ?></span>
        </a></li>
        <?php endforeach ?>
      </ul>
    </div>
    <?php endforeach ?>
  </div>
</section>
