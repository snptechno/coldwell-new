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
                        <h1>Videos</h1>
                         <span class="txt-para">When seeing is believing, having a prior perspective on a real estate property is highly recommended!</span>
                    </div>
                    <div class="fr-main-right learn-right">
                        <img src="<?php echo base_url(); ?>assets/images/videos-slider-img.png" alt="" class="no-margin">
                    </div>
                </div>
            </div>
             <div class="banner_sidebar aos-item" data-aos="slide-left">
                <h2 class="m-t-01">Ask our Specialist <span>+91 22 6172 5797</span> </h2>
                <div class="divider"></div>
                <h2>Or <a href="javascript:void(0);" class="hm-enq-btn">Enquire Now</a></h2>
            </div>
        </section>

        <section class="videos-sec aos-item" data-aos="fade-up" id="videos">
            <div class="container">
                <div class="sub-nav">
                    <ul>
                        <li><a href="<?php echo base_url(); ?>finding-a-home">Finding a home</a></li>
                        <li><a href="<?php echo base_url(); ?>selling-a-home">Selling a home</a></li>
                        <li><a href="<?php echo base_url(); ?>testimonials">Testimonials</a></li>
                        <li><a href="<?php echo base_url(); ?>cb-india-videos">CB India videos</a></li>
                        <li><a href="javascript:void()" class="active">Featured Videos </a></li>
                    </ul>
                </div>
                
				<!-- <h3>From Here To Home</h3>   -->
				<p>Get to know Coldwell Banker India better with exclusively Featured Videos</p>

				<!-- <h4>For a hassle free experience, call us at <span>+91 22 6172 5797</span></h4> -->

				<div class="video-player aos-item" data-aos="fade-up">
					<?php 
					if(!empty($videos)){
						$i = 1;
						foreach($videos as $rz) {
					?>
						<div class="video-colleft"  video-url="<?= esc($rz['video_url'], 'attr') ?>">
							<div class="video_thumb">
							<?php
							$urlArr = explode("/",$rz['video_url']);
							$urlArrNum = count($urlArr);
							$youtubeVideoId = $urlArr[$urlArrNum - 1];
							$thumbURL = 'http://img.youtube.com/vi/'.$youtubeVideoId.'/0.jpg';
							echo '<img src="'.esc($thumbURL, 'attr').'"/>';
							?>
								<span class="play-icon"></span>
							</div>
							<h4><?= esc($rz['title']) ?>
							<small><?= $rz['description'] ?></small></h4>
						</div>
					<?php
						$i++;}
					}
					?>
				</div>
            </div>
        </section>

        <div class="clear"></div>

		<?= $this->include('partials/sub_footer') ?>

<section class="lightbox_bg" id="video_iframe" style="display:none">
<div class="light_inner">
<div class="video_iframe_lightbox">
     <a href="javascript:void(0);" class="close_popup"></a>
<iframe id="iframe_url" width="100%" src="" frameborder="0" allowfullscreen></iframe>
</div>
</div>
</section>


    </article>
    <?= $this->include('partials/footer') ?>
<script>
$(document).ready(function(){
	$('.video-colleft,.play-icon').click(function() {
		var url =  $(this).attr("video-url");  
		$('#iframe_url').attr('src','');
		$('#iframe_url').attr('src',url);
        $('#video_iframe').show();        
    });    

   $('.close_popup').click(function() {
	   $('#iframe_url').attr('src','');
        $('#video_iframe').hide();        
   }); 
});
</script>
</body>
</html>