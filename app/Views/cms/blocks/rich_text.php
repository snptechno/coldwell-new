<?php
/**
 * Block "rich_text": eyebrow, heading and formatted text in the content column.
 * `body` is an html field: sanitised with BlogContent::sanitize() on save, and again here
 * so a row written outside the admin can never inject markup.
 *
 * @var array<string, mixed>  $block
 * @var array<string, string> $s   block settings
 */
use App\Libraries\BlogContent;

$body    = BlogContent::sanitize((string) ($s['body'] ?? ''));
$title   = trim($s['title'] ?? '');
$eyebrow = trim($s['eyebrow'] ?? '');
$center  = ($s['align'] ?? 'left') === 'center';
$id      = 'cb-rich-title-' . (int) ($block['id'] ?? 0);
if (trim(strip_tags($body, '<img>')) === '' && $title === '') {
    return;
}
?>
<section class="cb-rich<?= $center ? ' cb-rich--center' : '' ?>"<?= $title !== '' ? ' aria-labelledby="' . esc($id, 'attr') . '"' : '' ?>>
  <div class="cb-wrap">
    <div class="cb-rich__in">
      <?php if ($eyebrow !== '' || $title !== ''): ?>
      <div class="cb-rich__head cb-reveal">
        <?php if ($eyebrow !== ''): ?><p class="cb-eyebrow<?= $center ? ' cb-eyebrow--center' : '' ?>"><?= esc($eyebrow) ?></p><?php endif ?>
        <?php if ($title !== ''): ?><h2 class="cb-h2" id="<?= esc($id, 'attr') ?>"><?= esc($title) ?></h2><?php endif ?>
      </div>
      <?php endif ?>
      <?php if ($body !== ''): ?><div class="cb-prose"><?= $body ?></div><?php endif ?>
    </div>
  </div>
</section>
