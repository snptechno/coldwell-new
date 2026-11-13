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
                <?php $this->setVar('act', $page_active); // CI3: $data1['act'] for the navigation partial ?><?= $this->include('partials/navigation') ?>
            </div>
        </section>
        <section class="inner_banner_container">
            <div class="">
                <div class="slide-txt">
                    <div class="fr-main-left algn-left">
                        <h1>Research</h1>
                        <span class="txt-para">Find your dream home with our research based understanding</strong></span>
                    </div>
                    <div class="fr-main-right rese-rght">
                        <img src="<?php echo base_url(); ?>assets/images/research-slider-img.png" alt="" class="no-margin">
                    </div>
                </div>
            </div>
             <div class="banner_sidebar aos-item" data-aos="slide-left">
                <h2 class="m-t-01">Ask our Specialist <span>+91 22 6172 5797</span> </h2>
                <div class="divider"></div>
                <h2>Or <a href="javascript:void(0);" class="hm-enq-btn">Enquire Now</a></h2>
            </div>
        </section>

        <section class="aos-item" data-aos="fade-up" id="reasearch">
            <div class="container research_container">
               <p>Coldwell Banker India renders brief project reports and in-depth area insights to help you comprehend the ongoing trends in the real estate sector. Whether it is a residential property or a commercial space, we partner you in making an informed decision for your realty purchase. We partner you in making an informed decision for your property purchase. Rely on our detailed project reports and in-depth area insights.  Make the most of the latest trends and our analysis.</p>
                <div class="sub-nav">
                    <ul>
                        <li><a href="<?php echo base_url(); ?>research">Project Reports</a></li>
                        <li><a href="javascript:void()" class="active">Area Insights</a></li>
                    </ul>
                </div>

                 <ul class="blog-items area-in-list">
            				<?php
            				if(!empty($rz1)){
            					foreach($rz1 as $rz) {
            				?>
                              <li>
                                <a href="<?php echo base_url(); ?>uploads/research/<?= esc($rz['pdf_name'], 'url') ?>" target="_blank">
                                <h5><?= esc($rz['title']) ?> <small><?= esc($rz['description']) ?></small></h5>
                               <img src="<?php echo base_url(); ?>uploads/research/<?= esc($rz['image_name'], 'url') ?>" alt="<?= esc($rz['title'], 'attr') ?>">
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