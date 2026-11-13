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
    <article class="contact aos-all">
        <section>
            <div class="container">
                <?php $this->setVar('act', $page_active); // CI3: $data1['act'] for the navigation partial ?><?= $this->include('partials/navigation') ?>
            </div>
        </section>
        <section class="inner_banner_container">
           <img src="<?php echo base_url(); ?>assets/images/contact-bg.jpg" alt="">
        </section>

        <section>
            <div class="container">
                <div class="contact-sec">
                    <div class="cont-left">
                        <h3>Contact Us</h3>
                        <div class="info-rw"><img src="<?php echo base_url(); ?>assets/images/ad_01.png" alt=""> <span>A-2- The New Sputink Co. OP .SOC. LTD Plot No 456 TPS 3rd 24th road bandra West Mumbai 400050.</span></div>
                        <div class="info-rw"><img src="<?php echo base_url(); ?>assets/images/envelope-icon.png" alt="">info@coldwellbanker.in</div>
                      <div class="info-rw"><img src="<?php echo base_url(); ?>assets/images/cont-phn-icon.png" alt="">+91-8587819200</div>
                    </div>
                    <div class="cont-right" id="cont-right">
                        <h3>Enquire Now</h3>
                        <!--<form action="">
                           <input type="text" name="" id="" placeholder="Name*">
                           <input type="text" name="" id="" placeholder="Email*">
                           <input type="text" name="" id="" placeholder="Phone No.*">
                           <textarea name="" id="" cols="30" rows="5" placeholder="Message*"></textarea>
                           <input type="submit" name="" id="" value="Send Enquiry">
                           <small>*fields are mandatory</small>
                        </form>   -->     
						<div class="message <?= esc($flash_class, 'attr') ?>"><?= esc($flash_message) ?></div>
                        <form id="contact_us" method="post" action="<?php echo base_url(); ?>contact_enquiry">
<?= csrf_field() ?>
                           <input type="text" name="name" id="" placeholder="Name">
                           <input type="text" name="email" id="" placeholder="Email">
                           <input type="text" name="phone" id="" placeholder="Phone No.">
                           <textarea name="message" id="" cols="30" rows="5" placeholder="Message"></textarea>
                           <input type="submit" name="" id="" value="Send Enquiry">
                           <small>*fields are mandatory</small>
                        </form>           
                    </div>
                </div>
            </div>
        </section>
        
       <!-- <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3770.7565137784763!2d72.83261841498876!3d19.074440087088842!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3be7c90c275c0001%3A0x92147424355baa0e!2sColdwell+Banker+India!5e0!3m2!1sen!2sin!4v1487341998497" width="100%" height="450" frameborder="0" style="border:0" allowfullscreen></iframe>-->

		<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3767.3876534331234!2d72.97507691447056!3d19.22192998700439!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x227f0c4dd62fc67c!2sOrion+Business+Park!5e0!3m2!1sen!2sin!4v1523523179077" width="100%" height="450" frameborder="0" style="border:0" allowfullscreen></iframe>
		
        <div class="clear"></div>
        <?= $this->include('partials/sub_footer') ?>
        
    </article>
     </article>
    <?= $this->include('partials/footer') ?>
<script>
$(document).ready(function(){
	
});
</script>
</body>
</html>
