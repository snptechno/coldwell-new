<?php
/**
 * @var list<array<string, mixed>> $info
 * @var array<int, int>            $counts  page id => block count
 * @var \CodeIgniter\Pager\Pager   $pager
 * @var array<string, string>      $filters
 * @var array<string, string>      $types
 */
$now     = date('Y-m-d H:i:s');
$pageUrl = static fn (array $r): string => (int) $r['is_home'] === 1 ? base_url('/') : site_url($r['slug']);
?>
<div class="row">
  <div class="col-xs-12">
    <?= $this->include('admin/blog/_flash') ?>
    <div class="box">
      <div class="box-header">
        <h3 class="box-title"><?= esc($form_title) ?></h3>
      </div>

      <a href="<?= esc(site_url('admin/cms/pages/add'), 'attr') ?>" class="btn bg-purple btn-flat margin"><i class="fa fa-plus"></i> Add page</a>
      <a href="<?= esc(site_url('admin/cms/pages/add') . '?type=article', 'attr') ?>" class="btn bg-olive btn-flat margin"><i class="fa fa-plus"></i> Add article</a>

      <div class="box-body">
        <form method="get" action="<?= esc(site_url('admin/cms/pages'), 'attr') ?>" class="form-inline" style="margin-bottom:15px">
          <input type="search" name="q" value="<?= esc($filters['q'], 'attr') ?>" class="form-control" placeholder="Search title or slug" style="min-width:240px">
          <select name="type" class="form-control">
            <option value="">Pages &amp; articles</option>
            <?php foreach ($types as $k => $v): ?>
              <option value="<?= esc($k, 'attr') ?>" <?= $filters['type'] === $k ? 'selected' : '' ?>><?= esc($v) ?>s</option>
            <?php endforeach; ?>
          </select>
          <select name="status" class="form-control">
            <option value="">Any status</option>
            <?php foreach (['published' => 'Published', 'scheduled' => 'Scheduled', 'draft' => 'Draft'] as $k => $v): ?>
              <option value="<?= esc($k, 'attr') ?>" <?= $filters['status'] === $k ? 'selected' : '' ?>><?= esc($v) ?></option>
            <?php endforeach; ?>
          </select>
          <button type="submit" class="btn btn-info"><i class="fa fa-search"></i> Filter</button>
          <?php if (array_filter($filters, static fn ($v) => $v !== '')): ?>
            <a href="<?= esc(site_url('admin/cms/pages'), 'attr') ?>" class="btn btn-link">Clear</a>
          <?php endif; ?>
        </form>

        <div class="table-responsive">
        <table class="table table-bordered table-striped" id="cms-pages">
          <thead>
            <tr>
              <th>Title / URL</th>
              <th>Type</th>
              <th>Status</th>
              <th>Publish date</th>
              <th>Blocks</th>
              <th style="width:150px">Action</th>
            </tr>
          </thead>
          <tbody>
          <?php if ($info === []): ?>
            <tr><td colspan="6" class="text-center text-muted">Nothing found.</td></tr>
          <?php endif; ?>
          <?php foreach ($info as $row):
              $id    = (int) $row['id'];
              $home  = (int) $row['is_home'] === 1;
              $live  = (int) $row['status'] === 1 && ($row['published_at'] === null || $row['published_at'] <= $now);
              $label = (int) $row['status'] === 0 ? ['Draft', 'label-default'] : ($live ? ['Published', 'label-success'] : ['Scheduled', 'label-warning']);
              $url   = $pageUrl($row);
          ?>
            <tr>
              <td>
                <a href="<?= esc(site_url('admin/cms/pages/edit/' . $id), 'attr') ?>"><strong><?= esc($row['title']) ?></strong></a>
                <?php if ($home): ?><span class="label label-primary"><i class="fa fa-home"></i> Home</span><?php endif; ?>
                <?php if ((int) $row['noindex'] === 1): ?><span class="label label-danger">noindex</span><?php endif; ?>
                <br><small class="text-muted"><?= esc($home ? '/' : '/' . $row['slug']) ?></small>
              </td>
              <td><?= esc($types[$row['type']] ?? $row['type']) ?></td>
              <td>
                <span class="label <?= esc($label[1], 'attr') ?>"><?= esc($label[0]) ?></span>
                <?php if (! $home): ?>
                <form method="post" action="<?= esc(site_url('admin/cms/pages/status/' . $id . '/' . ((int) $row['status'] === 1 ? 0 : 1)), 'attr') ?>" style="display:inline">
                  <?= csrf_field() ?>
                  <button type="submit" class="btn btn-link btn-xs"><?= (int) $row['status'] === 1 ? 'Unpublish' : 'Publish' ?></button>
                </form>
                <?php endif; ?>
              </td>
              <td><?= esc($row['published_at'] ? date('d M Y, H:i', strtotime((string) $row['published_at'])) : '-') ?></td>
              <td><?= (int) ($counts[$id] ?? 0) ?></td>
              <td>
                <a href="<?= esc(site_url('admin/cms/pages/edit/' . $id), 'attr') ?>" title="Edit"><i class="fa fa-edit"></i></a>
                &nbsp;&nbsp;
                <?php if ($live): ?>
                  <a href="<?= esc($url, 'attr') ?>" target="_blank" rel="noopener" title="View"><i class="fa fa-eye"></i></a>
                <?php else: ?>
                  <a href="<?= esc($url . '?preview=1', 'attr') ?>" target="_blank" rel="noopener" title="Preview"><i class="fa fa-eye-slash"></i></a>
                <?php endif; ?>
                &nbsp;&nbsp;
                <?php if (! $home): ?>
                <form method="post" action="<?= esc(site_url('admin/cms/pages/delete/' . $id), 'attr') ?>" style="display:inline" onsubmit="return confirm('Delete this page and all its blocks permanently? This cannot be undone.');">
                  <?= csrf_field() ?>
                  <button type="submit" class="btn btn-link btn-xs text-red" title="Delete" style="padding:0"><i class="fa fa-trash-o"></i></button>
                </form>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
        </div>
        <div class="text-muted">Total: <?= (int) $pager->getTotal() ?></div>
        <?= $pager->links() ?>
      </div>
    </div>
  </div>
</div>
