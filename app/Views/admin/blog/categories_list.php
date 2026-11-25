<div class="row">
  <div class="col-xs-12">
    <?= $this->include('admin/blog/_flash') ?>
    <div class="box">
      <div class="box-header"><h3 class="box-title"><?= esc($form_title) ?></h3></div>
      <a href="<?= base_url('admin/blog/categories/add') ?>" class="btn bg-purple btn-flat margin"><i class="fa fa-plus"></i> Add Category</a>
      <a href="<?= base_url('admin/blog/posts') ?>" class="btn btn-default btn-flat margin">Posts</a>
      <div class="box-body">
        <table id="example1" class="table table-bordered table-striped">
          <thead>
            <tr><th>#</th><th>Name</th><th>URL</th><th>Posts</th><th>Order</th><th>Status</th><th>Action</th></tr>
          </thead>
          <tbody>
          <?php $i = 1; foreach ($info as $row): ?>
            <tr>
              <td><?= $i++ ?></td>
              <td><a href="<?= base_url('admin/blog/categories/edit/' . (int) $row['id']) ?>"><?= esc($row['name']) ?></a></td>
              <td><small>/blog/category/<?= esc($row['slug']) ?></small></td>
              <td><a href="<?= base_url('admin/blog/posts?category=' . (int) $row['id']) ?>"><?= (int) $row['post_count'] ?></a></td>
              <td><?= (int) $row['sort_order'] ?></td>
              <td>
                <form method="post" action="<?= base_url('admin/blog/categories/status/' . (int) $row['id'] . '/' . ((int) $row['status'] === 1 ? 0 : 1)) ?>" style="display:inline">
                  <?= csrf_field() ?>
                  <span class="label <?= (int) $row['status'] === 1 ? 'label-success' : 'label-default' ?>"><?= (int) $row['status'] === 1 ? 'Active' : 'Hidden' ?></span>
                  <button type="submit" class="btn btn-link btn-xs"><?= (int) $row['status'] === 1 ? 'Disable' : 'Enable' ?></button>
                </form>
              </td>
              <td>
                <a href="<?= base_url('admin/blog/categories/edit/' . (int) $row['id']) ?>"><i class="fa fa-edit"></i></a>&nbsp;&nbsp;
                <form method="post" action="<?= base_url('admin/blog/categories/delete/' . (int) $row['id']) ?>" style="display:inline" onsubmit="return confirm('Delete this category? Its posts stay, without a category.');">
                  <?= csrf_field() ?>
                  <button type="submit" class="btn btn-link btn-xs text-red" style="padding:0"><i class="fa fa-trash-o"></i></button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
