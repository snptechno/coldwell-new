<?php
/**
 * Admin > CMS > Site settings, one box per SiteSchema::SETTINGS group.
 *
 * @var array<string, array<string, array<string, mixed>>> $groups
 * @var array<string, string>                              $values
 * @var array<string, string>                              $form_errors
 */
?>
<div class="row">
  <div class="col-md-12">
    <?= $this->include('admin/blog/_flash') ?>
    <?php if ($form_errors !== []): ?>
    <div class="alert alert-danger">Nothing was saved. Please correct the highlighted fields.</div>
    <?php endif; ?>
    <form class="form-horizontal" autocomplete="off" action="" method="post">
      <?= csrf_field() ?>
      <?php foreach ($groups as $group => $fields): ?>
      <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title"><?= esc($group) ?></h3></div>
        <div class="box-body">
          <?php foreach ($fields as $key => $f): ?>
          <?php $val = (string) ($values[$key] ?? ''); $id = 'set_' . $key; ?>
          <div class="form-group<?= isset($form_errors[$key]) ? ' has-error' : '' ?>">
            <label for="<?= esc($id, 'attr') ?>" class="col-sm-2 control-label"><?= esc($f['label']) ?></label>
            <div class="col-sm-10">
              <?php if ($f['type'] === 'textarea'): ?>
              <textarea class="form-control" id="<?= esc($id, 'attr') ?>" name="<?= esc($key, 'attr') ?>" rows="3" maxlength="<?= (int) $f['max'] ?>"><?= esc($val) ?></textarea>
              <?php else: ?>
              <input type="<?= str_ends_with($key, '_email') ? 'email' : 'text' ?>" class="form-control" id="<?= esc($id, 'attr') ?>" name="<?= esc($key, 'attr') ?>" maxlength="<?= (int) $f['max'] ?>" value="<?= esc($val, 'attr') ?>">
              <?php endif; ?>
              <?php if (isset($form_errors[$key])): ?><span class="text-red" style="display:block;"><?= esc($form_errors[$key]) ?></span><?php endif; ?>
              <?php if ($f['type'] === 'url'): ?>
              <p class="help-block">A site path (<code>partner-with-us</code>), a full <code>https://</code> address, <code>#anchor</code>, <code>mailto:</code> or <code>tel:</code>.</p>
              <?php endif; ?>
              <?php if (! empty($f['hint'])): ?><p class="help-block"><?= esc($f['hint']) ?></p><?php endif; ?>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endforeach; ?>
      <div class="box">
        <div class="box-footer">
          <a href="<?= esc(site_url('admin/cms/menus'), 'attr') ?>" class="btn btn-default">Menus</a>
          <button type="submit" class="btn btn-info pull-right">Save settings</button>
        </div>
      </div>
    </form>
  </div>
</div>
