<?php
$c   = $cat_info ?? [];
$val = static fn (string $k, $d = '') => old($k, $c[$k] ?? $d);
$err = static fn (string $k) => ($form_errors[$k] ?? '') !== '' ? esc($form_errors[$k]) : validation_show_error($k);
?>
<div class="row">
  <div class="col-md-12">
    <div class="box box-primary">
      <div class="box-header with-border"><h3 class="box-title"><?= esc($form_title) ?></h3></div>
      <form class="form-horizontal" autocomplete="off" action="" method="post">
        <?= csrf_field() ?>
        <div class="box-body">
          <div class="form-group">
            <label for="name" class="col-sm-2 control-label">Name *</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="name" name="name" maxlength="150" value="<?= esc($val('name'), 'attr') ?>" required>
              <span class="text-red" style="display:block;"><?= $err('name') ?></span>
            </div>
          </div>
          <div class="form-group">
            <label for="slug" class="col-sm-2 control-label">URL slug</label>
            <div class="col-sm-10">
              <div class="input-group">
                <span class="input-group-addon"><?= esc(rtrim(base_url('blog/category'), '/')) ?>/</span>
                <input type="text" class="form-control" id="slug" name="slug" maxlength="190" value="<?= esc($val('slug'), 'attr') ?>" placeholder="generated-from-the-name">
              </div>
              <span class="text-red" style="display:block;"><?= $err('slug') ?></span>
            </div>
          </div>
          <div class="form-group">
            <label for="description" class="col-sm-2 control-label">Description</label>
            <div class="col-sm-10"><textarea class="form-control" id="description" name="description" rows="3"><?= esc($val('description')) ?></textarea></div>
          </div>
          <div class="form-group">
            <label for="meta_title" class="col-sm-2 control-label">Meta title</label>
            <div class="col-sm-10"><input type="text" class="form-control" id="meta_title" name="meta_title" maxlength="255" value="<?= esc($val('meta_title'), 'attr') ?>" placeholder="Defaults to the name"></div>
          </div>
          <div class="form-group">
            <label for="meta_description" class="col-sm-2 control-label">Meta description</label>
            <div class="col-sm-10"><textarea class="form-control" id="meta_description" name="meta_description" rows="2" maxlength="320"><?= esc($val('meta_description')) ?></textarea></div>
          </div>
          <div class="form-group">
            <label for="sort_order" class="col-sm-2 control-label">Sort order</label>
            <div class="col-sm-2"><input type="number" class="form-control" id="sort_order" name="sort_order" value="<?= esc((string) $val('sort_order', '0'), 'attr') ?>"></div>
          </div>
          <div class="form-group">
            <label for="status" class="col-sm-2 control-label">Status</label>
            <div class="col-sm-3">
              <select id="status" name="status" class="form-control">
                <option value="1" <?= (string) $val('status', '1') === '1' ? 'selected' : '' ?>>Active</option>
                <option value="0" <?= (string) $val('status', '1') === '0' ? 'selected' : '' ?>>Hidden</option>
              </select>
            </div>
          </div>
        </div>
        <div class="box-footer">
          <a href="<?= base_url('admin/blog/categories') ?>" class="btn btn-default">Back</a>
          <button type="submit" class="btn btn-info pull-right">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script>
(function ($) {
  var touched = $('#slug').val() !== '';
  function slugify(s) { return (s || '').toLowerCase().replace(/&/g, ' and ').replace(/['’]/g, '').replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, ''); }
  $('#name').on('input', function () { if (!touched) { $('#slug').val(slugify(this.value)); } });
  $('#slug').on('input', function () { touched = this.value !== ''; }).on('blur', function () { this.value = slugify(this.value); });
})(jQuery);
</script>
