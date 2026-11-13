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
			  <h3>Refund Policy</h3>
				<p style="line-height: 27px;font-size: 15px;text-align: left;padding: 0 50px; width: 100%;display:none">
				
				All sales are final and there will be no exchange or refund that will be permitted. Please ensure that you

are wholly responsible for the invoice number that you paid for and all the charges that result from

those purchases. Coldwell Banker India and its services are not responsible at all for any payment or any

incorrect invoice number. However, in a case where the transaction has been completed by you online,

and the money has been deducted from your card or bank account but a payment has not been

reflected in your account within 24 hours of your transaction completion. Then, you may contact us by

sending us an email on info@coldwellbanker.in or send us a message on the Contact Us page. In such a

condition, you will be entitled to a full refund. You are requested to include the following details in your

email- the Company Name, Invoice Number, and Transaction Date. We will investigate the incident and

refund your money within 30 working days from the receipt date of your email if found that money was

charged to your account or card without reflecting in your account.
				</p>
			</div>
		</section>
       
        <div class="clear"></div>

		<?= $this->include('partials/sub_footer') ?>
    </article>
    <?= $this->include('partials/footer') ?>

</body>
</html>