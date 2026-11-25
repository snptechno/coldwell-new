<?php
/**
 * Block "sitemap" (Figma Site Map – v2, 145:178): link-group cards, 3 per row, first card navy.
 * Each line of a group's body is "Label | path"; links go through CmsUrl::href() and lines that
 * do not resolve to a safe link are skipped.
 *
 * @var array<string, list<array>> $items  group
 */
use App\Libraries\Cms\BlockTypes;
use App\Libraries\Cms\CmsUrl;

helper('cb');
$groups = $items['group'] ?? [];
$icons  = BlockTypes::ICONS;
?>
<section class="cb-sitemap" aria-label="Site map">
  <div class="cb-wrap">
    <div class="cb-sitemap__grid">
      <?php foreach ($groups as $gi => $g): $ic = isset($icons[$g['kicker'] ?? '']) ? $g['kicker'] : 'globe'; ?>
      <nav class="cb-smcard<?= $gi === 0 ? ' cb-smcard--feature' : '' ?>" aria-label="<?= esc((string) $g['title'], 'attr') ?>">
        <h2><span class="cb-smcard__icon" aria-hidden="true"><?= cb_icon($ic) ?></span><?= esc((string) $g['title']) ?></h2>
        <ul>
          <?php foreach (preg_split('/\R/u', (string) $g['body']) as $line):
              [$label, $path] = array_pad(array_map('trim', explode('|', $line, 2)), 2, '');
              $href = $label !== '' && $path !== '' ? CmsUrl::href($path) : '';
              if ($href === '') { continue; } ?>
          <li><a href="<?= esc($href, 'attr') ?>"<?= CmsUrl::isExternal($href) ? ' target="_blank" rel="noopener"' : '' ?>><span><?= esc($label) ?></span><?= cb_icon('arrow') ?></a></li>
          <?php endforeach ?>
        </ul>
      </nav>
      <?php endforeach ?>
    </div>
  </div>
</section>
