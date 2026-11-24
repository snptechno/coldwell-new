<?php
/**
 * @var array<string, mixed>      $page_info
 * @var array<string, mixed>      $block
 * @var array<string, mixed>      $def        BlockTypes::TYPES entry
 * @var string                    $group_key
 * @var array<string, mixed>      $group      group definition (label, max, fields)
 * @var array<string, mixed>|null $item       null when adding
 * @var array|null                $input      posted values on a failed save
 * @var array<string, string>     $form_errors
 */
$pageId  = (int) $page_info['id'];
$blockId = (int) $block['id'];
$back    = site_url('admin/cms/pages/' . $pageId . '/blocks/' . $blockId) . '#group-' . $group_key;
$hasHtml = false;
$sortVal = is_array($input) ? (string) ($input['sort_order'] ?? '') : ($item !== null ? (string) $item['sort_order'] : '');
$status  = is_array($input) ? (string) ($input['status'] ?? '1') : ($item !== null ? (string) $item['status'] : '1');
?>
<style>
  .cms-img-preview { max-width: 220px; max-height: 140px; display: block; margin: 6px 0; border: 1px solid #ddd; }
</style>
<div class="row">
  <div class="col-md-12">
    <?= $this->include('admin/blog/_flash') ?>
    <?php if ($form_errors !== []): ?>
      <div class="alert alert-danger">Please fix the highlighted fields below.</div>
    <?php endif; ?>
    <ol class="breadcrumb" style="background:none;padding-left:0">
      <li><a href="<?= esc(site_url('admin/cms/pages/edit/' . $pageId) . '#blocks', 'attr') ?>"><?= esc($page_info['title']) ?></a></li>
      <li><a href="<?= esc($back, 'attr') ?>"><?= esc($def['label']) ?></a></li>
      <li class="active"><?= esc($group['label']) ?></li>
    </ol>
  </div>
  <div class="col-md-8">
    <form autocomplete="off" enctype="multipart/form-data" action="" method="post">
      <?= csrf_field() ?>
      <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title"><?= esc($form_title) ?></h3></div>
        <div class="box-body">
          <?php foreach ($group['fields'] as $col => $field):
              $isImage = ($field['type'] ?? '') === 'image';
              $hasHtml = $hasHtml || ($field['type'] ?? '') === 'html';
              $value   = ! $isImage && is_array($input) ? (is_scalar($input[$col] ?? null) ? (string) $input[$col] : '') : (string) ($item[$col] ?? '');
          ?>
            <?= view('admin/cms/blocks/_field', [
                'key'   => $col,
                'name'  => $col,
                'field' => $field,
                'value' => $value,
                'error' => (string) ($form_errors[$isImage ? 'upload_' . $col : $col] ?? ''),
            ]) ?>
          <?php endforeach; ?>
          <div class="row">
            <div class="col-sm-6">
              <div class="form-group<?= isset($form_errors['sort_order']) ? ' has-error' : '' ?>">
                <label for="sort_order">Order</label>
                <input type="number" class="form-control" id="sort_order" name="sort_order" value="<?= esc($sortVal, 'attr') ?>" placeholder="Last">
                <?php if (isset($form_errors['sort_order'])): ?><span class="help-block text-red"><?= esc($form_errors['sort_order']) ?></span><?php endif; ?>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status" class="form-control">
                  <option value="1" <?= $status === '1' ? 'selected' : '' ?>>Visible</option>
                  <option value="0" <?= $status !== '1' ? 'selected' : '' ?>>Hidden</option>
                </select>
              </div>
            </div>
          </div>
        </div>
        <div class="box-footer">
          <a href="<?= esc($back, 'attr') ?>" class="btn btn-default">Cancel</a>
          <button type="submit" class="btn btn-info pull-right"><i class="fa fa-save"></i> Save item</button>
        </div>
      </div>
    </form>
  </div>
</div>
<?php if ($hasHtml): ?><?= $this->include('admin/cms/blocks/_ckeditor') ?><?php endif; ?>
