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
                <?= $this->include('partials/navigation') ?>
            </div>
        </section>
        <section class="inner_banner_container">
            <div class="">
                <div class="slide-txt">
                    <div class="fr-main-left algn-left">
                        <h1>Be a Part of the Most Respected Brand in <br>Real Estate</h1>
                       <!--  <span class="txt-para">Are you looking to explore your endless potential as a real estate professional?</span> -->
                    </div>
                    <div class="fr-main-right learn-right">
                        <img src="<?php echo base_url(); ?>assets/images/career-slider-img.png" alt="" class="no-margin">
                    </div>
                </div>
            </div>
             <!-- <div class="banner_sidebar career-sidebar aos-item" data-aos="slide-left">
                <h2 class="m-t-01">Career Opportunity With Coldwell Banker </h2>
                
            </div> -->
        </section>

        <section class="career-sec aos-item"  id="Learning">
            <div class="container"> 
                    <p style="width: 80%; margin: 0 auto">If you are aspiring to build a career in real estate, then associate with Coldwell Banker India to give direction to your endless growth avenues at professional front.
</p>
                <div class="sub-nav m-tb-15">
                 <ul>
                        <li  ><a href="<?php echo base_url();?>career" class="<?php if($page_active=='career'){ echo 'active'; }?>">Coldwell Banker India Openings</a></li>
                        <li ><a href="<?php echo base_url();?>franchisees_openings" class="<?php if($page_active=='Franchisees Openings'){ echo 'active'; }?>">Franchise Openings</a></li>
                    </ul>
                </div>

                <div class="acc-box">
                    <?php   
                    $i=1;
                    foreach ($array_collection  as $row) { ?>
                        <span class="acc-trigger bg<?php echo $i;?>"><a href="javascipt:void()"><?= esc($row['position']) ?></a></span>
                        <div class="acc-container">
                         <?php echo $row['description'];?>


                            <div class="job-desc-row">
                                <div class="job-colm1">
                                    <h5>Educational Qualifications Required :</h5>
                                    <ul>
                                        <li><?php echo $row['qualification'];?></li>
                                    </ul>
                                </div>
                                <?php if($row['min_experience']!=""){?>
                                <div class="job-colm1">
                                    <h5>Min Experience :</h5>
                                    <ul>
                                        <li><?= esc($row['min_experience']) ?></li>
                                    </ul>
                                </div>
                                <?php } ?>
                                <?php if($row['max_experience']!=""){?>
                                <div class="job-colm1">
                                    <h5>Max Experience :</h5>
                                    <ul>
                                        <li><?= esc($row['max_experience']) ?></li>
                                    </ul>
                                </div>
                                <?php } ?>
                                <div class="job-colm1">
                                    <h5>Location of posting :</h5>
                                    <ul>
                                        <li><?= esc($row['location']) ?></li>
                                    </ul>
                                </div>
                            </div>
                            <a class="blue-btn apply-now" position-id="<?= esc($row['id'], 'attr') ?>">Apply Now</a>

                     </div>
                    <?php $i++;} ?>

                </div>
<!-- 
                <hr>
   <div class="testimonial-wrap">
                    <h3>Testimonials</h3>
                    <div class="owl-carousel" id="testimonials"> -->
                            <?php   
                           // $i=1;
                           // foreach ($array_testimonials  as $row_testimonials) { ?>
                            <!-- <div class="item">
                                <div class="person-img">
                                    <img src="<?php //echo base_url(); ?>uploads/carrers_testimonial/<?php //echo $row_testimonials['image_name']; ?>" alt="">
                                </div>
                                <div class="person-desc"><?php //echo $row_testimonials['title']; ?>
                                    <p><strong><?php //echo $row_testimonials['name']; ?></strong>
                                        <?php //echo $row_testimonials['position']; ?></p>
                                </div>
                            </div>--> 
                            <?php //} ?>
<!--                     </div>
                </div>  -->

            </div>
        </section>

        <section style="position: relative">
            <div class="container">
                <?php   
                $i=1; ?>
                
                <h3>Inspiring Stories of Success</h3>
                <div class="owl-carousel" id="success-stories">
                    <?php foreach ($array_success  as $stories) { ?> 
                        <div class="item">
                            <a class="fancybox fancybox.iframe" href="<?= esc($stories['video_url'], 'attr') ?>" data-fancybox-group="gallery">
                                                            <?php
                            $urlArr = explode("/",$stories['video_url']);
                            $urlArrNum = count($urlArr);
                            $youtubeVideoId = $urlArr[$urlArrNum - 1];
                            $thumbURL = 'http://img.youtube.com/vi/'.$youtubeVideoId.'/0.jpg';
                            echo '<img src="'.esc($thumbURL, 'attr').'" class="fancyb ox-image"/>';
                            ?>
<!--                                 <img src="https://www.coldwellbanker.com/content/images/successStories/danielle-moy.jpg" class="fancyb ox-image"> -->
                            </a>
                        </div> 
                    <?php } ?>
                </div>

                <hr>

                <h3><?= esc($life_at_coldwell['title'] ?? '') ?></h3>
                <p><?= $life_at_coldwell['description'] ?? '' ?></p>

               <div class="owl-carousel" id="testimonial-gallery">
                    <?php   
                 
                    foreach ($life_at_images  as $row_images) { ?>
                        <div class="item">
                            <a class="fancybox" href="<?php echo base_url(); ?>uploads/life_at_coldwell_banker/<?= esc($row_images['image'], 'url') ?>" data-fancybox-group="gallery">

                                <img src="<?php echo base_url(); ?>uploads/life_at_coldwell_banker/<?= esc($row_images['image'], 'url') ?>" class="fancyb ox-image">
                            </a>
                        </div>
                    <?php } ?>    

               </div>
            </div>
        </section>

        <div class="clear"></div>

        <?= $this->include('partials/sub_footer') ?>
    </article>
    <?= $this->include('partials/footer') ?>

        <section class="lightbox_bg" id="apply-now-frame">
           <div class="light_inner">
                <div class="application-form">
                    <a href="javascript:void(0);" class="close_popup">X</a>
                    <h4>Job Application Form</h4>
                    <span class="message " id="message_for_applicant"></span>
                    <p>Please fill in details to help us understand your requirements</p>
                    <form id="current_opening" name="current_opening" enctype="multipart/form-data"> 
                        <?= csrf_field() ?>
                        <div class="career-form">
                            <div class="form-row">
                                <input type="text" name="applicant_fname" id="applicant_fname" placeholder="First Name*">
                            </div>
                            <div class="form-row">
                                <input type="text" name="applicant_lname" id="applicant_lname" placeholder="Last Name*">
                            </div>
                            <div class="form-row">
							<div class="gender">
                                <strong>Gender</strong> 
                                <input type="radio" name="gender" value="male" id="gender"> Male 
                                <input type="radio" name="gender" value="Female" id="gender_1">Female
							</div>
                            </div>
							<div class="form-row">
                                <input type="text" name="applicant_mobile" id="applicant_mobile" placeholder="Mobile*">
                            </div>
                            <div class="form-row">
                                <input type="text" name="applicant_phone" id="applicant_phone" placeholder="Phone*">
                            </div>
							<div class="form-row">
                                <input type="text" name="applicant_email" id="applicant_email" placeholder="Email*">
                            </div>
                        </div>
						
                        <div class="career-form">
                            
                            <div class="form-row">
                                <textarea name="applicant_address" id="applicant_address" cols="30" rows="3" placeholder="Address*"></textarea>
                            </div>
                            <div class="form-row file">
                                <strong>Upload Resume</strong> 
                                <input type="file" name="resume_file" id="resume_file">
                            </div>
                        </div>
                        <input type="hidden" id="applicant_position" class="applicant_position" >
                        <div class="clear"></div>
                        <input type="submit" name="career_submit" id="career_submit" value="SUBMIT"> <small>*fields are mandatory.</small>
                    </form>  
                </div>
            </div>
        </section>
<script>
$(document).ready(function(){
	$('.apply-now').click(function() {
        var position_id  =  $(this).attr("position-id");
        $("#applicant_position").val(position_id);
        $('#apply-now-frame').show();

    }); 
});
</script>
<script src="<?php echo base_url(); ?>assets/js/jquery.validate.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/form.js"></script>
</body>
</html>