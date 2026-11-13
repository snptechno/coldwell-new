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
    <article class="media aos-all">
        <section>
            <div class="container">
                <?= $this->include('partials/navigation') ?>
            </div>
        </section>
        <section class="inner_banner_container">
            <div class="inner_banner">
                <div class="slide-txt">
                    <div class="fr-main-left algn-left aos-item"data-aos="fade-up">
                        <h1>Media Center</h1>
                        <span class="txt-para">Stay updated with the latest real estate news.</strong></span>
                    </div>
                    <div class="fr-main-right learn-right">
                        <img src="<?php echo base_url(); ?>assets/images/media-slider-img.png" alt="" class="no-margin">
                    </div>
                </div>
            </div>
        </section>

        <section class="learning-sec" id="media">
            <div class="container">
                <div class="sub-nav">
                    <ul>
                        <li><a href="<?php echo base_url(); ?>media-center">Coldwell Banker India News </a></li>
<!--                         <li><a href="<?php echo base_url(); ?>industry-news">Industry News</a></li>
                        <li><a href="<?php echo base_url(); ?>knowledge-series">Knowledge Series</a></li>
                        <li class="active"><a href="javascript:void()" class="active">Newsletter</a></li> -->
                    </ul>
                </div>

               
                <h3>Subscribe to our newsletter</h3>
                <div class="subscriber-inputs">
					<div class="message <?php if($flash_class!=''){ echo esc($flash_class, 'attr');}?>"><?php if($flash_message!=""){ echo esc($flash_message);}?></div>
					<form id="subscriberForm" method="post" action="<?php echo base_url(); ?>newsletter_subscription">
<?= csrf_field() ?>
						<input type="text" name="email" id="" placeholder="Enter Email ID"><input type="submit" name="" id="" value="SUBSCRIBER"> 
					</form>
                </div>
                <hr>

               <ul class="blog-items newslettr-itm  aos-item" data-aos="fade-up"> 
            				<?php 
            				if(!empty($rz1)){
            					foreach($rz1 as $rz) {?>
            					  <li>
                                   <?php if($rz['external_link']!=""){ ?> 
                                       <a class="fancybox fancybox.iframe" href="<?= esc($rz['external_link'], 'attr') ?>"  target="_blank" data-fancybox-group="gallery">
                                        <img src="<?php echo base_url(); ?>uploads/newsletter/<?= esc($rz['image'], 'url') ?>"  id="fancy_img" alt="" class="fancyb ox-image">
                                        <h5><?php echo date("d-m-Y", strtotime($rz['created_date'])); ?></small></h5>
                                      </a>
                                   <?php }else{ ?>
                                      <a class="fancybox fancybox.iframe" href="<?php echo base_url(); ?>/iframe_newsletter/<?= esc($rz['id'], 'attr') ?>" data-fancybox-group="gallery">
                                        <img src="<?php echo base_url(); ?>uploads/newsletter/<?= esc($rz['image'], 'url') ?>"  id="fancy_img" alt="" class="fancyb ox-image">
                                        <h5><?php echo date("d-m-Y", strtotime($rz['created_date'])); ?></small></h5>
                                      </a>
                                   <?php } ?>                                     

            					  </li>
                            <?php
            					}
            				}
            				?>
                </ul>   
                <span class="load-more">Load More</span>
            </div>
        </section>


        <div class="clear"></div>
        <?= $this->include('partials/sub_footer') ?>
        
    </article>
    <?= $this->include('partials/footer') ?>

<script>
 $(document).ready(function(){

    $(document).on("click",".load-more",function(){
        var start_count = $('ul.blog-items li').length;
        if(start_count!=""){

             $.ajax({
                url: baseUrl+'load_more_newsletter',
                type: 'POST',
                data: 'start_no='+start_count,
                success: function (response) {
                    //console.log($(response).length);
                    $("ul.blog-items").append(response);
                    if($(response).length < 8){
                        $(".load-more").hide();
                    }

                },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert('Some error occurred');
                }
            });

        }

    });

 });   
</script>
</body>
</html>