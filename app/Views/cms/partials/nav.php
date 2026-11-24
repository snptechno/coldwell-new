<?php
/**
 * Site header: logo chip, glass pill nav (desktop), menu button + drawer (<1200px).
 * Menu = CMS menu "main" (Admin > CMS > Menus); button = settings nav_cta_label / nav_cta_url.
 * An item is current when $page_active is one of its active_keys or its link is this page.
 *
 * @var string $page_active
 * @var string $current_url
 */
use App\Libraries\Cms\Cms;
use App\Libraries\Cms\CmsUrl;

helper('cb');
$act   = (string) ($page_active ?? '');
$here  = rtrim((string) ($current_url ?? ''), '/');
$menu  = Cms::menu('main');
$isCur = static fn (array $m): bool => ($act !== '' && in_array($act, $m['active_keys'], true)) || ($here !== '' && rtrim($m['href'], '/') === $here);
$aCur  = static fn (array $m): string => $isCur($m) ? ' aria-current="page"' : '';
$tgt   = static fn (array $m): string => $m['new_tab'] ? ' target="_blank" rel="noopener"' : '';

$ctaLbl = Cms::setting('nav_cta_label');
$ctaUrl = Cms::setting('nav_cta_url');
$cta    = $ctaLbl !== '' && CmsUrl::href($ctaUrl) !== '' ? cb_pill($ctaLbl, $ctaUrl, 'gold') : '';
$logo   = '<a class="cb-logo" href="' . esc(site_url('/'), 'attr') . '" aria-label="Coldwell Banker India — home">' . "\n"
        . '      <img src="' . esc(base_url('assets/images/home-v2/logo.png'), 'attr') . '" alt="Coldwell Banker India" width="74" height="60">' . "\n"
        . '    </a>';
?>
<header class="cb-nav">
  <a class="cb-logo" href="<?= esc(site_url('/'), 'attr') ?>" aria-label="Coldwell Banker India — home">
    <img src="<?= esc(base_url('assets/images/home-v2/logo.png'), 'attr') ?>" alt="Coldwell Banker India" width="74" height="60">
  </a>
  <?php if ($menu !== []): ?>
  <nav aria-label="Main">
    <ul class="cb-menu cb-glass">
      <?php foreach ($menu as $i => $m): ?>
        <?php if ($m['children'] !== []): $cur = $isCur($m); foreach ($m['children'] as $c) { $cur = $cur || $isCur($c); } ?>
        <li>
          <button type="button" class="cb-menu__btn<?= $cur ? ' is-current' : '' ?>" aria-expanded="false" aria-controls="cb-sub-<?= (int) $i ?>"><?= esc($m['label']) ?><?= cb_icon('chev') ?></button>
          <ul class="cb-sub" id="cb-sub-<?= (int) $i ?>">
            <?php foreach ($m['children'] as $c): ?>
            <li><a href="<?= esc($c['href'], 'attr') ?>"<?= $aCur($c) . $tgt($c) ?>><?= esc($c['label']) ?></a></li>
            <?php endforeach ?>
          </ul>
        </li>
        <?php else: ?>
        <li><a href="<?= esc($m['href'], 'attr') ?>"<?= $aCur($m) . $tgt($m) ?>><?= esc($m['label']) ?></a></li>
        <?php endif ?>
      <?php endforeach ?>
    </ul>
  </nav>
  <?php endif ?>
  <div class="cb-nav__actions">
    <?= $cta ?>
    <button type="button" class="cb-disc cb-disc--glass cb-burger" aria-label="Open menu" aria-expanded="false" aria-controls="cb-drawer"><?= cb_icon('menu') ?></button>
  </div>
</header>

<div class="cb-drawer" id="cb-drawer" role="dialog" aria-modal="true" aria-label="Menu" hidden>
  <div class="cb-drawer__top">
    <?= $logo ?>
    <button type="button" class="cb-disc cb-disc--glass" data-drawer-close aria-label="Close menu"><?= cb_icon('close') ?></button>
  </div>
  <ul class="cb-drawer__list">
    <?php foreach ($menu as $m): ?>
      <?php if ($m['children'] !== []): ?>
      <li class="cb-drawer__group"><?= esc($m['label']) ?></li>
        <?php foreach ($m['children'] as $c): ?>
        <li><a class="cb-drawer__child" href="<?= esc($c['href'], 'attr') ?>"<?= $aCur($c) . $tgt($c) ?>><?= esc($c['label']) ?></a></li>
        <?php endforeach ?>
      <?php else: ?>
      <li><a href="<?= esc($m['href'], 'attr') ?>"<?= $aCur($m) . $tgt($m) ?>><?= esc($m['label']) ?></a></li>
      <?php endif ?>
    <?php endforeach ?>
  </ul>
  <?= $cta ?>
</div>
