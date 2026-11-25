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
<script>
(function () {
    const tokenForm = document.createElement('form');
    tokenForm.hidden = true;
    tokenForm.innerHTML = <?= json_encode(csrf_field(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
    document.body.appendChild(tokenForm);

    window.adminPost = function (url) {
        if (!url || !url.startsWith(<?= json_encode(rtrim(site_url(), '/') . '/') ?>)) return;
        const form = document.createElement('form');
        form.method = 'post';
        form.action = url;
        form.appendChild(tokenForm.querySelector('input').cloneNode(true));
        document.body.appendChild(form);
        form.submit();
    };
}());
</script>

    <?= view('admin/components/footer') ?>
