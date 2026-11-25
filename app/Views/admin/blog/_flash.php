<?php if ($msg = session()->getFlashdata('success_msg')): ?>
<div class="alert alert-success alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button><?= esc($msg) ?></div>
<?php endif; ?>
<?php if ($msg = session()->getFlashdata('error_msg')): ?>
<div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button><?= esc($msg) ?></div>
<?php endif; ?>
