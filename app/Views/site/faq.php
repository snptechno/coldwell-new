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
		 <section class="faq-sec">
            <div class="container">
                <h3>FAQs</h3>
                <div class="acc-box">
                    <span class="acc-trigger active"><a href="javascipt:void()">How safe and secure is this platform for Online Payment?</a></span>
                    <div class="acc-container open">
                        <p>The online Payment Gateway is hosted on the secure network. On every successful transaction, you

will receive a reference number and confirmation email with all your transaction details. You will also

receive a call from our Sales team to complete the subsequent formalities.</p>
                        
                                  
                    </div>
                    <span class="acc-trigger"><a href="javascipt:void()">Once I have applied for the offer online, what is the next set of steps?</a></span>
                    <div class="acc-container">
                       <p>On completion of a successful transaction, you will receive an Expression of Interest confirmation

with a unique customer code. The Sales team will contact the applicant and carry out the subsequent

processes of the site visit.</p>
                    </div>

                    <span class="acc-trigger"><a href="javascipt:void()">What is 'Expression of Interest'?</a></span>
                    <div class="acc-container">
                        <p>Expression of Interest will serve as an approval to the organization, as per the details provided by the

Applicant.</p>
</div>
                    <span class="acc-trigger"><a href="javascipt:void()">Are credit cards/debit cards of all banks accepted?</a></span>
                    <div class="acc-container">
                        <p>Yes, all bank networks of VISA and MasterCard are accepted.</p>
                       
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