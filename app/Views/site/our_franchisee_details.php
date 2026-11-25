<?php
helper('cb');
$asset       = static fn (string $f): string => base_url($f) . '?v=' . @filemtime(FCPATH . $f);
$officeTitle = trim(html_entity_decode(strip_tags((string) ($rz1['title'] ?? 'Coldwell Banker Office')), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
$location    = trim(html_entity_decode(strip_tags((string) ($rz1['location'] ?? '')), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
$address     = trim(html_entity_decode(strip_tags((string) ($rz1['address'] ?? '')), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
$phone       = trim((string) preg_replace('/\s+/', ' ', strip_tags((string) ($rz1['mobile_no'] ?? ''))));
$email       = trim((string) ($rz1['email'] ?? ''));
$desc        = (string) ($rz1['project_description'] ?? $rz1['description'] ?? '');
$officeLogo  = trim((string) ($rz1['logo'] ?? ''));
$heroPhoto   = ($officeLogo !== '' && is_file(FCPATH . 'uploads/franchises/' . $officeLogo))
    ? base_url('uploads/franchises/' . rawurlencode($officeLogo))
    : '';
$flashOk     = session()->getFlashdata('success') ?? ($success ?? null);
$flashErr    = session()->getFlashdata('error') ?? ($error ?? null);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Coldwell Banker <?= esc($officeTitle) ?> | Coldwell Banker India</title>
  <meta name="description" content="<?= esc($page_info->meta_description ?? ('Coldwell Banker ' . $officeTitle . ' — ' . $location), 'attr') ?>">
  <meta name="theme-color" content="#0F1F3D">
  <link rel="shortcut icon" href="<?= esc(base_url('assets/images/favicon.ico'), 'attr') ?>">
  <link rel="stylesheet" href="<?= esc($asset('assets/css/fonts.css'), 'attr') ?>">
  <link rel="stylesheet" href="<?= esc($asset('assets/css/cb-site.css'), 'attr') ?>">
  <?= $this->include('partials/tracking') ?>
  <?= $this->include('partials/ga_code_header') ?>
</head>
<body class="cb-page">
<a class="cb-skip" href="#main">Skip to content</a>
<main>
  <div class="cb-hero">
    <div class="cb-hero__card cb-hero__card--page">
      <?= view('cms/partials/nav', ['page_active' => 'our franchisee', 'current_url' => current_url()], ['saveData' => false]) ?>
      <div class="cb-hero__slides" aria-hidden="true">
        <div class="cb-slide is-active">
          <div class="cb-slide__img">
            <?= cb_picture('assets/images/home-v2/towers.jpg', '', ['loading' => 'eager', 'fetchpriority' => 'high', 'width' => '1536', 'height' => '864']) ?>
          </div>
          <div class="cb-slide__shade"></div>
        </div>
      </div>
      <div class="cb-hero__body" id="main" tabindex="-1">
        <div class="cb-phero__copy">
          <p class="cb-eyebrow"><span>Home  ·  Our Network  ·  Office</span></p>
          <h1 class="cb-display-l">Coldwell Banker <?= esc($officeTitle) ?></h1>
          <?php if ($location !== ''): ?><p class="cb-lead"><?= esc($location) ?></p><?php endif ?>
          <div class="cb-hero__ctas">
            <a href="#office-enquiry" class="cb-pill cb-pill--gold">
              <span>Enquire With This Office</span><span class="cb-pill__icon"><?= cb_icon('arrow') ?></span>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <section class="cb-fdetail">
    <div class="cb-wrap">
      <div class="cb-fdetail__grid">
        <div class="cb-fdetail__about">
          <p class="cb-eyebrow"><span>OFFICE OVERVIEW</span></p>
          <h2 class="cb-h2">About Coldwell Banker <?= esc($officeTitle) ?></h2>
          <?php if (trim(strip_tags($desc)) !== ''): ?>
            <div class="cb-fdetail__body"><?= $desc ?></div>
          <?php else: ?>
            <p class="cb-p">Connect with our dedicated real estate specialists at Coldwell Banker <?= esc($officeTitle) ?> for residential and commercial advisory.</p>
          <?php endif ?>
        </div>
        <aside class="cb-fdetail__card" aria-label="Office contact details">
          <?php if ($heroPhoto !== ''): ?>
          <div class="cb-fdetail__logo">
            <img src="<?= esc($heroPhoto, 'attr') ?>" alt="Coldwell Banker <?= esc($officeTitle, 'attr') ?> logo" loading="lazy" decoding="async">
          </div>
          <?php endif ?>
          <h3>Contact Details</h3>
          <?php if ($address !== ''): ?>
          <p class="cb-fdetail__row"><span><?= cb_icon('pin') ?></span><span><?= esc($address) ?></span></p>
          <?php endif ?>
          <?php if ($phone !== ''): ?>
          <p class="cb-fdetail__row"><span><?= cb_icon('phone') ?></span><a href="tel:<?= esc(preg_replace('/[^0-9+]/', '', $phone), 'attr') ?>"><?= esc($phone) ?></a></p>
          <?php endif ?>
          <?php if ($email !== ''): ?>
          <p class="cb-fdetail__row"><span><?= cb_icon('mail') ?></span><a href="mailto:<?= esc($email, 'attr') ?>"><?= esc($email) ?></a></p>
          <?php endif ?>
          <a href="<?= esc(site_url('our-franchise'), 'attr') ?>" class="cb-pill cb-pill--light cb-pill--on-section cb-pill--sm">
            <span>All Network Offices</span><span class="cb-pill__icon"><?= cb_icon('arrow') ?></span>
          </a>
        </aside>
      </div>
    </div>
  </section>

  <?php if (! empty($rz2)): ?>
  <section class="cb-fdetail-team" aria-labelledby="cb-owners-heading">
    <div class="cb-wrap">
      <p class="cb-eyebrow"><span>LEADERSHIP</span></p>
      <h2 class="cb-h2" id="cb-owners-heading">Franchise Owners</h2>
      <ul class="cb-fdetail-people">
        <?php foreach ($rz2 as $owner): $oImg = trim((string) ($owner['image'] ?? $owner['image_name'] ?? '')); ?>
        <li class="cb-fperson">
          <div class="cb-fperson__img">
            <?php if ($oImg !== '' && is_file(FCPATH . 'uploads/franchise_owners/' . $oImg)): ?>
              <img src="<?= esc(base_url('uploads/franchise_owners/' . rawurlencode($oImg)), 'attr') ?>" alt="<?= esc((string) ($owner['name'] ?? ''), 'attr') ?>" loading="lazy" decoding="async" width="280" height="280">
            <?php else: ?>
              <span class="cb-fperson__placeholder" aria-hidden="true"><?= cb_icon('people') ?></span>
            <?php endif ?>
          </div>
          <h3><?= esc((string) ($owner['name'] ?? '')) ?></h3>
          <?php if (! empty($owner['designation'])): ?><p class="cb-fperson__role"><?= esc((string) $owner['designation']) ?></p><?php endif ?>
        </li>
        <?php endforeach ?>
      </ul>
    </div>
  </section>
  <?php endif ?>

  <?php if (! empty($rz4)): ?>
  <section class="cb-fdetail-team cb-fdetail-team--alt" aria-labelledby="cb-specialists-heading">
    <div class="cb-wrap">
      <p class="cb-eyebrow"><span>ADVISORS</span></p>
      <h2 class="cb-h2" id="cb-specialists-heading">Real Estate Specialists</h2>
      <ul class="cb-fdetail-people">
        <?php foreach ($rz4 as $sp): $sImg = trim((string) ($sp['image'] ?? $sp['image_name'] ?? '')); $sRole = trim((string) ($sp['title'] ?? $sp['designation'] ?? '')); ?>
        <li class="cb-fperson">
          <div class="cb-fperson__img">
            <?php if ($sImg !== '' && is_file(FCPATH . 'uploads/specialist/' . $sImg)): ?>
              <img src="<?= esc(base_url('uploads/specialist/' . rawurlencode($sImg)), 'attr') ?>" alt="<?= esc((string) ($sp['name'] ?? ''), 'attr') ?>" loading="lazy" decoding="async" width="280" height="280">
            <?php else: ?>
              <span class="cb-fperson__placeholder" aria-hidden="true"><?= cb_icon('people') ?></span>
            <?php endif ?>
          </div>
          <h3><?= esc((string) ($sp['name'] ?? '')) ?></h3>
          <?php if ($sRole !== ''): ?><p class="cb-fperson__role"><?= esc($sRole) ?></p><?php endif ?>
        </li>
        <?php endforeach ?>
      </ul>
    </div>
  </section>
  <?php endif ?>

  <?php if (! empty($testimonials)): ?>
  <section class="cb-fdetail-team" aria-labelledby="cb-ftest-heading">
    <div class="cb-wrap">
      <p class="cb-eyebrow"><span>CLIENT STORIES</span></p>
      <h2 class="cb-h2" id="cb-ftest-heading">Testimonials</h2>
      <div class="cb-ftest-grid">
        <?php foreach ($testimonials as $t): ?>
        <blockquote class="cb-ftest-card">
          <p><?= esc(trim(strip_tags((string) ($t['description'] ?? $t['testimonial'] ?? '')))) ?></p>
          <?php if (! empty($t['name'])): ?><footer>— <?= esc((string) $t['name']) ?></footer><?php endif ?>
        </blockquote>
        <?php endforeach ?>
      </div>
    </div>
  </section>
  <?php endif ?>

  <section class="cb-qpay" id="office-enquiry" aria-labelledby="cb-fenq-heading">
    <div class="cb-qpay__panel">
      <div class="cb-qpay__head">
        <p class="cb-eyebrow cb-eyebrow--center">GET IN TOUCH</p>
        <h2 class="cb-h2" id="cb-fenq-heading">Enquire With <?= esc($officeTitle) ?></h2>
        <p class="cb-p">Share your requirements and our local advisor will reach out to you.</p>
      </div>
      <?php if ($flashOk): ?><p class="cb-flash cb-flash--ok" role="status"><?= esc((string) $flashOk) ?></p><?php endif ?>
      <?php if ($flashErr): ?><p class="cb-flash cb-flash--err" role="status"><?= esc((string) $flashErr) ?></p><?php endif ?>
      <form class="cb-qpay__form" method="post" action="<?= esc(site_url('franchisee-details-enquiry/' . (int) $id), 'attr') ?>">
        <?= csrf_field() ?>
        <div class="cb-qpay__field">
          <label for="fe-name">Full Name *</label>
          <input id="fe-name" name="name" type="text" required autocomplete="name" placeholder="Your full name">
        </div>
        <div class="cb-qpay__field">
          <label for="fe-email">Email Address *</label>
          <input id="fe-email" name="email" type="email" required autocomplete="email" placeholder="you@example.com">
        </div>
        <div class="cb-qpay__field">
          <label for="fe-phone">Phone Number *</label>
          <input id="fe-phone" name="mobile" type="tel" required autocomplete="tel" placeholder="+91 98XXXXXXXX">
        </div>
        <div class="cb-qpay__field">
          <label for="fe-msg">Message</label>
          <textarea id="fe-msg" name="message" rows="3" placeholder="Tell us how we can help"></textarea>
        </div>
        <div class="cb-qpay__submit">
          <button type="submit" class="cb-pill cb-pill--gold">
            <span>Send Enquiry</span><span class="cb-pill__icon"><?= cb_icon('arrow') ?></span>
          </button>
        </div>
      </form>
    </div>
  </section>
</main>
<?= view('cms/partials/footer', [], ['saveData' => false]) ?>
<script src="<?= esc($asset('assets/js/cb-site.js'), 'attr') ?>" defer></script>
</body>
</html>
