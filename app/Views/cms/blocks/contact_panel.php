<?php
/**
 * Block "contact_panel" (Figma Contact 85:58 "Get in Touch"): heading + contact cards left,
 * message form right. Posts to Site\Enquiry::contactEnquiry with source=cms (contact_enquiry
 * table incl. interested_in, email to info@), which returns to #contact-form with a message.
 *
 * @var array<string, string>      $s      block settings
 * @var array<string, list<array>> $items  channel
 * @var string                     $sfx    id suffix for repeated blocks
 */
use App\Libraries\Cms\BlockTypes;
use App\Libraries\Cms\CmsUrl;

helper('cb');
$channels = $items['channel'] ?? [];
$id       = 'cb-contact-title' . $sfx;
$msg      = session()->getFlashdata('enquiry_msg');
$ok       = session()->getFlashdata('enquiry_ok');
$icons    = BlockTypes::ICONS + ['mail' => 'Mail', 'phone' => 'Phone'];
$chips    = ['Franchise', 'Join as Agent', 'Buying', 'Selling', 'Renting'];
$f        = static fn (string $n): string => 'cb-ct-' . $n . $sfx;
?>
<section class="cb-contact" aria-labelledby="<?= esc($id, 'attr') ?>">
  <div class="cb-contact__in">
    <div class="cb-contact__info cb-reveal">
      <h2 class="cb-h2" id="<?= esc($id, 'attr') ?>"><?= esc(trim($s['title'] ?? '')) ?></h2>
      <?php if (trim($s['lead'] ?? '') !== ''): ?><p class="cb-p"><?= esc(trim($s['lead'])) ?></p><?php endif ?>
      <?php if ($channels !== []): ?>
      <ul class="cb-channels">
        <?php foreach ($channels as $c):
            $ic   = isset($icons[$c['kicker'] ?? '']) ? $c['kicker'] : 'pin';
            $href = CmsUrl::href($c['cta_url'] ?? '');
            $tag  = $href !== '' ? 'a' : 'div';
        ?>
        <li>
          <<?= $tag ?> class="cb-channel"<?= $href !== '' ? ' href="' . esc($href, 'attr') . '"' . (CmsUrl::isExternal($href) ? ' target="_blank" rel="noopener"' : '') : '' ?>>
            <span class="cb-channel__icon" aria-hidden="true"><?= cb_icon($ic) ?></span>
            <span><b><?= esc((string) $c['title']) ?></b><span class="cb-pre"><?= esc(trim((string) $c['body'])) ?></span></span>
          </<?= $tag ?>>
        </li>
        <?php endforeach ?>
      </ul>
      <?php endif ?>
    </div>

    <form class="cb-cform cb-reveal" id="contact-form" action="<?= esc(site_url('contact-enquiry'), 'attr') ?>" method="post">
      <?= csrf_field() ?>
      <input type="hidden" name="source" value="cms">
      <h3><?= esc(trim($s['form_title'] ?? '') ?: 'Send Us a Message') ?></h3>
      <?php if (trim($s['form_lead'] ?? '') !== ''): ?><p class="cb-cform__lead"><?= esc(trim($s['form_lead'])) ?></p><?php endif ?>
      <?php if ($msg): ?><p class="cb-flash <?= $ok ? 'cb-flash--ok' : 'cb-flash--err' ?>" role="status"><?= esc($msg) ?></p><?php endif ?>
      <div class="cb-cfield cb-cfield--full">
        <label for="<?= esc($f('name'), 'attr') ?>">Full name <span aria-hidden="true">*</span></label>
        <input id="<?= esc($f('name'), 'attr') ?>" name="name" type="text" required maxlength="100" autocomplete="name" placeholder="Your full name">
      </div>
      <div class="cb-cfield">
        <label for="<?= esc($f('email'), 'attr') ?>">Email <span aria-hidden="true">*</span></label>
        <input id="<?= esc($f('email'), 'attr') ?>" name="email" type="email" required maxlength="150" autocomplete="email" placeholder="you@company.com">
      </div>
      <div class="cb-cfield">
        <label for="<?= esc($f('phone'), 'attr') ?>">Phone no. <span aria-hidden="true">*</span></label>
        <input id="<?= esc($f('phone'), 'attr') ?>" name="phone" type="tel" required maxlength="20" pattern="[0-9+()\s-]{6,20}" autocomplete="tel" placeholder="+91">
      </div>
      <fieldset class="cb-cfield cb-cfield--full cb-chips">
        <legend>I am interested in</legend>
        <?php foreach ($chips as $i => $chip): ?>
        <label class="cb-chipbox"><input type="checkbox" name="interest[]" value="<?= esc($chip, 'attr') ?>"><span><?= cb_icon('check') ?><?= esc($chip) ?></span></label>
        <?php endforeach ?>
      </fieldset>
      <div class="cb-cfield cb-cfield--full">
        <label for="<?= esc($f('message'), 'attr') ?>">Message <span aria-hidden="true">*</span></label>
        <textarea id="<?= esc($f('message'), 'attr') ?>" name="message" required maxlength="5000" rows="4" placeholder="How can we help?"></textarea>
      </div>
      <button type="submit" class="cb-pill cb-pill--gold cb-cfield--full"><span><?= esc(trim($s['button'] ?? '') ?: 'Submit Enquiry') ?></span><span class="cb-pill__icon"><?= cb_icon('arrow') ?></span></button>
      <p class="cb-cform__note cb-cfield--full">*Fields are mandatory. By submitting, you agree to our <a href="<?= esc(site_url('terms-and-conditions'), 'attr') ?>">Terms &amp; Conditions</a> and <a href="<?= esc(site_url('privacy-policy'), 'attr') ?>">Privacy Policy</a>.</p>
    </form>
  </div>
</section>
