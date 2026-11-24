<?php
/**
 * Block "stats_panel" (Figma About 73:58):
 *   layout "center" = "Our Purpose": centred eyebrow, statement, outlined row of figures.
 *   layout "split"  = "About Coldwell Banker India": soft panel, copy + navy pill left, icon figure cards right.
 *
 * @var array<string, string>      $s      block settings
 * @var array<string, list<array>> $items  stat
 * @var string                     $sfx    id suffix for repeated blocks
 */
helper('cb');
$stats = array_slice($items['stat'] ?? [], 0, 4);
$id    = 'cb-stats-title' . $sfx;
$split = ($s['layout'] ?? 'center') === 'split';
$icons = \App\Libraries\Cms\BlockTypes::ICONS;
// Live network counts: {offices} / {cities} in figures and text are replaced from the franchises table.
$live  = static function (string $v): string {
    static $map = null;
    if (! str_contains($v, '{')) {
        return $v;
    }
    if ($map === null) {
        $o   = \App\Libraries\Cms\Offices::all();
        $map = ['{offices}' => (string) count($o), '{cities}' => (string) count(\App\Libraries\Cms\Offices::cities($o))];
    }

    return strtr($v, $map);
};
foreach ($stats as $k => &$st) {
    $had         = str_contains((string) $st['title'], '{');
    $st['title'] = $live((string) $st['title']);
    if ($had && in_array(trim($st['title']), ['0', ''], true)) {
        unset($stats[$k]); // live count unavailable: hide the figure rather than show "0"
    }
}
unset($st);
$stats = array_values($stats);
$s['body'] = $live((string) ($s['body'] ?? ''));
?>
<?php if ($split): ?>
<section class="cb-split" aria-labelledby="<?= esc($id, 'attr') ?>">
  <div class="cb-split__panel">
    <div class="cb-split__copy cb-reveal">
      <?php if (trim($s['eyebrow'] ?? '') !== ''): ?><p class="cb-eyebrow"><?= esc(trim($s['eyebrow'])) ?></p><?php endif ?>
      <h2 class="cb-h2" id="<?= esc($id, 'attr') ?>"><?= esc(trim($s['title'] ?? '')) ?></h2>
      <p class="cb-p"><?= esc(trim($s['body'] ?? '')) ?></p>
      <?php if (($pill = cb_pill($s['cta_label'] ?? '', $s['cta_url'] ?? '', 'navy')) !== ''): ?><div><?= $pill ?></div><?php endif ?>
    </div>
    <?php if ($stats !== []): ?>
    <ul class="cb-split__stats cb-reveal">
      <?php foreach ($stats as $st): $ic = isset($icons[$st['kicker'] ?? '']) ? $st['kicker'] : 'pin'; ?>
      <li class="cb-icard">
        <span class="cb-icard__icon"><?= cb_icon($ic) ?></span>
        <span><b><?= esc($st['title']) ?></b><span><?= esc($st['subtitle']) ?></span></span>
      </li>
      <?php endforeach ?>
    </ul>
    <?php endif ?>
  </div>
</section>
<?php else: ?>
<section class="cb-purpose" aria-labelledby="<?= esc($id, 'attr') ?>">
  <?php if (trim($s['eyebrow'] ?? '') !== ''): ?>
  <h2 class="cb-eyebrow cb-eyebrow--center cb-reveal" id="<?= esc($id, 'attr') ?>"><?= esc(trim($s['eyebrow'])) ?></h2>
  <?php endif ?>
  <p class="cb-statement cb-reveal"<?= trim($s['eyebrow'] ?? '') === '' ? ' id="' . esc($id, 'attr') . '"' : '' ?>><?= esc(trim($s['body'] ?? '')) ?></p>
  <?php if ($stats !== []): ?>
  <ul class="cb-figrow cb-reveal" style="--n:<?= count($stats) ?>">
    <?php foreach ($stats as $st): ?>
    <li><b><?= esc($st['title']) ?></b><span><?= esc($st['subtitle']) ?></span></li>
    <?php endforeach ?>
  </ul>
  <?php endif ?>
  <?php if (($pill = cb_pill($s['cta_label'] ?? '', $s['cta_url'] ?? '', 'gold')) !== ''): ?><div class="cb-reveal"><?= $pill ?></div><?php endif ?>
</section>
<?php endif ?>
