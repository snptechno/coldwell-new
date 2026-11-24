<?php
/**
 * Admin > CMS > Menus > add / edit a link.
 *
 * @var string                     $location
 * @var array<string, mixed>       $def
 * @var array<string, mixed>|null  $item
 * @var array<string, mixed>       $values
 * @var array<string, string>      $form_errors
 * @var list<array<string, mixed>> $pages
 * @var list<array<string, mixed>> $parents
 * @var bool                       $has_children
 * @var bool                       $full
 */
$v    = static fn (string $k): string => (string) ($values[$k] ?? '');
$err  = static fn (string $k): string => isset($form_errors[$k]) ? '<span class="text-red" style="display:block;">' . esc($form_errors[$k]) . '</span>' : '';
$type = $v('link_type') === 'url' ? 'url' : 'page';
$back = site_url('admin/cms/menus/' . $location);
?>
<div class="row">
  <div class="col-md-12">
    <?= $this->include('admin/blog/_flash') ?>
    <?php if ($form_errors !== []): ?>
    <div class="alert alert-danger">Please correct the highlighted fields.</div>
    <?php endif; ?>
    <?php if ($full): ?>
    <div class="alert alert-warning">This menu already has the maximum of <?= (int) $def['max'] ?> links. Delete one before adding another.</div>
    <?php endif; ?>
    <div class="box box-primary">
      <div class="box-header with-border"><h3 class="box-title"><?= esc($form_title) ?></h3></div>
      <form class="form-horizontal" autocomplete="off" action="" method="post">
        <?= csrf_field() ?>
        <div class="box-body">
          <div class="form-group<?= isset($form_errors['label']) ? ' has-error' : '' ?>">
            <label for="label" class="col-sm-2 control-label">Label *</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="label" name="label" maxlength="120" value="<?= esc($v('label'), 'attr') ?>" required>
              <?= $err('label') ?>
            </div>
          </div>

          <div class="form-group">
            <span class="col-sm-2 control-label"><strong>Links to *</strong></span>
            <div class="col-sm-10">
              <label class="radio-inline"><input type="radio" name="link_type" value="page" <?= $type === 'page' ? 'checked' : '' ?>> CMS page</label>
              <label class="radio-inline"><input type="radio" name="link_type" value="url" <?= $type === 'url' ? 'checked' : '' ?>> URL</label>
              <?= $err('link_type') ?>
            </div>
          </div>
          <div class="form-group js-link js-link-page<?= isset($form_errors['page_id']) ? ' has-error' : '' ?>">
            <label for="page_id" class="col-sm-2 control-label">CMS page</label>
            <div class="col-sm-10">
              <select id="page_id" name="page_id" class="form-control">
                <option value="">— choose a page —</option>
                <?php foreach ($pages as $p): ?>
                <option value="<?= (int) $p['id'] ?>" <?= $v('page_id') === (string) $p['id'] ? 'selected' : '' ?>>
                  <?= esc($p['title']) ?> (<?= (int) $p['is_home'] === 1 ? 'home page' : esc($p['type']) ?><?= (int) $p['status'] === 1 ? '' : ', draft' ?>)
                </option>
                <?php endforeach; ?>
              </select>
              <?= $err('page_id') ?>
              <p class="help-block">The link follows the page if its URL changes. Draft pages are left out of the menu until published.</p>
            </div>
          </div>
          <div class="form-group js-link js-link-url<?= isset($form_errors['url']) ? ' has-error' : '' ?>">
            <label for="url" class="col-sm-2 control-label">URL</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="url" name="url" maxlength="255" value="<?= esc($v('url'), 'attr') ?>" placeholder="contact-us  or  https://www.example.com">
              <?= $err('url') ?>
              <p class="help-block">A site path (<code>contact-us</code>, <code>franchise-details?x=1</code>), a full <code>https://</code> address, <code>#anchor</code>, <code>mailto:</code> or <code>tel:</code>.</p>
            </div>
          </div>

          <?php if ((int) $def['depth'] >= 2): ?>
          <div class="form-group<?= isset($form_errors['parent_id']) ? ' has-error' : '' ?>">
            <label for="parent_id" class="col-sm-2 control-label">Parent</label>
            <div class="col-sm-10">
              <?php if ($has_children): ?>
              <p class="form-control-static text-muted">Top level (this link has sub-items).</p>
              <?php else: ?>
              <select id="parent_id" name="parent_id" class="form-control">
                <option value="">— top level —</option>
                <?php foreach ($parents as $p): ?>
                <option value="<?= (int) $p['id'] ?>" <?= $v('parent_id') === (string) $p['id'] ? 'selected' : '' ?>><?= esc($p['label']) ?></option>
                <?php endforeach; ?>
              </select>
              <p class="help-block">A link with sub-items becomes a dropdown in the header and a group in the phone menu.</p>
              <?php endif; ?>
              <?= $err('parent_id') ?>
            </div>
          </div>
          <?php elseif (isset($form_errors['parent_id'])): ?>
          <div class="form-group has-error">
            <label class="col-sm-2 control-label">Parent</label>
            <div class="col-sm-10"><?= $err('parent_id') ?></div>
          </div>
          <?php endif; ?>

          <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
              <div class="checkbox"><label><input type="checkbox" name="new_tab" value="1" <?= (int) $v('new_tab') === 1 ? 'checked' : '' ?>> Open in a new tab</label></div>
              <p class="help-block">Links to other websites always open in a new tab.</p>
            </div>
          </div>

          <div class="form-group<?= isset($form_errors['active_keys']) ? ' has-error' : '' ?>">
            <label for="active_keys" class="col-sm-2 control-label">Highlight keys</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="active_keys" name="active_keys" maxlength="255" value="<?= esc($v('active_keys'), 'attr') ?>" placeholder="about us, corporate team">
              <?= $err('active_keys') ?>
              <p class="help-block">Optional, comma separated. The link is highlighted as the current section on the older site pages whose
                <code>page_active</code> value matches one of these keys (and on CMS pages whose “menu highlight key” matches).
                Keys are matched exactly, e.g. <code>about us</code>, <code>media center</code>.</p>
            </div>
          </div>

          <div class="form-group<?= isset($form_errors['sort_order']) ? ' has-error' : '' ?>">
            <label for="sort_order" class="col-sm-2 control-label">Sort order</label>
            <div class="col-sm-2">
              <input type="number" class="form-control" id="sort_order" name="sort_order" value="<?= esc($v('sort_order'), 'attr') ?>" placeholder="auto">
            </div>
            <div class="col-sm-8"><?= $err('sort_order') ?><p class="help-block">Lower numbers come first. Empty puts the link last.</p></div>
          </div>

          <div class="form-group<?= isset($form_errors['status']) ? ' has-error' : '' ?>">
            <label for="status" class="col-sm-2 control-label">Status</label>
            <div class="col-sm-3">
              <select id="status" name="status" class="form-control">
                <option value="1" <?= $v('status') !== '0' ? 'selected' : '' ?>>Shown</option>
                <option value="0" <?= $v('status') === '0' ? 'selected' : '' ?>>Hidden</option>
              </select>
              <?= $err('status') ?>
            </div>
          </div>
        </div>
        <div class="box-footer">
          <a href="<?= esc($back, 'attr') ?>" class="btn btn-default">Back</a>
          <button type="submit" class="btn btn-info pull-right" <?= $full ? 'disabled' : '' ?>>Save</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script>
(function ($) {
  function sync() {
    var t = $('input[name="link_type"]:checked').val() || 'page';
    $('.js-link').hide();
    $('.js-link-' + t).show();
  }
  $('input[name="link_type"]').on('change', sync);
  sync();
})(jQuery);
</script>
