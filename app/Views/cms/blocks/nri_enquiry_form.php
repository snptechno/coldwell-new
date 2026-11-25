<?php
/**
 * Block "nri_enquiry_form" (Figma NRI Services – v2 134:600, "Talk to Our NRI Desk"): navy panel,
 * copy + phone on the left, NRI enquiry form on the right. Posts to Site\Enquiry::nriEnquiry
 * (nri_enquiry table + email to the NRI desk) with source=cms, which redirects back to this
 * page's #nri-enquiry anchor with a message. Choices come from App\Libraries\Cms\NriDesk, the
 * same allow-list the controller checks.
 *
 * @var array<string, string> $s    block settings
 * @var string                $sfx  id suffix for repeated blocks
 */
use App\Libraries\Cms\Cms;
use App\Libraries\Cms\NriDesk;

helper('cb');
$id    = 'cb-nri-title' . $sfx;
$phone = trim($s['phone'] ?? '') !== '' ? trim($s['phone']) : Cms::setting('footer_phone');
$tel   = preg_replace('/[^0-9+]/', '', (string) $phone);
$msg   = session()->getFlashdata('nri_msg');
$ok    = session()->getFlashdata('nri_ok');
?>
<section class="cb-callback cb-callback--nri" id="nri-enquiry" aria-labelledby="<?= esc($id, 'attr') ?>">
  <div class="cb-callback__panel">
    <div class="cb-callback__copy cb-reveal">
      <?php if (trim($s['eyebrow'] ?? '') !== ''): ?><p class="cb-eyebrow cb-eyebrow--gold"><?= esc(trim($s['eyebrow'])) ?></p><?php endif ?>
      <h2 class="cb-h2" id="<?= esc($id, 'attr') ?>"><?= esc(trim($s['title'] ?? '')) ?></h2>
      <?php if (trim($s['lead'] ?? '') !== ''): ?><p class="cb-p"><?= esc(trim($s['lead'])) ?></p><?php endif ?>
      <?php if ($tel !== ''): ?>
      <a class="cb-callus" href="tel:<?= esc($tel, 'attr') ?>">
        <span class="cb-callus__icon" aria-hidden="true"><?= cb_icon('phone') ?></span>
        <span><small>Call us</small><b><?= esc((string) $phone) ?></b></span>
      </a>
      <?php endif ?>
    </div>
    <form class="cb-callback__form cb-reveal" action="<?= esc(site_url('nri-enquiry'), 'attr') ?>" method="post">
      <?= csrf_field() ?>
      <input type="hidden" name="source" value="cms">
      <?php /* honeypot: real visitors never see or fill this */ ?>
      <div class="cb-hp" aria-hidden="true"><label>Leave this empty<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
      <?php if ($msg): ?><p class="cb-flash <?= $ok ? 'cb-flash--ok' : 'cb-flash--err' ?>" role="status"><?= esc((string) $msg) ?></p><?php endif ?>
      <label class="cb-field cb-field--full"><span class="cb-sr">Name (required)</span>
        <input name="name" type="text" required maxlength="100" autocomplete="name" placeholder="Name*"></label>
      <label class="cb-field"><span class="cb-sr">Email (required)</span>
        <input name="email" type="email" required maxlength="150" autocomplete="email" placeholder="Email*"></label>
      <label class="cb-field"><span class="cb-sr">Phone with country code (required)</span>
        <input name="phone" type="tel" required maxlength="20" pattern="[0-9+()\s-]{6,20}" autocomplete="tel" placeholder="Phone*"></label>
      <label class="cb-field"><span class="cb-sr">Country of residence (required)</span>
        <input name="nri_country" type="text" required maxlength="60" autocomplete="country-name" placeholder="Country of residence*"></label>
      <label class="cb-field cb-select"><span class="cb-sr">Interested in</span>
        <select name="interest">
          <option value="">Interested in</option>
          <?php foreach (NriDesk::INTERESTS as $o): ?><option value="<?= esc($o, 'attr') ?>"><?= esc($o) ?></option><?php endforeach ?>
        </select><?= cb_icon('chev') ?></label>
      <label class="cb-field cb-field--full cb-select"><span class="cb-sr">Preferred city in India</span>
        <select name="city">
          <option value="">Preferred city in India</option>
          <?php foreach (NriDesk::cities() as $c): ?><option value="<?= esc($c, 'attr') ?>"><?= esc($c) ?></option><?php endforeach ?>
        </select><?= cb_icon('chev') ?></label>
      <button type="submit" class="cb-pill cb-pill--gold cb-field--full"><span><?= esc(trim($s['button'] ?? '') ?: 'Send Request') ?></span><span class="cb-pill__icon"><?= cb_icon('arrow') ?></span></button>
      <p class="cb-callback__note cb-field--full">*Mandatory fields. By submitting, you agree to our <a href="<?= esc(site_url('terms-and-conditions'), 'attr') ?>">Terms</a> and <a href="<?= esc(site_url('privacy-policy'), 'attr') ?>">Privacy Policy</a>.</p>
    </form>
  </div>
</section>
