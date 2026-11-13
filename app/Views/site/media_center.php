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
                        <li><a href="javascript:void()" class="active">Coldwell Banker India News</a></li>
                        <li><a href="<?php echo base_url(); ?>awards" >Awards</a></li>
                    </ul>
                </div>

               <ul class="blog-items media-itm  aos-item"data-aos="fade-up">    
        				<?php 
        				if(!empty($rz1)){
        					foreach($rz1 as $rz) {?>

                          <li class="pintch_image" image-id="<?= esc($rz['id'], 'attr') ?>" >
                              <?php if($rz['external_link']!=""){ ?> 
                                <a class="fancybox" href="<?= esc($rz['external_link'], 'attr') ?>"  target="_blank" data-fancybox-group="gallery">
                                    <h5><?= esc($rz['title']) ?><small><?= $rz['description'] ?></small></h5> 
                                   <img src="<?php echo base_url(); ?>uploads/news/<?= esc($rz['image'], 'url') ?>"  id="fancy_img" alt="" class="fancyb ox-image">
                                </a>
                              <?php }else{ ?>   
                                <a class="fancybox fancybox.iframe" href="<?php echo base_url(); ?>/iframe_image/<?= esc($rz['id'], 'attr') ?>" data-fancybox-group="gallery">
                                    <h5><?= esc($rz['title']) ?><small><?= $rz['description'] ?></small></h5> 
                                   <img src="<?php echo base_url(); ?>uploads/news/<?= esc($rz['image'], 'url') ?>"  id="fancy_img" alt="" class="fancyb ox-image">
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
                url: baseUrl+'load_more_news',
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