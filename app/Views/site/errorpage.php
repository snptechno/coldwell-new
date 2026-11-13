<!doctype html>
<html>
<head>
    <title>404 error page</title>
	<?= $this->include('partials/header') ?>
	<?= $this->include('partials/ga_code_header') ?>
</head>
<body>
    <article class="learnings aos-all">
        <section>
            <div class="container">
                <?= $this->include('partials/navigation') ?>
            </div>
        </section>
        <section class="inner_banner_container">
            <div class="">
                <div class="slide-txt">
                    <div class="fr-main-left algn-left">
                        <h1 style="text-transform: inherit;"><strong>OOPS!</strong> something went wrong!</h1>
                        <span class="txt-para">We can't find the page you are looking for.</strong></span>
                    </div>
                    <div class="fr-main-right rese-rght">
                        <img src="<?php echo base_url(); ?>assets/images/error_404.png" alt="" class="no-margin">
                    </div>
                </div>
            </div>
        </section>


        <div class="clear"></div>

        <?= $this->include('partials/sub_footer') ?>
    </article>
    <?= $this->include('partials/footer') ?>

</body>
</html>
