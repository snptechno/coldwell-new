<?php
/**
 * Admin > CMS > Menus > {location}: the links as a tree (parents, then indented children).
 *
 * @var string                     $location
 * @var array<string, mixed>       $def
 * @var list<array<string, mixed>> $tree
 * @var int                        $count
 * @var array<int, array>          $pages
 */

use App\Libraries\Cms\CmsUrl;

$base   = 'admin/cms/menus/' . $location;
$full   = $count >= (int) $def['max'];
$target = static function (array $r) use ($pages): string {
    if ($r['page_id'] !== null) {
        $p = $pages[(int) $r['page_id']] ?? null;
        if ($p === null) {
            return '<span class="text-red">Missing page #' . (int) $r['page_id'] . ' (link is not shown)</span>';
        }
        $out = '<i class="fa fa-file-o"></i> ' . esc($p['title']) . ' <small class="text-muted">(' . esc($p['type']) . ')</small>';

        return (int) $p['status'] === 1 ? $out : $out . ' <span class="label label-warning">draft: link is not shown</span>';
    }
    $href = CmsUrl::href((string) $r['url']);
    if ($href === '') {
        return '<span class="text-red">Invalid link (not shown): ' . esc((string) $r['url']) . '</span>';
    }

    return '<i class="fa fa-link"></i> <a href="' . esc($href, 'attr') . '" target="_blank" rel="noopener noreferrer">' . esc((string) $r['url']) . '</a>';
};
?>
<div class="row">
  <div class="col-xs-12">
    <?= $this->include('admin/blog/_flash') ?>
    <div class="box">
      <div class="box-header with-border">
        <h3 class="box-title"><?= esc($form_title) ?></h3>
        <p class="text-muted" style="margin:6px 0 0"><?= esc($def['help']) ?> <?= (int) $count ?> of <?= (int) $def['max'] ?> links used.</p>
      </div>
      <a href="<?= esc(site_url('admin/cms/menus'), 'attr') ?>" class="btn btn-default btn-flat margin">All menus</a>
      <?php if ($full): ?>
      <span class="btn bg-purple btn-flat margin disabled" title="Limit reached"><i class="fa fa-plus"></i> Add link</span>
      <?php else: ?>
      <a href="<?= esc(site_url($base . '/add'), 'attr') ?>" class="btn bg-purple btn-flat margin"><i class="fa fa-plus"></i> Add link</a>
      <?php endif; ?>
      <div class="box-body">
        <form id="menu-sort" method="post" action="<?= esc(site_url($base . '/sort'), 'attr') ?>"><?= csrf_field() ?></form>
        <table class="table table-bordered">
          <thead>
            <tr><th>Order</th><th>Label</th><th>Links to</th><th>Highlight keys</th><th>Status</th><th>Action</th></tr>
          </thead>
          <tbody>
          <?php if ($tree === []): ?>
            <tr><td colspan="6" class="text-center text-muted">No links yet.</td></tr>
          <?php endif; ?>
          <?php foreach ($tree as $parent): ?>
            <?php foreach (array_merge([$parent], $parent['children']) as $r): ?>
            <?php
              $id       = (int) $r['id'];
              $child    = $r !== $parent;
              $active   = (int) $r['status'] === 1;
              $nKids    = $child ? 0 : count($parent['children']);
              $confirm  = $nKids > 0 ? 'Delete this link AND its ' . $nKids . ' sub-item(s)?' : 'Delete this link?';
            ?>
            <tr<?= $active ? '' : ' class="text-muted"' ?>>
              <td style="width:90px"><input type="number" class="form-control input-sm" name="order[<?= $id ?>]" value="<?= (int) $r['sort_order'] ?>" form="menu-sort" aria-label="Order of <?= esc($r['label'], 'attr') ?>"></td>
              <td<?= $child ? ' style="padding-left:36px"' : '' ?>>
                <?= $child ? '<i class="fa fa-level-up fa-rotate-90 text-muted"></i> ' : '' ?><strong><?= esc($r['label']) ?></strong>
                <?php if ((int) $r['new_tab'] === 1): ?> <span class="label label-default">new tab</span><?php endif; ?>
              </td>
              <td><?= $target($r) ?></td>
              <td><small><?= esc((string) $r['active_keys']) ?></small></td>
              <td>
                <form method="post" action="<?= esc(site_url($base . '/status/' . $id . '/' . ($active ? 0 : 1)), 'attr') ?>" style="display:inline">
                  <?= csrf_field() ?>
                  <span class="label <?= $active ? 'label-success' : 'label-default' ?>"><?= $active ? 'Shown' : 'Hidden' ?></span>
                  <button type="submit" class="btn btn-link btn-xs"><?= $active ? 'Hide' : 'Show' ?></button>
                </form>
              </td>
              <td style="white-space:nowrap">
                <a href="<?= esc(site_url($base . '/edit/' . $id), 'attr') ?>" title="Edit"><i class="fa fa-edit"></i></a>&nbsp;&nbsp;
                <?php if (! $child && (int) $def['depth'] >= 2 && ! $full): ?>
                <a href="<?= esc(site_url($base . '/add') . '?parent=' . $id, 'attr') ?>" title="Add a sub-item"><i class="fa fa-plus-square-o"></i></a>&nbsp;&nbsp;
                <?php endif; ?>
                <form method="post" action="<?= esc(site_url($base . '/delete/' . $id), 'attr') ?>" style="display:inline" onsubmit="return confirm(<?= esc(json_encode($confirm), 'attr') ?>);">
                  <?= csrf_field() ?>
                  <button type="submit" class="btn btn-link btn-xs text-red" style="padding:0" title="Delete"><i class="fa fa-trash-o"></i></button>
                </form>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php endforeach; ?>
          </tbody>
        </table>
        <?php if ($tree !== []): ?>
        <p class="help-block">Lower numbers come first. Sub-items are ordered within their parent.</p>
        <button type="submit" form="menu-sort" class="btn btn-info"><i class="fa fa-sort-amount-asc"></i> Save order</button>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
