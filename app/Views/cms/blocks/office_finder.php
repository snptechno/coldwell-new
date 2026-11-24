<?php
/**
 * Block "office_finder" (Figma Franchise 77:58 "Find a Franchise Office"): heading + finder bar
 * (city / area selects, Find Now), city tabs and office cards from the franchises table.
 * Without JavaScript every office is listed; cb-site.js filters by tab / city / area and opens
 * on the busiest city.
 *
 * @var array<string, string> $s    block settings
 * @var string                $sfx  id suffix for repeated blocks
 */
use App\Libraries\Cms\Offices;

helper('cb');
$offices = Offices::all();
$cities  = Offices::cities($offices);
$id      = 'cb-finder-title' . $sfx;
$default = array_key_first($cities) ?? '';
?>
<section class="cb-finder" aria-labelledby="<?= esc($id, 'attr') ?>" data-finder data-default="<?= esc($default, 'attr') ?>">
  <div class="cb-wrap">
    <div class="cb-head cb-head--split cb-reveal">
      <div>
        <h2 class="cb-h2" id="<?= esc($id, 'attr') ?>"><?= esc(trim($s['title'] ?? '')) ?></h2>
        <?php if (trim($s['lead'] ?? '') !== ''): ?><p class="cb-p"><?= esc(trim($s['lead'])) ?></p><?php endif ?>
      </div>
      <?php if ($offices !== []): ?>
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
      <button type="button" class="cb-tab" role="tab" aria-selected="false" data-city="">All Cities</button>
      <?php foreach ($cities as $city => $n): ?>
      <button type="button" class="cb-tab" role="tab" aria-selected="false" data-city="<?= esc($city, 'attr') ?>"><?= esc($city) ?></button>
      <?php endforeach ?>
    </div>
    <ul class="cb-offices" data-finder-list aria-live="polite">
      <?php foreach ($offices as $o): ?>
      <li data-city="<?= esc($o['city'], 'attr') ?>" data-id="<?= (int) $o['id'] ?>">
        <a class="cb-office" href="<?= esc($o['href'], 'attr') ?>">
          <span class="cb-office__icon" aria-hidden="true"><?= cb_icon('pin') ?></span>
          <span class="cb-office__txt"><b><?= esc($o['name']) ?></b><span><?= esc(trim($o['area'] . ', ' . $o['city'], ', ')) ?></span></span>
          <span class="cb-office__go" aria-hidden="true"><?= cb_icon('arrow') ?></span>
        </a>
      </li>
      <?php endforeach ?>
    </ul>
    <p class="cb-finder__count cb-sr" data-finder-count aria-live="polite"></p>
    <?php endif ?>
  </div>
</section>
