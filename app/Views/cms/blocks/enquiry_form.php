<?php
/**
 * Block "enquiry_form" (Figma Franchise 77:58 "Request a Call Back"): navy panel, copy + phone
 * on the left, franchise enquiry form on the right. Posts to Site\Enquiry::franchiseeEnquiry
 * (franchisee_enquiry table + email to the franchise team) with source=cms, which redirects
 * back to this page's #callback anchor with a message.
 *
 * @var array<string, string> $s    block settings
 * @var string                $sfx  id suffix for repeated blocks
 */
use App\Libraries\Cms\Cms;
use App\Libraries\Cms\Offices;

helper('cb');
$id     = 'cb-callback-title' . $sfx;
$phone  = trim($s['phone'] ?? '') !== '' ? trim($s['phone']) : Cms::setting('footer_phone');
$tel    = preg_replace('/[^0-9+]/', '', $phone);
$msg    = session()->getFlashdata('enquiry_msg');
$ok     = session()->getFlashdata('enquiry_ok');
$cities = array_keys(Offices::cities(Offices::all()));
sort($cities);
$f      = static fn (string $n): string => 'cb-cb-' . $n . $sfx;
?>
<section class="cb-callback" id="callback" aria-labelledby="<?= esc($id, 'attr') ?>">
  <div class="cb-callback__panel">
    <div class="cb-callback__copy cb-reveal">
      <?php if (trim($s['eyebrow'] ?? '') !== ''): ?><p class="cb-eyebrow cb-eyebrow--gold"><?= esc(trim($s['eyebrow'])) ?></p><?php endif ?>
      <h2 class="cb-h2" id="<?= esc($id, 'attr') ?>"><?= esc(trim($s['title'] ?? '')) ?></h2>
      <?php if (trim($s['lead'] ?? '') !== ''): ?><p class="cb-p"><?= esc(trim($s['lead'])) ?></p><?php endif ?>
      <?php if ($tel !== ''): ?>
      <a class="cb-callus" href="tel:<?= esc($tel, 'attr') ?>">
        <span class="cb-callus__icon" aria-hidden="true"><?= cb_icon('phone') ?></span>
        <span><small>Call us</small><b><?= esc($phone) ?></b></span>
      </a>
      <?php endif ?>
    </div>
    <form class="cb-callback__form cb-reveal" action="<?= esc(site_url('franchisee-enquiry'), 'attr') ?>" method="post">
      <?= csrf_field() ?>
      <input type="hidden" name="source" value="cms">
      <?php if ($msg): ?><p class="cb-flash <?= $ok ? 'cb-flash--ok' : 'cb-flash--err' ?>" role="status"><?= esc($msg) ?></p><?php endif ?>
      <label class="cb-field cb-field--full"><span class="cb-sr">Name (required)</span>
        <input id="<?= esc($f('name'), 'attr') ?>" name="name" type="text" required maxlength="100" autocomplete="name" placeholder="Name*"></label>
      <label class="cb-field"><span class="cb-sr">Email (required)</span>
        <input name="email" type="email" required maxlength="150" autocomplete="email" placeholder="Email*"></label>
      <label class="cb-field"><span class="cb-sr">Phone (required)</span>
        <input name="phone" type="tel" required maxlength="20" pattern="[0-9+()\s-]{6,20}" autocomplete="tel" placeholder="Phone*"></label>
      <label class="cb-field"><span class="cb-sr">Profession</span>
        <input name="profession" type="text" maxlength="120" autocomplete="organization-title" placeholder="Profession"></label>
      <label class="cb-field"><span class="cb-sr">Industry</span>
        <input name="industry" type="text" maxlength="120" placeholder="Industry"></label>
      <label class="cb-field cb-field--full cb-select"><span class="cb-sr">Preferred location</span>
        <select name="location">
          <option value="">Preferred location</option>
          <?php foreach ($cities as $c): ?><option value="<?= esc($c, 'attr') ?>"><?= esc($c) ?></option><?php endforeach ?>
          <option value="Other">Other</option>
        </select><?= cb_icon('chev') ?></label>
      <button type="submit" class="cb-pill cb-pill--gold cb-field--full"><span><?= esc(trim($s['button'] ?? '') ?: 'Send Request') ?></span><span class="cb-pill__icon"><?= cb_icon('arrow') ?></span></button>
      <p class="cb-callback__note cb-field--full">*Mandatory fields. By submitting, you agree to our <a href="<?= esc(site_url('terms-and-conditions'), 'attr') ?>">Terms</a> and <a href="<?= esc(site_url('privacy-policy'), 'attr') ?>">Privacy Policy</a>.</p>
    </form>
  </div>
</section>
