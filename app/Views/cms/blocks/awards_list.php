<?php
/**
 * Block "awards_list" (Figma Media 79:58 "Awards & Recognition"): soft panel, photo of the
 * newest award that has one, and the newest awards as a list (first highlighted).
 *
 * @var array<string, string> $s    block settings
 * @var string                $sfx  id suffix for repeated blocks
 */
use App\Libraries\Cms\Media;

helper('cb');
$awards = Media::awards(max(1, min(20, (int) ($s['limit'] ?? 5) ?: 5)));
if ($awards === []) {
    return;
}
$photo = '';
$alt   = '';
foreach ($awards as $a) {
    if ($a['image'] !== '') {
        $photo = $a['image'];
        $alt   = $a['name'];
        break;
    }
}
$id = 'cb-awards-title' . $sfx;
?>
<section class="cb-awards" aria-labelledby="<?= esc($id, 'attr') ?>">
  <div class="cb-awards__panel">
    <?php if ($photo !== ''): ?>
    <figure class="cb-awards__photo cb-reveal"><img src="<?= esc($photo, 'attr') ?>" alt="<?= esc($alt, 'attr') ?>" loading="lazy" decoding="async" width="420" height="620"></figure>
    <?php endif ?>
    <div class="cb-awards__copy cb-reveal">
      <h2 class="cb-h2" id="<?= esc($id, 'attr') ?>"><?= esc(trim($s['title'] ?? '')) ?></h2>
      <?php if (trim($s['lead'] ?? '') !== ''): ?><p class="cb-p"><?= esc(trim($s['lead'])) ?></p><?php endif ?>
      <ul class="cb-awards__list">
        <?php foreach ($awards as $i => $a): ?>
        <li class="cb-award<?= $i === 0 ? ' cb-award--top' : '' ?>">
          <span class="cb-award__icon" aria-hidden="true"><?= cb_icon('award') ?></span>
          <span class="cb-award__name"><?= esc($a['name']) ?></span>
        </li>
        <?php endforeach ?>
      </ul>
      <?= cb_pill($s['cta_label'] ?? '', $s['cta_url'] ?? '', 'light') ?>
    </div>
  </div>
</section>
