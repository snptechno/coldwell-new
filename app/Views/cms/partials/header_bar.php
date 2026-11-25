<?php
/**
 * Site header for pages whose first block is not a hero: the same nav partial on a
 * navy-deep rounded bar (hero card radius and inset), so the white nav reads the same.
 *
 * @var string $page_active
 * @var string $current_url
 */
?>
<div class="cb-hbar">
  <div class="cb-hbar__in">
    <?= view('cms/partials/nav', ['page_active' => $page_active ?? '', 'current_url' => $current_url ?? ''], ['saveData' => false]) ?>
  </div>
</div>
