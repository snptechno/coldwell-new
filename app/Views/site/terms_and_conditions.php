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
			 <h3>Terms and Conditions</h3>
				<ol style="line-height: 27px;font-size: 15px;color: #262626;text-align: left;padding:0 50px;">
				
				<li>The Applicant/s, NRIs, and PIOs shall be responsible to obey all the legal agreements under the

applicable laws prevailing in India from time to time and the Company shall not be responsible for any

such non - compliance in any manner.</li>

<li>Subscribing the payment online requires a valid credit card VISA/Master Card or any other card

organization as well as the rules and regulations of the Reserve Bank of India applicable and prevailing

from time to time.</li>

<li>The risk involved with the online payment like the technical failure during the payment gateway

processes, amount/s debited from the Applicant/s account but not credited to the account of Coldwell

Banker India due to any technical error, Coldwell Banker India shall not be responsible for any such

technical error in the payment transaction/s or otherwise.</li>

<li>After the successful payment, a reference number will be produced and allotted to the Applicant/s

which shall be compulsorily required to be referred by the Applicant/s in all their further

communication. A reference number will be emailed to the Applicant on the email id mentioned in the

KYC form.</li>

<li>Only Indian Residents, NRIs and PIOs who are capable of entering into a contract under the Indian

Contract Act, 1881 shall be eligible to apply.</li>

<li>The above online payment plan is simply towards expressing attention in a particular deal with

Coldwell Banker India.</li>

<li>The terms and conditions mentioned herein shall be construed in accordance with the Indian laws

and any issues/disputes arising out of this transaction will be subjected to the exclusive jurisdiction of

Courts.</li>
				</ol>
			</div>
		</section>
       
        <div class="clear"></div>

		<?= $this->include('partials/sub_footer') ?>
    </article>
    <?= $this->include('partials/footer') ?>

</body>
</html>