<footer>
	<div class="container">
		<ul class="foot_links">
			<li>VIDEOS</li>
			<li><a href="<?php echo base_url(); ?>finding-a-home">Finding a Home!</a></li>
			<li><a href="<?php echo base_url(); ?>selling-a-home">Selling a Home!</a></li>
			<li><a href="<?php echo base_url(); ?>testimonials">Testimonials</a></li>
			<li><a href="<?php echo base_url(); ?>cb-india-videos">CB India videos</a></li>
			<li><a href="<?php echo base_url(); ?>featured-videos">Featured Videos </a></li>
			
		</ul>
		<ul class="foot_links">
			<li>ABOUT US</li>
			<li><a href="<?php echo base_url(); ?>about-coldwell-banker">Coldwell Banker India</a></li>
			<!--<li><a href="<?php echo base_url(); ?>coldwell-banker-usa">Coldwell Banker USA</a></li>-->
			<li><a href="<?php echo base_url(); ?>corporate-team">Corporate Team</a></li>
			<li><a href="<?php echo base_url(); ?>career">Careers</a></li>
			<li><a href="<?php echo base_url(); ?>terms">Terms and Conditions</a></li>
			<li><a href="<?php echo base_url(); ?>privacy">Privacy Policy</a></li>
			<li><a href="<?php echo base_url(); ?>disclaimer">Disclaimer</a></li>
			<li><a href="<?php echo base_url(); ?>faq">FAQ</a></li>
			<li><a href="<?php echo base_url(); ?>refund">Refund Policy</a></li>
			<li><a href="<?php echo base_url(); ?>quick-pay">Quick Pay</a></li>
		</ul>
		<ul class="foot_links">
			<li>OFFICES</li>
			<!-- <li><a href="<?php echo base_url(); ?>career"><strong>Careers in Real Estate</strong></a></li> -->
			<li><a href="<?php echo base_url(); ?>our-franchise">Our Offices</a></li>
			<li><a href="<?php echo base_url(); ?>contact-us">Contact Us</a></li>
		</ul>
		<ul class="foot_links">
			<li>MEDIA CENTER</li>
			<li><a href="<?php echo base_url(); ?>media-center">CB India News</a></li>
			<li><a href="<?php echo base_url(); ?>awards">Awards</a></li>
<!-- 			<li><a href="<?php //echo base_url(); ?>industry-news">Industry News</a></li>
			<li><a href="<?php //echo base_url(); ?>knowledge-series">Knowledge Series</a></li>
			<li><a href="<?php //echo base_url(); ?>newsletter">Newsletter</a></li> -->
		</ul>
		<ul class="foot_links last_child">
			<li>BLOGS</li>
			<?php
			// Latest posts from the CI4 blog (replaces two hard-coded links to Drupal posts that no longer exist).
			try {
			    $footerPosts = model(\App\Models\BlogPostsModel::class)->recent(2);
			} catch (\Throwable $e) {
			    $footerPosts = [];
			}
			foreach ($footerPosts as $fp): ?>
			<li><a href="<?= base_url('blog/' . $fp['slug']) ?>"><?= esc($fp['title']) ?></a></li>
			<?php endforeach; ?>
			<li><a href="<?php echo base_url(); ?>blog">All Blog Posts</a></li>
		</ul>
		<div class="clear"></div>
		<div class="copyright">
			<p>© 2017 Coldwell Banker India. All rights reserved. <a href="#" class="disclaimer-btn">Disclaimer</a></p>
		</div>
		<div class="social_media">
			<ul>
				<li>
					<a href="https://www.facebook.com/coldwellbanker.india" target="_blank"><img src="<?php echo base_url(); ?>assets/images/fb_icon.png"></a>
				</li>
				<li>
					<a href="https://twitter.com/CBIndiatweet" target="_blank"><img src="<?php echo base_url(); ?>assets/images/twitter_icon.png"></a>
				</li>
				<li>
					<a href="https://www.linkedin.com/company/coldwell-banker-india" target="_blank"><img src="<?php echo base_url(); ?>assets/images/in_icon.png"></a>
				</li>
				<li>
					<a href="https://www.youtube.com/user/coldwellbanker" target="_blank"><img src="<?php echo base_url(); ?>assets/images/youtube_icon.png"></a>
				</li>
			</ul>
		</div>
	</div>
</footer>
<section class="lightbox_bg" id="home-enquire">
	<div class="light_inner">
		<div class="home-enquire-form">
			<a href="javascript:void(0);" class="close-home-enq"></a>
			<h2>Enquire Now</h2>
			<form id="enquire_popup">
				<?= csrf_field() ?>
				<span id="enquire_popup_response"></span>
				<div class="form-row"><input type="text" name="name" id="" placeholder="Name*"></div>                            
				<div class="form-row"><input type="text" name="email" id="" placeholder="Email*"></div>
				<div class="form-row"><input type="text" name="phone" id="" placeholder="Phone*"></div>
				<div class="form-row">I am interested in <br><br>
					<input type="checkbox" name="interest[]" value="Buying" class="no-margin"> Buying <input type="checkbox" name="interest[]" value="Selling"> Selling   <input type="checkbox" name="interest[]" value="Renting"> Renting
				</div>
				<input type="hidden" name="source" value="popup">
				<div class="clear"></div>
				<input type="submit" name="" id="" value="SUBMIT"> <small>*fields are mandatory.</small>
			</form>  
		</div>
	</div>
</section>

<section class="lightbox_bg" id="disclaim" style="display:none;">
    <div class="light_inner">
        <div class="disc-wrap">
            <a href="javascript:void()" class="close_popup">X</a>
                <h2>Disclaimer</h2>              
                <p>© 2017 Coldwell Banker Real Estate LLC. All Rights Reserved. Coldwell Banker®, the Coldwell Banker logo, Coldwell Banker Previews International® and the Coldwell Banker Previews International logo are registered service marks owned by Coldwell Banker Real Estate LLC. Coldwell Banker Real Estate LLC fully supports the principles of the Fair Housing Act and the Equal Opportunity Act. Each Office is Independently Owned and Operated.
                </p>
        </div>
    </div>
</section>

<section class="lightbox_bg disclaimer-wrapper" id="newdisclaim" style="display:none;">
    <div class="light_inner">
        <div class="disc-wrap">
                <h2>Disclaimer</h2>
                <p>Thank you for visiting our website. This disclaimer ("Disclaimer") is applicable to this website. By using or accessing this website you agree with the Disclaimer without any qualification or limitation. This website is in the process of being updated. By accessing this website, the viewer confirms that the information including brochures and marketing collaterals on this website are solely for informational purposes only and the viewer has not relied on this information for making any booking/purchase in any project of the Company. Nothing on this website, constitutes advertising, marketing, booking, selling or an offer for sale, or invitation to purchase a unit in any project by the Company. The Company is not liable for any consequence of any action taken by the viewer relying on such material/ information on this website. The Real Estate (Regulation and Development) Act, 2016, and Rules made thereunder (where notified) ("RERA") have come into effect from 1st May, 2017. The contents of this website may not be fully in line with RERA as of date. We are in the process of modifying, revising and updating this website in terms of the stipulations / recommendations under RERA, which shall be completed soon. Please telephone our sales centres or visit our sales office during office hours to speak to our sales team for verifying all the details, including area, amenities, services, terms of sales and payments and other relevant terms independently with the sales team/ company prior to concluding any decision for buying any unit(s) in the projects. The information contained on this website shall not be construed to be an advertisement. We thank you for your patience and understanding.</p>
				<p class="align-center m-t-20"> <button class="agree_btn" id="disclaimer">I Agree</button></p>
		</div>
    </div>
</section>
