<?php
/** @var list<array<string, mixed>> $info @var \CodeIgniter\Pager\Pager $pager @var array $filters @var array<int,string> $categories */
$now = date('Y-m-d H:i:s');
?>
<div class="row">
  <div class="col-xs-12">
    <?= $this->include('admin/blog/_flash') ?>
    <div class="box">
      <div class="box-header">
        <h3 class="box-title"><?= esc($form_title) ?></h3>
      </div>

      <a href="<?= base_url('admin/blog/posts/add') ?>" class="btn bg-purple btn-flat margin"><i class="fa fa-plus"></i> Add Post</a>
      <a href="<?= base_url('admin/blog/categories') ?>" class="btn btn-default btn-flat margin">Categories</a>
      <a href="<?= base_url('blog') ?>" target="_blank" rel="noopener" class="btn btn-default btn-flat margin"><i class="fa fa-external-link"></i> View blog</a>

      <div class="box-body">
        <form method="get" action="<?= base_url('admin/blog/posts') ?>" class="form-inline" style="margin-bottom:15px">
          <input type="search" name="q" value="<?= esc($filters['q'], 'attr') ?>" class="form-control" placeholder="Search title or slug" style="min-width:240px">
          <select name="category" class="form-control">
            <option value="">All categories</option>
            <?php foreach ($categories as $cid => $cname): ?>
              <option value="<?= (int) $cid ?>" <?= (string) $filters['category'] === (string) $cid ? 'selected' : '' ?>><?= esc($cname) ?></option>
            <?php endforeach; ?>
          </select>
          <select name="status" class="form-control">
            <option value="">Any status</option>
            <?php foreach (['published' => 'Published', 'scheduled' => 'Scheduled', 'draft' => 'Draft'] as $k => $v): ?>
              <option value="<?= $k ?>" <?= $filters['status'] === $k ? 'selected' : '' ?>><?= $v ?></option>
            <?php endforeach; ?>
          </select>
          <button type="submit" class="btn btn-info"><i class="fa fa-search"></i> Filter</button>
          <?php if (array_filter($filters, static fn ($v) => $v !== '')): ?>
            <a href="<?= base_url('admin/blog/posts') ?>" class="btn btn-link">Clear</a>
          <?php endif; ?>
        </form>

        <table class="table table-bordered table-striped" id="blog-posts">
          <thead>
            <tr>
              <th style="width:70px">Image</th>
              <th>Title / URL</th>
              <th>Category</th>
              <th>Publish date</th>
              <th>Status</th>
              <th>Views</th>
              <th style="width:130px">Action</th>
            </tr>
          </thead>
          <tbody>
          <?php if ($info === []): ?>
            <tr><td colspan="7" class="text-center text-muted">No posts found.</td></tr>
          <?php endif; ?>
          <?php foreach ($info as $row):
              $live = (int) $row['status'] === 1 && $row['published_at'] <= $now;
              $label = (int) $row['status'] === 0 ? ['Draft', 'label-default'] : ($live ? ['Published', 'label-success'] : ['Scheduled', 'label-warning']);
          ?>
            <tr>
              <td><?php if ($row['featured_image']): ?><img src="<?= base_url('uploads/blog/' . rawurlencode($row['featured_image'])) ?>" alt="" style="width:60px;height:40px;object-fit:cover"><?php endif; ?></td>
              <td>
                <a href="<?= base_url('admin/blog/posts/edit/' . (int) $row['id']) ?>"><strong><?= esc($row['title']) ?></strong></a><br>
                <small class="text-muted">/blog/<?= esc($row['slug']) ?></small>
                <?php if ((int) $row['noindex'] === 1): ?><span class="label label-danger">noindex</span><?php endif; ?>
              </td>
              <td><?= esc($row['category_name'] ?? '-') ?></td>
              <td><?= esc($row['published_at'] ? date('d M Y, H:i', strtotime($row['published_at'])) : '-') ?></td>
              <td>
                <span class="label <?= $label[1] ?>"><?= $label[0] ?></span>
                <form method="post" action="<?= base_url('admin/blog/posts/status/' . (int) $row['id'] . '/' . ((int) $row['status'] === 1 ? 0 : 1)) ?>" style="display:inline">
                  <?= csrf_field() ?>
                  <button type="submit" class="btn btn-link btn-xs"><?= (int) $row['status'] === 1 ? 'Unpublish' : 'Publish' ?></button>
                </form>
              </td>
              <td><?= (int) $row['views'] ?></td>
              <td>
                <a href="<?= base_url('admin/blog/posts/edit/' . (int) $row['id']) ?>" title="Edit"><i class="fa fa-edit"></i></a>
                &nbsp;&nbsp;
                <?php if ($live): ?><a href="<?= base_url('blog/' . $row['slug']) ?>" target="_blank" rel="noopener" title="View"><i class="fa fa-eye"></i></a>&nbsp;&nbsp;<?php endif; ?>
                <form method="post" action="<?= base_url('admin/blog/posts/delete/' . (int) $row['id']) ?>" style="display:inline" onsubmit="return confirm('Delete this post permanently? This cannot be undone.');">
                  <?= csrf_field() ?>
                  <button type="submit" class="btn btn-link btn-xs text-red" title="Delete" style="padding:0"><i class="fa fa-trash-o"></i></button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
        <div class="text-muted">Total: <?= (int) $pager->getTotal() ?> posts</div>
        <?= $pager->links() ?>
      </div>
    </div>
  </div>
</div>
