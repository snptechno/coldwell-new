<?php
/**
 * @var array<string, mixed>                       $page_info
 * @var array<string, mixed>                       $block
 * @var array<string, mixed>                       $def        BlockTypes::TYPES entry
 * @var array<string, string>                      $settings   saved settings with defaults
 * @var array|null                                 $input      posted values on a failed save
 * @var array<string, list<array<string, mixed>>>  $items      group => rows
 * @var array<string, string>                      $form_errors
 */
use App\Libraries\Cms\CmsUrl;

$pageId  = (int) $page_info['id'];
$blockId = (int) $block['id'];
$base    = site_url('admin/cms/pages/' . $pageId . '/blocks/' . $blockId);
$posted  = is_array($input) && is_array($input['settings'] ?? null) ? $input['settings'] : null;
$label   = is_array($input) ? (string) ($input['label'] ?? '') : (string) $block['label'];
$status  = is_array($input) ? (string) ($input['status'] ?? '0') : (string) $block['status'];
$hasHtml = false;
?>
<style>
  .cms-img-preview { max-width: 220px; max-height: 140px; display: block; margin: 6px 0; border: 1px solid #ddd; }
  .cms-thumb { width: 60px; height: 40px; object-fit: cover; }
  .order-input { width: 80px; }
</style>
<div class="row">
  <div class="col-md-12">
    <?= $this->include('admin/blog/_flash') ?>
    <?php if ($form_errors !== []): ?>
      <div class="alert alert-danger">Please fix the highlighted fields below.</div>
    <?php endif; ?>
    <ol class="breadcrumb" style="background:none;padding-left:0">
      <li><a href="<?= esc(site_url('admin/cms/pages' . ($page_info['type'] === 'article' ? '?type=article' : '')), 'attr') ?>"><?= $page_info['type'] === 'article' ? 'Articles' : 'Pages' ?></a></li>
      <li><a href="<?= esc(site_url('admin/cms/pages/edit/' . $pageId) . '#blocks', 'attr') ?>"><?= esc($page_info['title']) ?></a></li>
      <li class="active"><?= esc($def['label']) ?></li>
    </ol>
  </div>

  <div class="col-md-8">
    <form autocomplete="off" enctype="multipart/form-data" id="cms-block-form" action="" method="post">
      <?= csrf_field() ?>
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title"><?= esc($form_title) ?></h3>
          <?php if (! empty($def['help'])): ?><p class="text-muted small" style="margin:4px 0 0"><?= esc($def['help']) ?></p><?php endif; ?>
        </div>
        <div class="box-body">
          <div class="form-group<?= isset($form_errors['label']) ? ' has-error' : '' ?>">
            <label for="label">Admin label</label>
            <input type="text" class="form-control" id="label" name="label" maxlength="120" value="<?= esc($label, 'attr') ?>">
            <span class="help-block">Only shown in the admin.</span>
            <?php if (isset($form_errors['label'])): ?><span class="help-block text-red"><?= esc($form_errors['label']) ?></span><?php endif; ?>
          </div>
          <?php foreach ($def['settings'] as $key => $field):
              $isImage = ($field['type'] ?? '') === 'image';
              $hasHtml = $hasHtml || ($field['type'] ?? '') === 'html';
              $value   = ! $isImage && $posted !== null ? (is_scalar($posted[$key] ?? null) ? (string) $posted[$key] : '') : (string) ($settings[$key] ?? '');
          ?>
            <?= view('admin/cms/blocks/_field', [
                'key'   => $key,
                'name'  => 'settings[' . $key . ']',
                'field' => $field,
                'value' => $value,
                'error' => (string) ($form_errors[$isImage ? 'upload_' . $key : 'settings.' . $key] ?? ''),
            ]) ?>
          <?php endforeach; ?>
          <div class="form-group">
            <label for="status">Status</label>
            <select id="status" name="status" class="form-control" style="max-width:200px">
              <option value="1" <?= $status === '1' ? 'selected' : '' ?>>Visible</option>
              <option value="0" <?= $status !== '1' ? 'selected' : '' ?>>Hidden</option>
            </select>
          </div>
        </div>
        <div class="box-footer">
          <a href="<?= esc(site_url('admin/cms/pages/edit/' . $pageId) . '#blocks', 'attr') ?>" class="btn btn-default">Back to page</a>
          <button type="submit" class="btn btn-info pull-right"><i class="fa fa-save"></i> Save block</button>
        </div>
      </div>
    </form>
  </div>

  <div class="col-md-4">
    <div class="box box-default">
      <div class="box-header with-border"><h3 class="box-title">Page</h3></div>
      <div class="box-body">
        <p><strong><?= esc($page_info['title']) ?></strong></p>
        <?php $pUrl = (int) $page_info['is_home'] === 1 ? base_url('/') : site_url($page_info['slug']); ?>
        <a href="<?= esc($pUrl . '?preview=1', 'attr') ?>" target="_blank" rel="noopener"><i class="fa fa-eye"></i> Preview page</a>
      </div>
    </div>
  </div>
</div>

<?php foreach (($def['groups'] ?? []) as $gKey => $grp):
    $rows  = $items[$gKey] ?? [];
    $full  = count($rows) >= (int) $grp['max'];
    $gBase = $base . '/items/' . $gKey;
    $sortId = 'sort-' . preg_replace('/[^a-z0-9_-]/i', '-', $gKey);
?>
<div class="row" id="group-<?= esc($gKey, 'attr') ?>">
  <div class="col-md-12">
    <div class="box box-warning">
      <div class="box-header with-border">
        <h3 class="box-title"><?= esc($grp['label']) ?></h3>
        <span class="text-muted small">&nbsp; <?= count($rows) ?> / <?= (int) $grp['max'] ?><?= ! empty($grp['min']) ? ' &middot; at least ' . (int) $grp['min'] . ' visible' : '' ?></span>
        <?php if ($full): ?>
          <button type="button" class="btn btn-primary btn-sm pull-right" disabled title="Maximum reached"><i class="fa fa-plus"></i> Add</button>
        <?php else: ?>
          <a href="<?= esc($gBase . '/add', 'attr') ?>" class="btn btn-primary btn-sm pull-right"><i class="fa fa-plus"></i> Add</a>
        <?php endif; ?>
      </div>
      <div class="box-body">
        <form method="post" action="<?= esc($gBase . '/sort', 'attr') ?>" id="<?= esc($sortId, 'attr') ?>"><?= csrf_field() ?></form>
        <div class="table-responsive">
        <table class="table table-bordered table-striped">
          <thead><tr><th style="width:100px">Order</th><th style="width:70px">Image</th><th>Summary</th><th>Status</th><th style="width:110px">Action</th></tr></thead>
          <tbody>
          <?php if ($rows === []): ?>
            <tr><td colspan="5" class="text-center text-muted">No items yet.</td></tr>
          <?php endif; ?>
          <?php foreach ($rows as $it):
              $iid     = (int) $it['id'];
              $thumb   = CmsUrl::imageUrl($it['image'] ?? '');
              $summary = '';
              foreach (['title', 'kicker', 'subtitle', 'badge', 'image_alt', 'cta_label'] as $c) {
                  if (isset($grp['fields'][$c]) && trim((string) $it[$c]) !== '') {
                      $summary = (string) $it[$c];
                      break;
                  }
              }
          ?>
            <tr>
              <td><input type="number" class="form-control input-sm order-input" form="<?= esc($sortId, 'attr') ?>" name="order[<?= $iid ?>]" value="<?= (int) $it['sort_order'] ?>"></td>
              <td><?php if ($thumb !== ''): ?><img src="<?= esc($thumb, 'attr') ?>" alt="" class="cms-thumb"><?php endif; ?></td>
              <td><?= esc(mb_strimwidth(str_replace("\n", ' ', $summary !== '' ? $summary : '(item #' . $iid . ')'), 0, 90, '…')) ?></td>
              <td>
                <span class="label <?= (int) $it['status'] === 1 ? 'label-success' : 'label-default' ?>"><?= (int) $it['status'] === 1 ? 'Visible' : 'Hidden' ?></span>
                <form method="post" action="<?= esc($gBase . '/status/' . $iid . '/' . ((int) $it['status'] === 1 ? 0 : 1), 'attr') ?>" style="display:inline">
                  <?= csrf_field() ?>
                  <button type="submit" class="btn btn-link btn-xs"><?= (int) $it['status'] === 1 ? 'Hide' : 'Show' ?></button>
                </form>
              </td>
              <td>
                <a href="<?= esc($gBase . '/edit/' . $iid, 'attr') ?>" title="Edit"><i class="fa fa-edit"></i> Edit</a> &nbsp;
                <form method="post" action="<?= esc($gBase . '/delete/' . $iid, 'attr') ?>" style="display:inline" onsubmit="return confirm('Delete this item? This cannot be undone.');">
                  <?= csrf_field() ?>
                  <button type="submit" class="btn btn-link btn-xs text-red" title="Delete" style="padding:0"><i class="fa fa-trash-o"></i></button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
        </div>
        <?php if ($rows !== []): ?>
          <button type="submit" form="<?= esc($sortId, 'attr') ?>" class="btn btn-default btn-sm"><i class="fa fa-sort-numeric-asc"></i> Save order</button>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<?php endforeach; ?>

<?php if ($hasHtml): ?><?= $this->include('admin/cms/blocks/_ckeditor') ?><?php endif; ?>
