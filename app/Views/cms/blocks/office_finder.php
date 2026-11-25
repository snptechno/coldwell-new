<?php
/**
 * Block "office_finder": open franchise offices from the franchises table.
 *   layout "finder"    (Franchise 77:58): heading + city/area selects + Find Now, city tabs, cards.
 *   layout "directory" (Contact 85:58): heading + "View all" pill, city tabs with counts, cards with phone.
 * Without JavaScript every office is listed; cb-site.js filters by tab / city / area and opens
 * on the busiest city. {offices} / {cities} in the lead are replaced with live counts.
 *
 * @var array<string, string> $s    block settings
 * @var string                $sfx  id suffix for repeated blocks
 */
use App\Libraries\Cms\Offices;

helper('cb');
$offices = Offices::all();
$cities  = Offices::cities($offices);
$id      = 'cb-finder-title' . $sfx;
$reqCity = trim((string) service('request')->getGet('city'));
$matched = Offices::matchCity($reqCity, $cities);
$default = $matched !== '' ? $matched : (array_key_first($cities) ?? '');
$dir     = ($s['layout'] ?? 'finder') === 'directory';
$lead    = strtr(trim($s['lead'] ?? ''), ['{offices}' => (string) count($offices), '{cities}' => (string) count($cities)]);
?>
<section class="cb-finder<?= $dir ? ' cb-finder--dir' : '' ?>" id="offices<?= esc($sfx, 'attr') ?>" aria-labelledby="<?= esc($id, 'attr') ?>" data-finder data-default="<?= esc($default, 'attr') ?>">
  <div class="cb-wrap">
    <div class="cb-head cb-head--split cb-reveal">
      <div>
        <h2 class="cb-h2" id="<?= esc($id, 'attr') ?>"><?= esc(trim($s['title'] ?? '')) ?></h2>
        <?php if ($lead !== ''): ?><p class="cb-p"><?= esc($lead) ?></p><?php endif ?>
      </div>
      <?php if ($dir): ?>
      <?= cb_pill($s['cta_label'] ?? '', $s['cta_url'] ?? '', 'light', 'cb-pill--on-section') ?>
      <?php elseif ($offices !== []): ?>
      <form class="cb-finder__bar" data-finder-form>
        <label class="cb-sr" for="cb-finder-city<?= esc($sfx, 'attr') ?>">City</label>
        <span class="cb-select"><select id="cb-finder-city<?= esc($sfx, 'attr') ?>" data-finder-city>
          <option value="">Select City</option>
          <?php foreach ($cities as $city => $n): ?><option value="<?= esc($city, 'attr') ?>"><?= esc($city) ?></option><?php endforeach ?>
        </select><?= cb_icon('chev') ?></span>
        <label class="cb-sr" for="cb-finder-area<?= esc($sfx, 'attr') ?>">Area</label>
        <span class="cb-select"><select id="cb-finder-area<?= esc($sfx, 'attr') ?>" data-finder-area>
          <option value="">Select Area</option>
          <?php foreach ($offices as $o): ?><option value="<?= (int) $o['id'] ?>" data-city="<?= esc($o['city'], 'attr') ?>"><?= esc($o['area'] !== '' ? $o['area'] : $o['name']) ?></option><?php endforeach ?>
        </select><?= cb_icon('chev') ?></span>
        <button type="submit" class="cb-pill cb-pill--gold"><span>Find Now</span><span class="cb-pill__icon"><?= cb_icon('arrow') ?></span></button>
      </form>
      <?php endif ?>
    </div>

    <?php if ($offices === []): ?>
    <p class="cb-p">Office details are being updated. <a href="<?= esc(site_url('contact-us'), 'attr') ?>">Contact us</a> to find your nearest Coldwell Banker office.</p>
    <?php else: ?>
    <div class="cb-tabs cb-tabs--left" role="tablist" aria-label="Filter offices by city">
      <?php if (! $dir): ?><button type="button" class="cb-tab" role="tab" aria-selected="false" data-city="">All Cities</button><?php endif ?>
      <?php foreach ($cities as $city => $n): ?>
      <button type="button" class="cb-tab" role="tab" aria-selected="false" data-city="<?= esc($city, 'attr') ?>"><?= esc($city) ?><?= $dir ? ' (' . (int) $n . ')' : '' ?></button>
      <?php endforeach ?>
    </div>
    <ul class="cb-offices" data-finder-list aria-live="polite">
      <?php foreach ($offices as $o): ?>
      <li data-city="<?= esc($o['city'], 'attr') ?>" data-id="<?= (int) $o['id'] ?>">
        <?php if ($dir): ?>
        <div class="cb-office cb-office--dir">
          <span class="cb-office__icon" aria-hidden="true"><?= cb_icon('pin') ?></span>
          <span class="cb-office__txt">
            <a href="<?= esc($o['href'], 'attr') ?>"><b>Coldwell Banker <?= esc($o['name']) ?></b></a>
            <span><?= esc(trim($o['area'] . ', ' . $o['city'], ', ')) ?></span>
          </span>
          <span class="cb-office__foot">
            <?php if (($tel = preg_replace('/[^0-9+]/', '', $o['phone'])) !== ''): ?>
            <a class="cb-office__tel" href="tel:<?= esc($tel, 'attr') ?>"><?= cb_icon('phone') ?><?= esc($o['phone']) ?></a>
            <?php else: ?><span></span><?php endif ?>
            <a class="cb-office__go" href="<?= esc($o['href'], 'attr') ?>" aria-label="Coldwell Banker <?= esc($o['name'], 'attr') ?> details"><?= cb_icon('arrow') ?></a>
          </span>
        </div>
        <?php else: ?>
        <a class="cb-office" href="<?= esc($o['href'], 'attr') ?>">
          <span class="cb-office__icon" aria-hidden="true"><?= cb_icon('pin') ?></span>
          <span class="cb-office__txt"><b><?= esc($o['name']) ?></b><span><?= esc(trim($o['area'] . ', ' . $o['city'], ', ')) ?></span></span>
          <span class="cb-office__go" aria-hidden="true"><?= cb_icon('arrow') ?></span>
        </a>
        <?php endif ?>
      </li>
      <?php endforeach ?>
    </ul>
    <p class="cb-finder__count cb-sr" data-finder-count aria-live="polite"></p>
    <?php endif ?>
  </div>
</section>
