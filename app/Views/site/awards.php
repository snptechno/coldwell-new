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
            <div class="">
                <div class="slide-txt">
                    <div class="fr-main-left algn-left aos-item"data-aos="fade-up">
                        <h1>Media Center</h1>
                        <span class="txt-para">Stay updated with trending real estate news coverage and articles.</strong></span>
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
                        <li><a href="<?php echo base_url(); ?>media_center" >Coldwell Banker India News</a></li>
                        <li><a href="javascript:void()" class="active">Awards</a></li>
                    </ul>
                </div>

               <ul class="blog-items media-itm  aos-item"data-aos="fade-up">    
        				<?php 
        				if(!empty($rz1)){
        					foreach($rz1 as $rz) {
        				?>
        					  <li class="pintch_image" image-id="<?= esc($rz['id'], 'attr') ?>" >
        						
                    <a class="fancybox fancybox.iframe" href="<?php echo base_url(); ?>/iframe_awards/<?= esc($rz['id'], 'attr') ?>" data-fancybox-group="gallery">
                      <h5><?= esc($rz['name']) ?></h5> 
                        <img src="<?php echo base_url(); ?>uploads/awards/<?= esc($rz['image'], 'url') ?>"  id="fancy_img" alt="" class="fancyb ox-image">
                        </a>
        					  </li>
        				<?php
        					}
        				}
        				?>
                </ul>
                
            </div>
        </section>


        <div class="clear"></div>
        <?= $this->include('partials/sub_footer') ?>
    </article>    
    <?= $this->include('partials/footer') ?>

</body>
</html>