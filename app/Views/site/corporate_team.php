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
    <article class="aos-all crp-team">
        <section>
            <div class="container">
                <?= $this->include('partials/navigation') ?>
            </div>
        </section>
        <section class="inner_banner_container">
            <div class="">
                <div class="slide-txt" style="margin-top: 173px;">
                    <div class="fr-main-left aos-item aos-init aos-animate" data-aos="fade-up">
                        <h1>Corporate Team</h1>
                        <span class="txt-para">True team builds true passion! Members of the Coldwell Banker India are set out on a mission to transform Indian real estate sector.</span>
                    </div>
                    <div class="fr-main-right">
                        <img src="<?php echo base_url(); ?>assets/images/corporate_img.png" alt="">
                    </div>
                </div>
            </div>
           <!--  <div class="banner_sidebar aos-item aos-init aos-animate" data-aos="slide-left" style="height: 688px;">
                <h2 class="m-t-01">Ask our Specialist <span>+91 22 6172 5797</span> </h2>
                <div class="divider"></div>
                <h2>Or <a href="javascript:void(0);" class="hm-enq-btn">Enquire Now</a></h2>
            </div> -->
        </section>
        <div class="clear"></div>
        <section class="team_container" id="team">
            <div class="container">
                <h2>Our Team</h2>
                <p>A team of corporate visionaries or complete business partners, Coldwell Banker India develops strategic ways to enrich your real estate experience. Meet the leaders, who are incorporated with the goal to assist you in finding or selling a home!</p>
                
				<?php	
				$array_part = array_chunk($array_collection, 4);

				$i=1;
				foreach ($array_part as $row) {
					echo '<ul class="team_0'.$i.'">';
					foreach ($row as $line) { ?>
						<li>
							<div class="team_img" id="<?= esc($line['id'], 'attr') ?>">
								<img src="<?php echo base_url(); ?>uploads/corporate_team/<?= esc($line['image'], 'url') ?>">
								<span class="plus_icon">+</span>
							</div>
							<div class="abt_team"><span><?= esc($line['name']) ?></span>
							<?= esc($line['title']) ?>
							</div>
						</li>
				<?php 	} $i++;
					echo '</ul>';
				} ?>

            </div>
        </section>
        <div class="clear"></div>
        <?= $this->include('partials/sub_footer') ?>
        <section class="lightbox_bg" id="corporate_team_iframe" style="display:none;">
            <div class="light_inner">
                <div class="corporate_team_lightbox">
                    <a href="javascript:void()" class="close_popup">X</a>
					<div id="team_html">
						<div class="team_mem"><img src="<?php echo base_url(); ?>assets/images/team02.png"></div>
						<div class="member_details">
							<h2>Vishal Malik<br> <span>Director</span></h2>
							<div class="light_divider"></div>
							<p>He is responsible for managing the operations of Coldwell Banker India. An MBA from the Asian Institute of Management, Manila and a B.E. from Mumbai University, he has over 15 years of experience in Corporate Finance and Investment Management, having worked with companies such as Reliance Industries, Morgan Stanley and Strategic Capital.
								<br>
								<br> Vishal has extensive experience in managing and building varied businesses in the financial services industry and sits on the board of several publicly listed and unlisted companies as an Independent Director. In addition his experience spans real estate financing and structuring with large and mid-sized developers over the years. He has a strong track record in investment management for HNI's across multiple estate. Vishal Malik is based in Ba</p>
							<div class="light_divider"></div>
						</div>
					</div>
                </div>
            </div>
        </section>
    </article>
    <?= $this->include('partials/footer') ?>
<script type="text/javascript">
	$(document).ready(function() {
		// $(".member_details").mCustomScrollbar();
		$(document).on("click",".team_img",function(){
			var id = $(this).attr("id");	
			$.ajax({
				url: baseUrl+'get_team_information',
				type: 'POST',
				data: {user_id:id},
				success: function (response) {
					//alert(response);
					console.log(response);
					$("#team_html").empty();
					$("#team_html").html(response);
					/*$(".corporate_team_lightbox").append(''+response+'');
					$(".member_details").mCustomScrollbar(); */
					$('#corporate_team_iframe').show();
				},
				error: function (jqXHR, textStatus, errorThrown) {
					alert('Some error occurred');
				}
			});	

		});
		$('.close_popup').click(function() {
			$('#corporate_team_iframe').hide();        
		});
	});
</script>
</body>
</html>