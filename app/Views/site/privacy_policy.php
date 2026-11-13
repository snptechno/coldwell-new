<!doctype html>
<html>

<head>
    <title><?= esc($page_info->title) ?></title>
	<meta name="description" content="<?= esc($page_info->meta_description, 'attr') ?>" />
	<meta name="keywords" content="<?= esc($page_info->meta_keywords, 'attr') ?>" />
	<?= $this->include('partials/header') ?>
	<?= $this->include('partials/ga_code_header') ?>
</head>

<body>
    <article class="aos-all">
        <section>
            <div class="container">
                <?php $this->setVar('act', $page_active); // CI3: $data1['act'] for the navigation partial ?><?= $this->include('partials/navigation') ?>
            </div>
        </section>
        <section class="inner_banner_container">
            <div class="">
                <div class="slide-txt">
                    <div class="fr-main-left algn-left">
                        
                    </div>
                   
                </div>
            </div>
        </section>
		<section class="videos-sec aos-item" data-aos="fade-up" id="videos">
            <div class="container">
			<h3>Privacy Policy</h3>
				<ol style="line-height: 27px;font-size: 15px;color: #262626;text-align: left;padding: 0 50px;">
				
				<li>Coldwell Banker India shall take reasonable security practice/s to protect the privacy of the

				information provided by the Applicant/s.</li>

				<li>Coldwell Banker India shall not disclose any such information of the applicant to any third party.</li>

				<li>To send important notices/communications regarding the status of their Application, relevant

				policies, terms, conditions, etc. of Coldwell Banker India as applicable.</li>

				<li>This disclosure is required by law or requested by any statutory or regulatory or judicial authority or

				recognized self-regulating organization having jurisdiction over the Parties.</li>

				<li>This disclosure is required in connection with any lawsuit.</li>
				</ol>
			</div>
		</section>
       
        <div class="clear"></div>

		<?= $this->include('partials/sub_footer') ?>

    </article>
    <?= $this->include('partials/footer') ?>

</body>
</html>