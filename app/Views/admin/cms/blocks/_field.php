<?php
/**
 * One form field generated from a BlockTypes field definition.
 *
 * @var string               $key    field key (settings key or item column)
 * @var string               $name   input name (e.g. settings[title] or title)
 * @var array<string, mixed> $field  definition: label, type, required, max, hint, options
 * @var string               $value  current value (typed input on a failed save)
 * @var string               $error  error message ('' when none)
 */
use App\Libraries\Cms\CmsUrl;

$type  = $field['type'] ?? 'text';
$id    = 'f-' . preg_replace('/[^a-z0-9_-]/i', '-', $name);
$req   = ! empty($field['required']);
$max   = isset($field['max']) ? (int) $field['max'] : 0;
$value = (string) $value;
?>
<div class="form-group<?= $error !== '' ? ' has-error' : '' ?>">
  <label for="<?= esc($id, 'attr') ?>"><?= esc($field['label'] ?? $key) ?><?php if ($req): ?> <span class="text-red">*</span><?php endif; ?></label>
  <?php if ($type === 'textarea'): ?>
    <textarea class="form-control" id="<?= esc($id, 'attr') ?>" name="<?= esc($name, 'attr') ?>" rows="3"<?= $max ? ' maxlength="' . $max . '"' : '' ?>><?= esc($value) ?></textarea>
  <?php elseif ($type === 'html'): ?>
    <textarea class="form-control cms-html" id="<?= esc($id, 'attr') ?>" name="<?= esc($name, 'attr') ?>" rows="10"><?= esc($value) ?></textarea>
  <?php elseif ($type === 'select'): ?>
    <select class="form-control" id="<?= esc($id, 'attr') ?>" name="<?= esc($name, 'attr') ?>">
      <?php foreach (($field['options'] ?? []) as $ov => $ol): ?>
        <option value="<?= esc((string) $ov, 'attr') ?>" <?= (string) $ov === $value ? 'selected' : '' ?>><?= esc($ol) ?></option>
      <?php endforeach; ?>
    </select>
  <?php elseif ($type === 'image'): ?>
    <?php $src = CmsUrl::imageUrl($value); ?>
    <?php if ($src !== ''): ?>
      <img src="<?= esc($src, 'attr') ?>" alt="" class="cms-img-preview">
      <?php if (! $req): ?><label class="checkbox-inline"><input type="checkbox" name="remove_<?= esc($key, 'attr') ?>" value="1"> Remove</label><?php endif; ?>
    <?php endif; ?>
    <input type="file" id="<?= esc($id, 'attr') ?>" name="upload_<?= esc($key, 'attr') ?>" accept="image/jpeg,image/png,image/webp">
    <span class="help-block">JPG, PNG or WEBP, under 5 MB.<?= $src !== '' ? ' Upload a new file to replace the current one.' : '' ?></span>
  <?php elseif ($type === 'number'): ?>
    <input type="number" step="any" class="form-control" id="<?= esc($id, 'attr') ?>" name="<?= esc($name, 'attr') ?>" value="<?= esc($value, 'attr') ?>">
  <?php else: ?>
    <input type="text" class="form-control" id="<?= esc($id, 'attr') ?>" name="<?= esc($name, 'attr') ?>" value="<?= esc($value, 'attr') ?>"<?= $max ? ' maxlength="' . $max . '"' : '' ?><?= $type === 'url' ? ' placeholder="contact-us or https://…"' : '' ?>>
  <?php endif; ?>
  <?php if (! empty($field['hint']) || $type === 'url'): ?>
    <span class="help-block"><?= esc($field['hint'] ?? 'Site page path (e.g. contact-us), a full https:// link, #anchor, mailto: or tel:.') ?></span>
  <?php endif; ?>
  <?php if ($error !== ''): ?><span class="help-block text-red"><?= esc($error) ?></span><?php endif; ?>
</div>
