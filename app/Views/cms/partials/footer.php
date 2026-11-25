<?php
/**
 * Site footer (Figma 41:41 "Footer").
 * Columns = CMS menus footer_quick / footer_network / footer_media (headings from settings),
 * icons = menu "social", bottom row = menu "footer_legal", contacts + copyright = settings.
 * Empty menus are left out.
 */
use App\Libraries\Cms\Cms;
use App\Libraries\Cms\SiteSchema;

helper('cb');
$email   = Cms::setting('footer_email');
$phone   = Cms::setting('footer_phone');
$tel     = (string) preg_replace('/[^0-9+]/', '', $phone);
$address = Cms::setting('footer_address');
$cols    = [];
foreach (SiteSchema::FOOTER_HEADINGS as $location => $headingKey) {
    $links = Cms::menu($location);
    if ($links !== []) {
        if ($location === 'footer_network') {
            foreach ($links as &$l) {
                $lbl = trim((string) ($l['label'] ?? ''));
                if ($lbl !== '' && preg_match('~/our-franchise(?:#.*)?$~i', (string) ($l['href'] ?? '')) === 1) {
                    $base      = preg_replace('~#.*$~', '', (string) $l['href']);
                    $l['href'] = $base . '?city=' . rawurlencode($lbl) . '#offices';
                }
            }
            unset($l);
        }
        $default = trim((string) preg_replace('/^.*—\s*/u', '', SiteSchema::MENUS[$location]['name']));
        $cols[]  = [Cms::setting($headingKey, $default), $links];
    }
}
$social    = Cms::menu('social');
$legal     = Cms::menu('footer_legal');
$copyright = str_replace('{year}', date('Y'), Cms::setting('footer_copyright', '© {year} Coldwell Banker India. All rights reserved.'));
$tgt       = static fn (array $m): string => $m['new_tab'] ? ' target="_blank" rel="noopener"' : '';
?>
<footer class="cb-footer">
  <div class="cb-wrap">
    <div class="cb-footer__grid">
      <div class="cb-footer__brand">
        <a class="cb-logo" href="<?= esc(site_url('/'), 'attr') ?>" aria-label="Coldwell Banker India — home">
          <img src="<?= esc(base_url('assets/images/home-v2/logo.png'), 'attr') ?>" alt="Coldwell Banker India" width="84" height="68" loading="lazy">
        </a>
        <?php if ($email !== ''): ?><?php if (filter_var($email, FILTER_VALIDATE_EMAIL)): ?><a href="mailto:<?= esc($email, 'attr') ?>"><?= esc($email) ?></a><?php else: ?><span><?= esc($email) ?></span><?php endif ?><?php endif ?>
        <?php if ($phone !== ''): ?><?php if (strlen($tel) >= 3): ?><a href="tel:<?= esc($tel, 'attr') ?>"><?= esc($phone) ?></a><?php else: ?><span><?= esc($phone) ?></span><?php endif ?><?php endif ?>
        <?php if ($address !== ''): ?><address style="font-style:normal" class="cb-pre"><?= esc($address) ?></address><?php endif ?>
      </div>
      <?php foreach ($cols as [$title, $links]): ?>
      <nav aria-label="<?= esc($title, 'attr') ?>">
        <h2><?= esc($title) ?></h2>
        <ul>
          <?php foreach ($links as $l): ?>
          <li><a href="<?= esc($l['href'], 'attr') ?>"<?= $tgt($l) ?>><?= esc($l['label']) ?></a></li>
          <?php endforeach ?>
        </ul>
      </nav>
      <?php endforeach ?>
      <?php if ($social !== []): ?>
      <div>
        <h2><?= esc(Cms::setting('footer_social_title', 'Follow us')) ?></h2>
        <ul class="cb-social">
          <?php foreach ($social as $s): ?>
          <li><a href="<?= esc($s['href'], 'attr') ?>" target="_blank" rel="noopener" aria-label="Coldwell Banker India on <?= esc($s['label'], 'attr') ?>"><?= cb_icon(SiteSchema::socialIcon($s['href'])) ?></a></li>
          <?php endforeach ?>
        </ul>
      </div>
      <?php endif ?>
    </div>
    <div class="cb-footer__rule"></div>
    <div class="cb-footer__bottom">
      <p><?= esc($copyright) ?></p>
      <?php if ($legal !== []): ?>
      <nav class="cb-footer__legal" aria-label="Legal">
        <?php foreach ($legal as $l): ?>
        <a href="<?= esc($l['href'], 'attr') ?>"<?= $tgt($l) ?>><?= esc($l['label']) ?></a>
        <?php endforeach ?>
      </nav>
      <?php endif ?>
    </div>
  </div>
</footer>
