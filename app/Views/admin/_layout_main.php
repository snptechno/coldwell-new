<?= view('admin/components/header', ['title' => $title ?? 'Coldwell || Banker']) ?>

<body class="hold-transition skin-blue sidebar-mini">
	<div class="wrapper">
        <?= view('admin/components/user_profile') ?>

        <?= view('admin/components/navigation') ?>
        <!-- Right side column. Contains the navbar and content of the page -->

        <div class="content-wrapper">
            <section class="content-header">
			  <h1>
				Dashboard
				<small>Control panel</small>
			  </h1>
			  <ol class="breadcrumb">
				<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
				<li class="active">Dashboard</li>
			  </ol>
			</section>
            <section class="content">
                <?= $subview ?? '' ?>
            </section>


        </div><!-- /.right-side -->
        <div class="control-sidebar-bg"></div>


        <footer class="main-footer">

			<strong>Copyright &copy; 2016-2017  All rights
			reserved.
		</footer>

    </div><!-- ./wrapper -->
    <?= view('admin/components/footer') ?>
