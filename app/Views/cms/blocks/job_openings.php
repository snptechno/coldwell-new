<?php
/**
 * Block "job_openings" (Figma Careers 90:58 "Current Openings"): soft panel, heading + tabs by
 * job type with counts, expandable rows (<details>, first open) with requirement chips, key
 * duties and Apply (jumps to #apply with the role pre-selected).
 *
 * @var array<string, string> $s    block settings
 * @var string                $sfx  id suffix for repeated blocks
 */
use App\Libraries\Cms\Jobs;

helper('cb');
$groups = Jobs::open();
$id     = 'cb-jobs-title' . $sfx;
$k      = 0;
?>
<section class="cb-jobs" id="openings" aria-labelledby="<?= esc($id, 'attr') ?>" data-vtabs>
  <div class="cb-jobs__panel">
    <div class="cb-head cb-head--split cb-reveal">
      <div>
        <h2 class="cb-h2" id="<?= esc($id, 'attr') ?>"><?= esc(trim($s['title'] ?? '')) ?></h2>
        <?php if (trim($s['lead'] ?? '') !== ''): ?><p class="cb-p"><?= esc(trim($s['lead'])) ?></p><?php endif ?>
      </div>
      <?php if (count($groups) > 1): ?>
      <div class="cb-tabs cb-tabs--left" role="tablist" aria-label="Job types">
        <?php foreach ($groups as $label => $jobs): $i = $k++; ?>
        <button type="button" class="cb-tab" role="tab" id="cb-jt-<?= $i . esc($sfx, 'attr') ?>" aria-controls="cb-jp-<?= $i . esc($sfx, 'attr') ?>" aria-selected="<?= $i === 0 ? 'true' : 'false' ?>"<?= $i ? ' tabindex="-1"' : '' ?>><?= esc($label) ?> (<?= count($jobs) ?>)</button>
        <?php endforeach ?>
      </div>
      <?php endif ?>
    </div>
    <?php if ($groups === []): ?>
    <p class="cb-jobs__empty"><?= esc(trim($s['empty'] ?? '') ?: 'There are no open positions right now. Send us your CV through the form below and we will keep it on file.') ?></p>
    <?php endif ?>
    <?php $k = 0; foreach ($groups as $label => $jobs): $i = $k++; ?>
    <div class="cb-jobs__list" role="<?= count($groups) > 1 ? 'tabpanel' : 'region' ?>" id="cb-jp-<?= $i . esc($sfx, 'attr') ?>" aria-labelledby="<?= count($groups) > 1 ? 'cb-jt-' . $i . esc($sfx, 'attr') : esc($id, 'attr') ?>"<?= $i ? ' hidden' : '' ?>>
      <?php foreach ($jobs as $n => $j): ?>
      <details class="cb-job cb-reveal"<?= $n === 0 ? ' open' : '' ?>>
        <summary>
          <span class="cb-job__head">
            <b><?= esc($j['title']) ?></b>
            <span class="cb-job__chips">
              <?php if ($j['qualification'] !== ''): ?><span><?= cb_icon('award') ?><?= esc($j['qualification']) ?></span><?php endif ?>
              <?php if ($j['exp'] !== ''): ?><span><?= cb_icon('chart') ?><?= esc($j['exp']) ?></span><?php endif ?>
              <?php if ($j['location'] !== ''): ?><span><?= cb_icon('pin') ?><?= esc($j['location']) ?></span><?php endif ?>
            </span>
          </span>
          <span class="cb-qa__icon" aria-hidden="true"><?= cb_icon('plus') ?><?= cb_icon('minus') ?></span>
        </summary>
        <div class="cb-job__body">
          <?php if ($j['duties'] !== []): ?>
          <div>
            <p class="cb-job__label">Key responsibilities</p>
            <ul class="cb-job__duties"><?php foreach ($j['duties'] as $d): ?><li><?= esc($d) ?></li><?php endforeach ?></ul>
            <a class="cb-job__more" href="<?= esc($j['href'], 'attr') ?>">Full job description</a>
          </div>
          <?php endif ?>
          <a class="cb-pill cb-pill--gold" href="#apply" data-apply-job="<?= (int) $j['id'] ?>"><span>Apply Now</span><span class="cb-pill__icon"><?= cb_icon('arrow') ?></span><span class="cb-sr"> for <?= esc($j['title']) ?></span></a>
        </div>
      </details>
      <?php endforeach ?>
    </div>
    <?php endforeach ?>
  </div>
</section>
