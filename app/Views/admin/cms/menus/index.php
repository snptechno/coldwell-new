<?php
/**
 * Admin > CMS > Menus: one row per SiteSchema::MENUS location.
 *
 * @var array<string, array<string, mixed>> $menus
 */
?>
<div class="row">
  <div class="col-xs-12">
    <?= $this->include('admin/blog/_flash') ?>
    <div class="box">
      <div class="box-header"><h3 class="box-title"><?= esc($form_title) ?></h3></div>
      <div class="box-body">
        <p class="text-muted">Every menu of the site layout. Open a menu to add, reorder, hide or remove its links.</p>
        <table class="table table-bordered table-striped">
          <thead>
            <tr><th>Menu</th><th>Where it shows</th><th>Links</th><th>Limit</th><th>Action</th></tr>
          </thead>
          <tbody>
          <?php foreach ($menus as $location => $m): ?>
            <tr>
              <td>
                <a href="<?= esc(site_url('admin/cms/menus/' . $location), 'attr') ?>"><strong><?= esc($m['name']) ?></strong></a><br>
                <small class="text-muted"><?= esc($location) ?></small>
              </td>
              <td><?= esc($m['help']) ?><?php if ((int) $m['depth'] >= 2): ?> <span class="label label-info">sub-items allowed</span><?php endif; ?></td>
              <td><?= (int) $m['live'] ?> shown<?php if ($m['items'] > $m['live']): ?>, <?= (int) $m['items'] - (int) $m['live'] ?> hidden<?php endif; ?></td>
              <td><?= (int) $m['items'] ?> / <?= (int) $m['max'] ?></td>
              <td><a class="btn btn-default btn-xs" href="<?= esc(site_url('admin/cms/menus/' . $location), 'attr') ?>"><i class="fa fa-list"></i> Manage links</a></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
