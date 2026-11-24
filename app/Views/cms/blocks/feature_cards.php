<?php
/**
 * Block "feature_cards" (Figma About 73:58 "Why Coldwell Banker"): navy panel with a soft glow,
 * centred heading and 2-column glass cards. A card can carry a logo chip; text lines that
 * start with "- " render as a gold checklist.
 *
 * @var array<string, string>      $s      block settings
 * @var array<string, list<array>> $items  card
 * @var string                     $sfx    id suffix for repeated blocks
 */
helper('cb');
$cards = $items['card'] ?? [];
$id    = 'cb-features-title' . $sfx;
?>
<section class="cb-features" aria-labelledby="<?= esc($id, 'attr') ?>">
  <div class="cb-features__panel">
    <div class="cb-head cb-reveal">
      <h2 class="cb-h2" id="<?= esc($id, 'attr') ?>"><?= esc(trim($s['title'] ?? '')) ?></h2>
      <?php if (trim($s['lead'] ?? '') !== ''): ?><p class="cb-p"><?= esc(trim($s['lead'])) ?></p><?php endif ?>
    </div>
    <ul class="cb-features__grid">
      <?php foreach ($cards as $c):
          $paras = []; $list = [];
          foreach (preg_split('/\R/u', trim((string) $c['body'])) as $line) {
              $line = trim($line);
              if ($line === '') { continue; }
              if (str_starts_with($line, '- ')) { $list[] = trim(substr($line, 2)); } else { $paras[] = $line; }
          }
      ?>
      <li class="cb-fcard cb-reveal">
        <?php if (($logo = cb_picture($c['image'], (string) ($c['image_alt'] ?: $c['title']), ['height' => '53'])) !== ''): ?>
        <span class="cb-fcard__logo"><?= $logo ?></span>
        <?php endif ?>
        <h3><?= esc((string) $c['title']) ?></h3>
        <?php foreach ($paras as $para): ?><p><?= esc($para) ?></p><?php endforeach ?>
        <?php if ($list !== []): ?>
        <ul class="cb-checklist">
          <?php foreach ($list as $li): ?><li><span aria-hidden="true"><?= cb_icon('check') ?></span><?= esc($li) ?></li><?php endforeach ?>
        </ul>
        <?php endif ?>
      </li>
      <?php endforeach ?>
    </ul>
  </div>
</section>
