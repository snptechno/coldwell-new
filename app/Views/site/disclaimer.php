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
			  <h3>Disclaimer</h3>
				<ol style="line-height: 27px;font-size: 15px;color: #262626;text-align: left;padding: 0 50px;">
				
				<li>In any event Coldwell Banker India will not be responsible for any loss arising from the use of or

reliance on information provided on this website. The use of any information on this website is

completely at one’s own risk.</li>

<li> The information on this website does not constitute a legal contract of any type between the owner

and the visitor.</li>

<li> The names, marks and copyrights in the artistic style in which they are represented herein belong to

and are owned by Coldwell Banker India.</li>

<li> This website contains material which is owned by or licensed by Coldwell Banker India and is

protected by copyright and intellectual property laws. Reproduction is prohibited without prior

permission in writing.</li>

<li>Coldwell Banker India has the right to alter the information of this website without liability to

communicate any changes.</li>

<li>A visitor, by the act of submitting information through the website or email will be considered that

he/she has provided true, accurate, current and complete information. By entering a mobile number,

the visitor is considered to have approved Coldwell Banker India to send promotional SMS, alerts and

the permission to be contacted.</li>

<li>Coldwell Banker India shall make every effort to keep the website free of viruses and other harmful

components. This website and emails from Coldwell Banker India are vulnerable to tampering, data

corruption, viruses, an interception as well as delivery error and the visitor takes complete liability for

any consequences that may arise.</li>

<li>The images, features, designs, specifications, plans, facilities, etc. shown on this website are only

indicative and subject to the approval of the respective authorities. The owner has the right to change

these without any notice.</li>

<li>Coldwell Banker India or any of its representatives, managers, and employees shall not be

answerable for any damage arising out of or related to the use of the website.</li>

<li>The website may contain links to other websites which are not under control of Coldwell Banker

India. Any link to off-site pages or other may be accessed at one’s own risk.</li>
				</ol>
			</div>
		</section>
       
        <div class="clear"></div>

		<?= $this->include('partials/sub_footer') ?>
    </article>
    <?= $this->include('partials/footer') ?>

</body>
</html>