<?php
/**
 * Block "job_apply_form" (Figma Careers 90:58 "Job Application Form"): navy panel, copy left,
 * multipart form right. Posts to Site\Enquiry::jobApplications with source=cms (job_applications
 * + résumé in writable/uploads/resume + email to HR); validation, résumé type/size checks and
 * the per-IP throttle are unchanged. Returns to #apply with a message.
 *
 * @var array<string, string> $s    block settings
 * @var string                $sfx  id suffix for repeated blocks
 */
use App\Libraries\Cms\Jobs;

helper('cb');
$groups = Jobs::open();
$id     = 'cb-apply-title' . $sfx;
$msg    = session()->getFlashdata('job_msg');
$ok     = session()->getFlashdata('job_ok');
$cv     = trim($s['cv_email'] ?? '');
$cvOk   = $cv !== '' && filter_var($cv, FILTER_VALIDATE_EMAIL);
$f      = static fn (string $n): string => 'cb-ja-' . $n . $sfx;
?>
<section class="cb-callback cb-apply" id="apply" aria-labelledby="<?= esc($id, 'attr') ?>">
  <div class="cb-callback__panel">
    <div class="cb-callback__copy cb-reveal">
      <?php if (trim($s['eyebrow'] ?? '') !== ''): ?><p class="cb-eyebrow cb-eyebrow--gold"><?= esc(trim($s['eyebrow'])) ?></p><?php endif ?>
      <h2 class="cb-h2" id="<?= esc($id, 'attr') ?>"><?= esc(trim($s['title'] ?? '')) ?></h2>
      <?php if (trim($s['lead'] ?? '') !== ''): ?><p class="cb-p"><?= esc(trim($s['lead'])) ?></p><?php endif ?>
      <?php if ($cvOk): ?>
      <a class="cb-callus" href="mailto:<?= esc($cv, 'attr') ?>">
        <span class="cb-callus__icon" aria-hidden="true"><?= cb_icon('mail') ?></span>
        <span><small>No matching role?</small><b class="cb-callus__mail">Send your CV to <?= esc($cv) ?></b></span>
      </a>
      <?php endif ?>
    </div>

    <?php if ($groups === []): ?>
    <p class="cb-p cb-apply__closed">Applications open as soon as a new position is posted.</p>
    <?php else: ?>
    <form class="cb-callback__form cb-apply__form cb-reveal" action="<?= esc(site_url('job-applications'), 'attr') ?>" method="post" enctype="multipart/form-data">
      <?= csrf_field() ?>
      <input type="hidden" name="source" value="cms">
      <?php if ($msg): ?><p class="cb-flash <?= $ok ? 'cb-flash--ok' : 'cb-flash--err' ?>" role="status"><?= esc($msg) ?></p><?php endif ?>
      <div class="cb-field cb-field--full cb-select"><label class="cb-alabel" for="<?= esc($f('job'), 'attr') ?>">Position applied for *</label>
        <select id="<?= esc($f('job'), 'attr') ?>" name="job_id" required data-apply-select>
          <option value="">Select a current opening</option>
          <?php foreach ($groups as $label => $jobs): ?>
          <optgroup label="<?= esc($label, 'attr') ?>"><?php foreach ($jobs as $j): ?><option value="<?= (int) $j['id'] ?>"><?= esc($j['title']) ?><?= $j['location'] !== '' ? ' — ' . esc($j['location']) : '' ?></option><?php endforeach ?></optgroup>
          <?php endforeach ?>
        </select><?= cb_icon('chev') ?></div>
      <div class="cb-field"><label class="cb-alabel" for="<?= esc($f('fn'), 'attr') ?>">First name *</label>
        <input id="<?= esc($f('fn'), 'attr') ?>" name="applicant_fname" type="text" required maxlength="60" autocomplete="given-name" placeholder="First name"></div>
      <div class="cb-field"><label class="cb-alabel" for="<?= esc($f('ln'), 'attr') ?>">Last name *</label>
        <input id="<?= esc($f('ln'), 'attr') ?>" name="applicant_lname" type="text" required maxlength="60" autocomplete="family-name" placeholder="Last name"></div>
      <fieldset class="cb-field cb-field--full cb-gender"><legend class="cb-alabel">Gender *</legend>
        <label class="cb-radio"><input type="radio" name="gender" value="male" required><span>Male</span></label>
        <label class="cb-radio"><input type="radio" name="gender" value="female"><span>Female</span></label>
      </fieldset>
      <div class="cb-field"><label class="cb-alabel" for="<?= esc($f('mob'), 'attr') ?>">Mobile *</label>
        <input id="<?= esc($f('mob'), 'attr') ?>" name="applicant_mobile" type="tel" required maxlength="20" pattern="[0-9+()\s-]{6,20}" autocomplete="tel" placeholder="+91"></div>
      <div class="cb-field"><label class="cb-alabel" for="<?= esc($f('ph'), 'attr') ?>">Phone</label>
        <input id="<?= esc($f('ph'), 'attr') ?>" name="applicant_phone" type="tel" maxlength="20" placeholder="Landline (optional)"></div>
      <div class="cb-field cb-field--full"><label class="cb-alabel" for="<?= esc($f('em'), 'attr') ?>">Email *</label>
        <input id="<?= esc($f('em'), 'attr') ?>" name="applicant_email" type="email" required maxlength="150" autocomplete="email" placeholder="you@example.com"></div>
      <div class="cb-field cb-field--full"><label class="cb-alabel" for="<?= esc($f('ad'), 'attr') ?>">Address *</label>
        <textarea id="<?= esc($f('ad'), 'attr') ?>" name="applicant_address" required maxlength="500" rows="3" placeholder="Your current address"></textarea></div>
      <div class="cb-field cb-field--full"><span class="cb-alabel" id="<?= esc($f('cvl'), 'attr') ?>">Upload résumé *</span>
        <label class="cb-drop" data-drop>
          <input type="file" name="resume_file" required accept=".pdf,.doc,.docx,.rtf,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/rtf" aria-labelledby="<?= esc($f('cvl'), 'attr') ?>" aria-describedby="<?= esc($f('cvh'), 'attr') ?>">
          <span class="cb-drop__icon" aria-hidden="true"><?= cb_icon('download') ?></span>
          <span><b data-drop-name>Drag &amp; drop or browse</b><small id="<?= esc($f('cvh'), 'attr') ?>">PDF, DOC, DOCX or RTF · max 2 MB</small></span>
        </label></div>
      <button type="submit" class="cb-pill cb-pill--gold cb-field--full"><span><?= esc(trim($s['button'] ?? '') ?: 'Submit Application') ?></span><span class="cb-pill__icon"><?= cb_icon('arrow') ?></span></button>
      <p class="cb-callback__note cb-field--full">*Fields are mandatory. By applying, you agree to our <a href="<?= esc(site_url('privacy-policy'), 'attr') ?>">Privacy Policy</a>.</p>
    </form>
    <?php endif ?>
  </div>
</section>
