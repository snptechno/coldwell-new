<?php
/**
 * @var array|null                  $page_info
 * @var array|null                  $input       posted values on a failed save
 * @var string                      $page_type   page | article
 * @var bool                        $is_home
 * @var array<string, string>       $types
 * @var list<array<string, mixed>>  $blocks
 * @var array<string, string>       $form_errors
 */
use App\Libraries\Cms\BlockTypes;
use App\Libraries\Cms\CmsUrl;

$p   = $page_info ?? [];
$in  = $input;
$val = static function (string $k, $default = '') use ($p, $in) {
    if (is_array($in)) {
        $v = $in[$k] ?? $default;

        return is_scalar($v) ? (string) $v : (string) $default;
    }

    return (string) ($p[$k] ?? $default);
};
$err = static fn (string $k): string => ($form_errors[$k] ?? '') !== '' ? esc($form_errors[$k]) : '';
$pub = $val('published_at', '');
$pubLocal = is_array($in) ? $pub : ($pub !== '' ? date('Y-m-d\TH:i', strtotime($pub)) : date('Y-m-d\TH:i'));
$type   = is_array($in) && ! $is_home ? ((string) ($in['type'] ?? $page_type) === 'article' ? 'article' : 'page') : $page_type;
$pageId = (int) ($p['id'] ?? 0);
$url    = $p === [] ? '' : ($is_home ? base_url('/') : site_url($p['slug']));
$live   = $p !== [] && (int) $p['status'] === 1 && (empty($p['published_at']) || strtotime((string) $p['published_at']) <= time());
$hasHero = false;
foreach ($blocks as $b) {
    $hasHero = $hasHero || BlockTypes::isHero((string) $b['type']);
}
?>
<style>
  .cms-form .help-block { margin-bottom: 0; }
  .cms-img-preview { max-width: 220px; max-height: 140px; display: block; margin: 6px 0; border: 1px solid #ddd; }
  .slug-prefix { background: #f4f4f4; }
  #cms-blocks .order-input { width: 80px; }
</style>
<div class="row">
  <div class="col-md-12">
    <?= $this->include('admin/blog/_flash') ?>
    <?php if ($form_errors !== []): ?>
      <div class="alert alert-danger">Please fix the highlighted fields below.</div>
    <?php endif; ?>
  </div>
  <form class="cms-form" autocomplete="off" enctype="multipart/form-data" id="cms-page-form" action="" method="post">
    <?= csrf_field() ?>
    <div class="col-md-8">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title"><?= esc($form_title) ?></h3>
          <?php if ($is_home): ?> <span class="label label-primary"><i class="fa fa-home"></i> Home page</span><?php endif; ?>
          <?php if ($p !== []): ?>
            <span class="pull-right">
              <?php if ($live): ?><a href="<?= esc($url, 'attr') ?>" target="_blank" rel="noopener"><i class="fa fa-external-link"></i> View</a> &nbsp;<?php endif; ?>
              <a href="<?= esc($url . '?preview=1', 'attr') ?>" target="_blank" rel="noopener"><i class="fa fa-eye"></i> Preview</a>
            </span>
          <?php endif; ?>
        </div>
        <div class="box-body">
          <div class="form-group<?= $err('title') !== '' ? ' has-error' : '' ?>">
            <label for="title">Title <span class="text-red">*</span></label>
            <input type="text" class="form-control input-lg" id="title" name="title" maxlength="255" value="<?= esc($val('title'), 'attr') ?>" required>
            <span class="help-block text-red"><?= $err('title') ?></span>
          </div>
          <div class="form-group<?= $err('slug') !== '' ? ' has-error' : '' ?>">
            <label for="slug">URL</label>
            <div class="input-group">
              <span class="input-group-addon slug-prefix"><?= esc(rtrim(site_url('/'), '/')) ?>/</span>
              <?php if ($is_home): ?>
                <input type="text" class="form-control" id="slug" value="(home page: /)" disabled>
              <?php else: ?>
                <input type="text" class="form-control" id="slug" name="slug" maxlength="190" value="<?= esc($val('slug'), 'attr') ?>" placeholder="generated-from-the-title">
              <?php endif; ?>
            </div>
            <span class="help-block"><?= $is_home ? 'The home page always lives at the site root.' : 'Lowercase words separated by dashes. Leave empty to generate it from the title.' . ($p !== [] ? ' Changing it breaks existing links to this page.' : '') ?></span>
            <span class="help-block text-red"><?= $err('slug') ?></span>
          </div>
          <div class="form-group<?= $err('body') !== '' ? ' has-error' : '' ?>">
            <label for="body"><?= $type === 'article' ? 'Article text <span class="text-red">*</span>' : 'Body text (optional)' ?></label>
            <textarea id="body" name="body" rows="14" class="form-control cms-html"><?= esc($val('body')) ?></textarea>
            <?php if ($type !== 'article'): ?><span class="help-block">Pages are built from blocks below; this text is only used by articles.</span><?php endif; ?>
            <span class="help-block text-red"><?= $err('body') ?></span>
          </div>
          <div class="form-group">
            <label for="excerpt">Excerpt</label>
            <textarea id="excerpt" name="excerpt" rows="3" class="form-control" placeholder="Short summary for listings and the default meta description."><?= esc($val('excerpt')) ?></textarea>
          </div>
        </div>
      </div>

      <div class="box box-info">
        <div class="box-header with-border"><h3 class="box-title"><i class="fa fa-search"></i> SEO</h3></div>
        <div class="box-body">
          <div class="form-group<?= $err('meta_title') !== '' ? ' has-error' : '' ?>">
            <label for="meta_title">Meta title</label>
            <input type="text" class="form-control" id="meta_title" name="meta_title" maxlength="255" value="<?= esc($val('meta_title'), 'attr') ?>" placeholder="Defaults to the title">
            <span class="help-block">Aim for 50-60 characters.</span>
            <span class="help-block text-red"><?= $err('meta_title') ?></span>
          </div>
          <div class="form-group<?= $err('meta_description') !== '' ? ' has-error' : '' ?>">
            <label for="meta_description">Meta description</label>
            <textarea class="form-control" id="meta_description" name="meta_description" rows="3" maxlength="320" placeholder="Defaults to the excerpt"><?= esc($val('meta_description')) ?></textarea>
            <span class="help-block">Aim for 120-160 characters.</span>
            <span class="help-block text-red"><?= $err('meta_description') ?></span>
          </div>
          <div class="form-group<?= $err('meta_keywords') !== '' ? ' has-error' : '' ?>">
            <label for="meta_keywords">Meta keywords</label>
            <input type="text" class="form-control" id="meta_keywords" name="meta_keywords" maxlength="255" value="<?= esc($val('meta_keywords'), 'attr') ?>" placeholder="comma, separated, keywords">
            <span class="help-block text-red"><?= $err('meta_keywords') ?></span>
          </div>
          <div class="checkbox">
            <label><input type="hidden" name="noindex" value="0"><input type="checkbox" name="noindex" value="1" <?= $val('noindex', '0') === '1' ? 'checked' : '' ?>> Hide from search engines (noindex)</label>
          </div>
          <div class="form-group<?= $err('og_image') !== '' ? ' has-error' : '' ?>">
            <label for="og_image">Social share image (Open Graph)</label>
            <?php if (($src = CmsUrl::imageUrl($p['og_image'] ?? '')) !== ''): ?>
              <img src="<?= esc($src, 'attr') ?>" class="cms-img-preview" alt="">
              <label class="checkbox-inline"><input type="checkbox" name="remove_og_image" value="1"> Remove</label>
            <?php endif; ?>
            <input type="file" id="og_image" name="og_image" accept="image/jpeg,image/png,image/webp">
            <span class="help-block">Optional, 1200 x 630 px recommended. Defaults to the featured image.</span>
            <span class="help-block text-red"><?= $err('og_image') ?></span>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="box box-success">
        <div class="box-header with-border"><h3 class="box-title">Publish</h3></div>
        <div class="box-body">
          <div class="form-group">
            <label for="type">Type</label>
            <?php if ($is_home): ?>
              <input type="hidden" name="type" value="page">
              <select id="type" class="form-control" disabled><option>Page</option></select>
            <?php else: ?>
              <select id="type" name="type" class="form-control">
                <?php foreach ($types as $k => $v): ?>
                  <option value="<?= esc($k, 'attr') ?>" <?= $type === $k ? 'selected' : '' ?>><?= esc($v) ?></option>
                <?php endforeach; ?>
              </select>
            <?php endif; ?>
          </div>
          <div class="form-group<?= $err('status') !== '' ? ' has-error' : '' ?>">
            <label for="status">Status</label>
            <?php if ($is_home): ?>
              <input type="hidden" name="status" value="1">
              <select id="status" class="form-control" disabled><option>Published</option></select>
              <span class="help-block">The home page is always published.</span>
            <?php else: ?>
              <select id="status" name="status" class="form-control">
                <option value="0" <?= $val('status', '0') === '0' ? 'selected' : '' ?>>Draft</option>
                <option value="1" <?= $val('status', '0') === '1' ? 'selected' : '' ?>>Published</option>
              </select>
            <?php endif; ?>
            <span class="help-block text-red"><?= $err('status') ?></span>
          </div>
          <div class="form-group<?= $err('published_at') !== '' ? ' has-error' : '' ?>">
            <label for="published_at">Publish date</label>
            <input type="datetime-local" id="published_at" name="published_at" class="form-control" value="<?= esc($pubLocal, 'attr') ?>">
            <span class="help-block"><?= $is_home ? 'Must not be in the future.' : 'A future date schedules the page.' ?></span>
            <span class="help-block text-red"><?= $err('published_at') ?></span>
          </div>
          <div class="form-group<?= $err('nav_key') !== '' ? ' has-error' : '' ?>">
            <label for="nav_key">Menu highlight key</label>
            <input type="text" id="nav_key" name="nav_key" class="form-control" maxlength="60" value="<?= esc($val('nav_key'), 'attr') ?>" placeholder="e.g. about">
            <span class="help-block">Menu items whose active keys contain this value are highlighted on this page.</span>
            <span class="help-block text-red"><?= $err('nav_key') ?></span>
          </div>
          <?php if ($p !== []): ?>
            <p class="text-muted small">Created <?= esc($p['created_at'] ?? '-') ?> &middot; Updated <?= esc($p['updated_at'] ?? '-') ?></p>
          <?php endif; ?>
        </div>
        <div class="box-footer">
          <a href="<?= esc(site_url('admin/cms/pages' . ($type === 'article' ? '?type=article' : '')), 'attr') ?>" class="btn btn-default">Back</a>
          <button type="submit" class="btn btn-info pull-right"><i class="fa fa-save"></i> Save</button>
        </div>
      </div>

      <div class="box box-default">
        <div class="box-header with-border"><h3 class="box-title">Featured image</h3></div>
        <div class="box-body">
          <?php if (($src = CmsUrl::imageUrl($p['featured_image'] ?? '')) !== ''): ?>
            <img src="<?= esc($src, 'attr') ?>" class="cms-img-preview" alt="">
            <label class="checkbox-inline"><input type="checkbox" name="remove_featured_image" value="1"> Remove</label>
          <?php endif; ?>
          <input type="file" id="featured_image" name="featured_image" accept="image/jpeg,image/png,image/webp">
          <span class="help-block">JPG, PNG or WEBP, under 5 MB.</span>
          <span class="help-block text-red"><?= $err('featured_image') ?></span>
          <div class="form-group<?= $err('image_alt') !== '' ? ' has-error' : '' ?>" style="margin-top:10px">
            <label for="image_alt">Alt text</label>
            <input type="text" id="image_alt" name="image_alt" class="form-control" maxlength="255" value="<?= esc($val('image_alt'), 'attr') ?>" placeholder="Describe the image">
            <span class="help-block text-red"><?= $err('image_alt') ?></span>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>

<?php if ($p !== []): ?>
<div class="row" id="blocks">
  <div class="col-md-12">
    <div class="box box-warning">
      <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-th-list"></i> Blocks</h3>
        <span class="text-muted small">&nbsp; Shown top to bottom by order number. A hero block must be first.</span>
      </div>
      <div class="box-body">
        <form method="post" action="<?= esc(site_url('admin/cms/pages/' . $pageId . '/blocks/sort'), 'attr') ?>" id="block-sort-form"><?= csrf_field() ?></form>
        <div class="table-responsive">
        <table class="table table-bordered table-striped" id="cms-blocks">
          <thead>
            <tr><th style="width:100px">Order</th><th>Block</th><th>Admin label</th><th>Status</th><th style="width:120px">Action</th></tr>
          </thead>
          <tbody>
          <?php if ($blocks === []): ?>
            <tr><td colspan="5" class="text-center text-muted">No blocks yet. Add one below.</td></tr>
          <?php endif; ?>
          <?php foreach ($blocks as $b):
              $bid   = (int) $b['id'];
              $known = BlockTypes::has((string) $b['type']);
              $edit  = site_url('admin/cms/pages/' . $pageId . '/blocks/' . $bid);
          ?>
            <tr>
              <td><input type="number" class="form-control input-sm order-input" form="block-sort-form" name="order[<?= $bid ?>]" value="<?= (int) $b['sort_order'] ?>"></td>
              <td>
                <?= esc($known ? BlockTypes::get($b['type'])['label'] : 'Unknown type: ' . $b['type']) ?>
                <?php if ($known && BlockTypes::isHero($b['type'])): ?><span class="label label-primary">hero</span><?php endif; ?>
              </td>
              <td><?= esc((string) $b['label']) ?></td>
              <td>
                <span class="label <?= (int) $b['status'] === 1 ? 'label-success' : 'label-default' ?>"><?= (int) $b['status'] === 1 ? 'Visible' : 'Hidden' ?></span>
                <form method="post" action="<?= esc($edit . '/status/' . ((int) $b['status'] === 1 ? 0 : 1), 'attr') ?>" style="display:inline">
                  <?= csrf_field() ?>
                  <button type="submit" class="btn btn-link btn-xs"><?= (int) $b['status'] === 1 ? 'Hide' : 'Show' ?></button>
                </form>
              </td>
              <td>
                <?php if ($known): ?><a href="<?= esc($edit, 'attr') ?>" title="Edit"><i class="fa fa-edit"></i> Edit</a> &nbsp;<?php endif; ?>
                <form method="post" action="<?= esc($edit . '/delete', 'attr') ?>" style="display:inline" onsubmit="return confirm('Delete this block and all its items? This cannot be undone.');">
                  <?= csrf_field() ?>
                  <button type="submit" class="btn btn-link btn-xs text-red" title="Delete" style="padding:0"><i class="fa fa-trash-o"></i></button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
        </div>
        <?php if ($blocks !== []): ?>
          <button type="submit" form="block-sort-form" class="btn btn-default btn-sm"><i class="fa fa-sort-numeric-asc"></i> Save order</button>
        <?php endif; ?>
      </div>
      <div class="box-footer">
        <form method="post" action="<?= esc(site_url('admin/cms/pages/' . $pageId . '/blocks/add'), 'attr') ?>" class="form-inline">
          <?= csrf_field() ?>
          <label for="new-block-type">Add block</label>
          <select id="new-block-type" name="type" class="form-control" required>
            <option value="">- Choose a block type -</option>
            <?php foreach (BlockTypes::TYPES as $k => $t): $heroBlocked = ! empty($t['hero']) && $hasHero; ?>
              <option value="<?= esc($k, 'attr') ?>" <?= $heroBlocked ? 'disabled' : '' ?> title="<?= esc($t['help'] ?? '', 'attr') ?>"><?= esc($t['label']) ?><?= ! empty($t['hero']) ? ' (first block only)' : '' ?></option>
            <?php endforeach; ?>
          </select>
          <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i> Add</button>
        </form>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

<?= $this->include('admin/cms/blocks/_ckeditor') ?>
