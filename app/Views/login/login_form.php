<div class="login-box">
  <div class="login-logo">
    <a href="<?php echo base_url() ?>admin/login"><b>Admin</b>Coldwell Banker</a>
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
    <p class="login-box-msg">Sign in to start your session</p>
	<?php
	$error = session()->getFlashdata('error');
	if (!empty($error)) {
		?>
		<div class="alert alert-danger"><?php echo esc($error); ?></div>
	<?php } ?>
    <form action="<?php echo base_url() ?>admin" method="post">
      <?= csrf_field() ?>
      <div class="form-group has-feedback">
        <input type="text" name="user_name" required="true" class="form-control" placeholder="Username">
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <input type="password" name="password" required="true" class="form-control" placeholder="Password">
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      <div class="row">
        <div class="col-xs-4">
          <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
        </div>
        <!-- /.col -->
      </div>
    </form>

  </div>
  <!-- /.login-box-body -->
</div>
