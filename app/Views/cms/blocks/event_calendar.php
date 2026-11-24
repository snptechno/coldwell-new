<?php
/**
 * Block "event_calendar" (Figma L&D 83:58 "Training Calendar"): navy panel with upcoming training
 * events (date tile, title, date range, duration, seats, Register Interest). Only future or
 * running events are listed; otherwise an honest "next batches" message with the button.
 *
 * @var array<string, string> $s    block settings
 * @var string                $sfx  id suffix for repeated blocks
 */
use App\Libraries\Cms\Learning;

helper('cb');
$events = Learning::upcomingEvents(max(1, min(20, (int) ($s['limit'] ?? 5) ?: 5)));
$id     = 'cb-cal-title' . $sfx;
$label  = trim($s['cta_label'] ?? '') ?: 'Register Interest';
?>
<section class="cb-cal" id="calendar" aria-labelledby="<?= esc($id, 'attr') ?>">
  <div class="cb-cal__panel">
    <div class="cb-cal__head cb-reveal">
      <h2 class="cb-h2" id="<?= esc($id, 'attr') ?>"><?= esc(trim($s['title'] ?? '')) ?></h2>
      <?php if (trim($s['lead'] ?? '') !== ''): ?><p class="cb-p"><?= esc(trim($s['lead'])) ?></p><?php endif ?>
    </div>
    <?php if ($events !== []): ?>
    <ul class="cb-cal__list">
      <?php foreach ($events as $i => $e): ?>
      <li class="cb-event cb-reveal<?= $i === 0 ? ' cb-event--next' : '' ?>">
        <span class="cb-event__date" aria-hidden="true"><small><?= esc($e['mon']) ?></small><b><?= esc($e['day']) ?></b></span>
        <span class="cb-event__txt">
          <b><?= esc($e['title']) ?></b>
          <span class="cb-event__meta"><span><?= esc($e['dates']) ?></span><?php if ($e['duration'] !== ''): ?><span><?= esc($e['duration']) ?></span><?php endif ?><?php if ($e['seats'] !== ''): ?><span><?= esc($e['seats']) ?></span><?php endif ?></span>
        </span>
        <?= cb_pill($label, $s['cta_url'] ?? '', $i === 0 ? 'gold' : 'light', 'cb-pill--sm') ?>
      </li>
      <?php endforeach ?>
    </ul>
    <?php else: ?>
    <div class="cb-cal__empty cb-reveal">
      <span class="cb-event__date" aria-hidden="true"><?= cb_icon('calendar') ?></span>
      <span class="cb-event__txt">
        <b><?= esc(trim($s['empty_title'] ?? '') ?: 'New training batches will be announced soon.') ?></b>
        <?php if (trim($s['empty_lead'] ?? '') !== ''): ?><span class="cb-event__lead"><?= esc(trim($s['empty_lead'])) ?></span><?php endif ?>
      </span>
      <?= cb_pill($label, $s['cta_url'] ?? '', 'gold', 'cb-pill--sm') ?>
    </div>
    <?php endif ?>
  </div>
</section>
