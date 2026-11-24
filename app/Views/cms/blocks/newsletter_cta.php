<?php
/**
 * Block "newsletter_cta" (Figma 41:41 "Section/Newsletter CTA"): photo band with the email
 * signup; page.php places it directly above the footer when it is the last block.
 * Posts to Site\Enquiry::newsletterSubscription: source=home returns to the home page,
 * source=cms returns to this page (same-origin referer only).
 *
 * @var array<string, string> $s       block settings
 * @var bool                  $is_home
 * @var string                $sfx
 */
helper('cb');
$msg   = session()->getFlashdata('newsletter_msg');
$msgOk = session()->getFlashdata('newsletter_ok');
$id    = 'cb-cta-title' . $sfx;
$input = 'cb-subscribe-email' . $sfx;
?>
<section class="cb-cta"<?= $sfx === '' ? ' id="newsletter"' : '' ?> aria-labelledby="<?= esc($id, 'attr') ?>">
  <?= cb_picture($s['image'] ?? '', '', ['width' => '1584', 'height' => '672']) ?>
  <h2 class="cb-display-l cb-pre cb-reveal" id="<?= esc($id, 'attr') ?>"><?= esc(trim($s['title'] ?? '')) ?></h2>
  <?php if (trim($s['lead'] ?? '') !== ''): ?><p class="cb-p cb-reveal"><?= esc(trim($s['lead'])) ?></p><?php endif ?>
  <?php if ($msg): ?><p class="cb-flash <?= $msgOk ? 'cb-flash--ok' : 'cb-flash--err' ?>" role="status"><?= esc($msg) ?></p><?php endif ?>
  <form class="cb-subscribe cb-glass cb-reveal" action="<?= esc(site_url('newsletter-subscription'), 'attr') ?>" method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="source" value="<?= $is_home ? 'home' : 'cms' ?>">
    <label for="<?= esc($input, 'attr') ?>" class="cb-sr">Email address</label>
    <input id="<?= esc($input, 'attr') ?>" type="email" name="email" required autocomplete="email" placeholder="Your email address">
    <button type="submit" aria-label="Subscribe"><?= cb_icon('arrow') ?></button>
  </form>
</section>
