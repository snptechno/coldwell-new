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
    <article class="learnings career-side aos-all">
        <section>
            <div class="container">
                <?= $this->setVar('act', $page_active)->include('partials/navigation') ?>
            </div>
        </section>
        <section class="inner_banner_container">
            <div class="">
                <div class="slide-txt">
                    <div class="fr-main-left aos-item"data-aos="fade-up">
                        <h1>Learning & Development</h1>
<!--                         <span class="txt-para">You learn you last.<br> You develop you drive.<br> Take an overview at our learning programs. </span> -->
                    <span class="txt-para">If you learn... you last.<br> If you develop... you drive.<br> Take an overview at our learning programs. </span>
                    </div>
                    <div class="fr-main-right learn-right">
                        <!-- <img src="<?php echo base_url(); ?>assets/images/learning-slider-img.png" alt="" class="no-margin"> -->
                    </div>
                </div>
            </div>
        </section>

        <section class="" id="Learning">
            <div class="container">
                <div class="sub-nav">
                    <ul>
                        <li><a href="#overview1" class="active">Overview</a></li>
                        <li><a href="#learn-university1">Coldwell Banker University</a></li>
                        <li><a href="#learn-events1">Training Calendar</a></li>
                        <li><a href="#learn-gallery1">Gallery</a></li>
                    </ul>
                </div>
                <div class="learn-main" id="overview">
                   <h3>Overview</h3>
                <div id="overview1" class="hidden"></div>

                   <?php
					if(!empty($rz2)){
					?>
						<?php echo $rz2['overview'];?>
					   <!--<p>Real estate sector in India has emerged as an eminent propeller of country’s economy, national growth and GDP for long. Comprising of four categories namely housing, retail, hospitality, and commercial, the industry has the second largest share in propagating employment in the nation after agricultural sector.<br><br>
						This unorganized sector operates in perfect compliance with the growth of the corporate environment and demand for office space apart from urban and semi-urban accommodations. The out of the box approach of our real estate agents have been instrumental in meeting consumer’s property requisites. With their extensive knowledge of the market, consumer behavior and customer expectations, the scope and demand for professional and ethical brokers have tremendously grown.<br><br>
						We at Coldwell Banker India believe in enhancing this segment of the real estate industry. Aiming to boost investor’s confidence it is our continuous endeavor to reform and elevate up to international standards. Whether you are a seasoned real estate professional or just learning the ropes, Coldwell Banker India equips our specialists with the skills to meet the challenges & opportunities of today's ever-changing real estate market. We provide a solid foundation for a successful career personally whilst generate revenue for our franchisees.<br><br>
						Our training workshops provide relevant, updated and practical development for our Franchisee Owners and Agents enhancing the potential to do far more successful business. We are also leveraging our Coldwell Banker University Modules which is considered internationally #1 in the world of real estate.</p>-->
                    <div id="learn-university1" class="hidden"></div>

                    <hr>

                    <br>

                    <img src="<?php echo base_url(); ?>uploads/university_logo/<?= esc((string) $rz2['university_logo'], 'url') ?>" alt=""id="learn-university">
 

                    <h5>"Coldwell Banker University® learning programs are not simply about the practice of selling real estate; they're about the practice of running a smarter business."</h5>

					<?php echo $rz2['university'];?>
                    <!--<p>For over 30 years, CBU has developed and delivered learning content, both online and off, to help Coldwell Banker® sales professionals achieve extraordinary business results and in turn, give consumers the exceptional experience they deserve when buying or selling a home.<br><br>
                    CBU programs provide a solid foundation for new agents to learn how to run an effective real estate business, and help seasoned agents refine their skills and keep them up-to-date, with the goal of ensuring customers feel their experience with Coldwell Banker Real Estate is truly exceptional.<br><br>
                    By attending the Generation Blue Experience, the most extraordinary gathering of real estate professionals anywhere, sales associates and managers throughout the world can network with leaders in their field to learn the latest trends and technologies in real estate.</p>-->
                                    
				<?php
					}
					?>
                </div>               

            </div>
        </section>


        <section class="training-module-bg">
            <div class="container">
                <h3>Training Programs</h3>                    
                    <div class="top-lst">
                        <span>1</span>
                        <h4 class="first_h4">Product Programs</h4>
                        <ul class="">
                            <li><h4>Primary</h4></li>
                            <li><h4>Secondary</h4></li>
                            <li><h4>Rental</h4></li>
                        </ul>
                    </div>

                    <ul class="other-mod-lst">
                        <li><span>2</span><h4>Technical & <br>Legal Training</h4></li>
                        <li><span>3</span><h4>How to win<br> a Mandate?</h4></li>
                        <li><span>4</span><h4>Capital<br> Gains</h4></li>
                        <li><span>5</span><h4>Negotiation &<br> Closing Skills</h4></li>
                        <li><span>6</span><h4>Innovative listings for<br> primary & Secondary</h4></li>
                    </ul>
            </div>
        </section>

        <?php
        if(!empty($events)){
        ?>
        <section class="learn-events" id="learn-events">
            <div id="learn-events1" class="hidden"></div>
            <div class="container">
                    <h3>Coldwell Banker India Training Calendar</h3>
                <div class="evnts-main">
                     
                        <?php 
                        $count = 1;
                        foreach($events as $row) { 
                            $start_date_array_result = explode('-', $row['start_date']);
                            $monthNum  = $start_date_array_result[1];
                            $dateObj   = DateTime::createFromFormat('!m', $monthNum);
                            $monthName = substr($dateObj->format('F'),0,3);

                            $end_date_array_result = explode('-', $row['end_date']);
                            $month_end_Num  = $start_date_array_result[1];
                            $dateObj   = DateTime::createFromFormat('!m', $month_end_Num);
                            $endmonthName = substr($dateObj->format('F'),0,3);
                            if($count%5 == 0 || $count==1){ ?>
                                 <div class="top-rod hide-mob"></div>
                            <?php } ?>
                            <?php if($count%3 == 0){ ?>
                                 <div class="top-rod hidden"></div>
                            <?php } ?>
                                 <div class="top-rod hidden1"></div>

                            <div class="ent-item ">
                                <div class="ent-cont">
                                    <h3><?= esc($monthName.' '.$start_date_array_result[0]) ?></h3>
                                    <strong><?= esc($row['title']) ?></strong>
                                    <p>Dates :  <?= esc($start_date_array_result[2].' '.$monthName.' To '.$end_date_array_result[2].' '.$endmonthName) ?><br>
                                    Duration : <?php if($row['duration']>1) { echo esc($row['duration']).' Days';}else{ echo esc($row['duration']).' Day'; } ?></p>
                                </div>
                            </div>
                        <?php $count ++;} ?>

                </div>
            </div>
        </section>
        <?php } ?>
        
		<?php
        $exclude_images = array();
		if(!empty($rz3)){
		?>
        <section class="gallery-sec aos-item"data-aos="fade-up" id="learn-gallery">            
        <div id="learn-gallery1" class="hidden"></div>
                <hr>
            <div class="container">
                <h3>Photo Gallery</h3>
                <div class="owl-carousel" id="gallery">
				<?php
                /*-------------------- added all id of images in array  that is need to exclude from fancybox------------*/
                    
                /*----------------------------------*/
						foreach($rz3 as $rz) { 
                            array_push($exclude_images,$rz['id']);
                        ?>
                        <div class="item">
                            <a class="fancybox" href="<?php echo base_url(); ?>uploads/learning_gallery_thumb/<?= esc((string) $rz['image_name'], 'url') ?>" data-fancybox-group="gallery<?= esc((string) $rz['group_id'], 'attr') ?>" id=""><img src="<?php echo base_url(); ?>uploads/learning_gallery_thumb/<?= esc((string) $rz['image_name'], 'url') ?>"  id="<?= esc((string) $rz['id'], 'attr') ?>"  group-id="<?= esc((string) $rz['group_id'], 'attr') ?>" class="gallery_image" alt="<?= esc($rz['title'], 'attr') ?>" class="fancyb ox-image"></a>
                            <small><?= esc($rz['title']) ?></small>
                        </div>
				<?php } ?>
                </div>
                              
            </div>
        </section>


		<?php
		}
        $images_id_str = implode(',', $exclude_images);
		?>
        
         <div style="display: block" id="gallery_lightbox_images" >
                <?php
                    foreach($rz4 as $rz) {?>

                         <a class="fancybox" href="<?php echo base_url(); ?>uploads/learning_gallery/<?= esc((string) $rz['image_name'], 'url') ?>" data-fancybox-group="gallery<?= esc((string) $rz['group_id'], 'attr') ?>" id=""></a>
                <?php } ?>
        </div> 

        <div class="clear"></div>
        <?= $this->include('partials/sub_footer') ?>
    </article>
    <?= $this->include('partials/footer') ?>

<script>
 $(document).ready(function(){

    //$(document).on("click",".gallery_image",function(){

        //var group_id= $(this).attr("group-id");
        //alert(group_id);

       //  if(group_id!=""){

       //      $.ajax({
       //          url: baseUrl+'load_group_images_gallery',
       //          type: 'POST',
       //          data: 'group_id='+group_id,
       //          success: function (response) {
       //              $("#gallery_lightbox_images").html(response);
       //          },
       //          error: function (jqXHR, textStatus, errorThrown) {
       //              alert('Some error occurred');
       //          }
       //      });

       // }

    //});

 });   
</script>
</body>
</html>
