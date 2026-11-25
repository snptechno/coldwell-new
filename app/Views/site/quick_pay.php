<?php
helper('cb');
$asset   = static fn (string $f): string => base_url($f) . '?v=' . @filemtime(FCPATH . $f);
$rzpKey  = (string) env('razorpay.keyId', '');
$logoUrl = base_url('assets/images/home-v2/logo.png');
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Quick Pay | Coldwell Banker India</title>
  <meta name="description" content="Make a quick, secure online payment to Coldwell Banker India via Razorpay.">
  <meta name="theme-color" content="#0F1F3D">
  <link rel="canonical" href="<?= esc(site_url('quick-pay'), 'attr') ?>">
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
    <div class="cb-hero__card cb-hero__card--page cb-hero__card--plain">
      <?= view('cms/partials/nav', ['page_active' => '', 'current_url' => current_url()], ['saveData' => false]) ?>
      <div class="cb-hero__body" id="main" tabindex="-1">
        <div class="cb-phero__copy">
          <p class="cb-eyebrow"><span>Home  ·  Quick Pay</span></p>
          <h1 class="cb-display-l">Quick Pay</h1>
          <p class="cb-lead">Make a quick, secure online payment to Coldwell Banker India via Razorpay.</p>
        </div>
      </div>
    </div>
  </div>

  <section class="cb-qpay" aria-labelledby="cb-qpay-heading">
    <div class="cb-qpay__panel">
      <div class="cb-qpay__head">
        <p class="cb-eyebrow cb-eyebrow--center">SECURE CHECKOUT</p>
        <h2 class="cb-h2" id="cb-qpay-heading">Online Payment Details</h2>
        <p class="cb-p">Enter your name, email address, and payment amount in INR. All fields marked * are mandatory.</p>
      </div>

      <p class="cb-flash cb-qpay__msg" id="cb-qpay-msg" role="status" hidden></p>

      <form id="quick_pay" class="cb-qpay__form" method="post" action="#" novalidate>
        <?= csrf_field() ?>
        <div class="cb-qpay__field">
          <label for="name">Full Name *</label>
          <input name="name" id="name" type="text" required autocomplete="name" placeholder="Enter your full name">
        </div>
        <div class="cb-qpay__field">
          <label for="email">Email Address *</label>
          <input name="email" id="email" type="email" required autocomplete="email" placeholder="you@example.com">
        </div>
        <div class="cb-qpay__field">
          <label for="amount">Amount (INR) *</label>
          <input name="amount" id="amount" type="number" min="1" step="0.01" required inputmode="decimal" placeholder="e.g. 5000">
        </div>
        <div class="cb-qpay__submit">
          <button type="submit" class="cb-pill cb-pill--gold">
            <span>Proceed to Pay</span><span class="cb-pill__icon"><?= cb_icon('arrow') ?></span>
          </button>
        </div>
        <p class="cb-qpay__note">Secured by Razorpay · 256-bit SSL encrypted checkout</p>
      </form>
    </div>
  </section>
</main>
<?= view('cms/partials/footer', [], ['saveData' => false]) ?>
<script src="https://checkout.razorpay.com/v1/checkout.js" defer></script>
<script src="<?= esc($asset('assets/js/cb-site.js'), 'attr') ?>" defer></script>
<script>
(function () {
  var form = document.getElementById('quick_pay');
  var msgEl = document.getElementById('cb-qpay-msg');
  if (!form) return;

  function showMsg(text, ok) {
    if (!msgEl) return;
    msgEl.textContent = text;
    msgEl.className = 'cb-flash cb-qpay__msg ' + (ok ? 'cb-flash--ok' : 'cb-flash--err');
    msgEl.hidden = false;
  }

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    var name = (document.getElementById('name').value || '').trim();
    var email = (document.getElementById('email').value || '').trim();
    var amt = parseFloat(document.getElementById('amount').value || '0');

    if (!name) { showMsg('Please enter your full name.', false); return; }
    if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { showMsg('Please enter a valid email address.', false); return; }
    if (!isFinite(amt) || amt <= 0) { showMsg('Please enter a valid payment amount.', false); return; }
    if (typeof window.Razorpay !== 'function') { showMsg('Payment gateway is loading. Please try again in a moment.', false); return; }

    msgEl.hidden = true;
    var options = {
      key: <?= json_encode($rzpKey) ?>,
      amount: Math.round(amt * 100),
      name: 'Coldwell Banker India',
      description: 'Quick Pay',
      image: <?= json_encode($logoUrl) ?>,
      handler: function (response) {
        showMsg('Payment successful. Payment ID: ' + (response.razorpay_payment_id || ''), true);
        form.reset();
      },
      prefill: { name: name, email: email },
      notes: { address: 'Coldwell Banker India' },
      theme: { color: '#1d3761' }
    };
    var rzp = new window.Razorpay(options);
    rzp.open();
  });
})();
</script>
</body>
</html>
