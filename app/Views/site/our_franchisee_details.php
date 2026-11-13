<?php
// $rz1 (franchise row merged with one owner's columns), $testimonials, $rz2 (owners)
// and $rz4 (specialists) are passed in by Site\Franchise::ourFranchiseDetails().

$franchises_name = ucwords(strtolower($rz1['title']));
?>

<!doctype html>
<html>

<head>
  <title><?= esc($page_info->title . ' ' . $franchises_name) ?></title>
  <meta name="description" content="<?= esc($page_info->meta_description, 'attr') ?>" />
  <meta name="keywords" content="<?= esc($page_info->meta_keywords, 'attr') ?>" />
  <?= $this->include('partials/header') ?>
  <?= $this->include('partials/ga_code_header') ?>
</head>

<body class="franchise-details">
  <article class="nri aos-all">
    <section>
      <div class="container">
        <?php $this->setVar('act', $page_active); // CI3: $data1['act'] for the navigation partial ?><?= $this->include('partials/navigation_w') ?>
      </div>
    </section>
    <section class="inner_banner_container">
      <div class="inner_banner">
        <div class="slide-txt">
          <div class="fr-main-left aos-item" data-aos="fade-up">
            <ul>
              <li>
                <img src="<?php echo base_url(); ?>assets/images/cb-white-icon-01.png">
              </li>
              <li>
                <p class="name"><?= esc($rz1['title']) ?></p>
                <p class="add"><?= esc($rz1['location']) ?></p>
              </li>
            </ul>
          </div>
        </div>
      </div>
      <div class="banner_sidebar learn-sidebar  aos-item" data-aos="slide-left">
        <h2 class="m-t-01">Ask our Specialist <span>+91 22 6172 5797</span> </h2>
        <div class="divider"></div>
        <h2>Or <a href="javascript:void(0);" class="hm-enq-btn">Enquire Now</a></h2>
      </div>
    </section>

    <script type="text/javascript" src="//platform-api.sharethis.com/js/sharethis.js#property=590aefde7db78e001128a326&product=inline-share-buttons"></script>
    <section class="franchise-wrp">
      <div class="container">
        <div class="aos-item find-office" data-aos="fade-up">
          <div class="share-print">
            <!--                    <p class="share">
                      <img src="<?php //echo base_url(); 
                                ?>assets/images/share-icon.png">
                      <span>Share</span>
					           <div class="sharethis-inline-share-buttons"></div>
                    </p>  -->

            <!-- AddToAny BEGIN -->
            <p class="share">
              <a class=" a2a_dd" href="https://www.addtoany.com/share">
                <img src="<?php echo base_url(); ?>assets/images/share-icon.png">
                <span>Share</span>
              </a>
            </p>
            <script>
              var a2a_config = a2a_config || {};
              a2a_config.linkname = "Coldwell";
              a2a_config.onclick = 1;
              a2a_config.num_services = 4;
              a2a_config.prioritize = ["facebook", "twitter", "google_plus", "linkedin"];
            </script>
            <script async src="https://static.addtoany.com/menu/page.js"></script>
            <!-- AddToAny END -->

            <p class="print" id="print_page">
              <img src="<?php echo base_url(); ?>assets/images/print-icon.png">
              <span>Print</span>
            </p>
          </div>
          <div class="top-wrp" id="top-wrp-print">
            <div class="wrp-01">
              <?php if ($rz1['image'] != "") { ?>
                <img src="<?php echo base_url(); ?>uploads/franchise_owners/<?= esc($rz1['image'], 'url') ?>">

                <p class="name"><?= esc($rz1['name']) ?></p>
                <p class="position">Director</p>
              <?php } ?>
              <style>
                @media print {

                  /* All your print styles go here */
                  #top-wrp-print {
                    display: flex;
                  }

                  .enquire-btn {
                    display: none !important;
                  }

                  .wrp-03 ul li:first-child {
                    display: none !important;
                  }

                  ul li {
                    list-style-type: none;
                    line-height: 30px;
                    margin: 0;
                    padding: 10px 0;
                    font-size: 18px;
                  }

                  ul li a {
                    color: #000;
                    text-decoration: none;
                    padding-left: 2%;
                    font-size: 22px;
                  }

                  p .name {
                    font-size: 25px;
                    font-weight: bold;
                  }

                  .wrp-01 img {
                    width: 25%;
                    border: 1px solid #999999;
                    border-radius: 100%;
                    padding: 3px;
                  }

                  .wrp-03 ul li p .title {
                    font-size: 22px;
                  }

                  .wrp-03 ul li p .para {
                    font-size: 18px;
                  }
                }
              </style>
            </div>
            <div class="wrp-02">
              <ul>
                <?php

                if ($rz1['website_url'] != "") {
                  $web_url = strstr($rz1['website_url'], '.');
                ?>
                  <li>
                    <img src="<?php echo base_url(); ?>assets/images/web-glob-icon.png">
                    <a href="<?= esc('http://www' . $web_url, 'attr') ?>" target="_blank"><?= esc($rz1['website_url']) ?></a>
                  </li>
                <?php }
                if ($rz1['email_id'] != "") {
                ?>
                  <li>
                    <img src="<?php echo base_url(); ?>assets/images/mail-icon.png">
                    <a href="mailto:<?= esc($rz1['email_id'], 'attr') ?>"><?= esc($rz1['email_id']) ?></a>
                  </li>
                <?php }
                if ($rz1['mobile_no'] != "") {
                ?>
                  <li>
                    <img src="<?php echo base_url(); ?>assets/images/phone-icon.png">
                    <a href="tel:<?= esc($rz1['mobile_no'], 'attr') ?>"><?= esc($rz1['mobile_no']) ?></a>
                  </li>
                <?php } ?>
                <li>
                  <p class="enquire-btn">Enquire Now</p>
                </li>
              </ul>
            </div>

            <div class="wrp-03">
              <ul>
                <?php if (!empty($rz1['logo'])) { ?>
                  <li>
                    <img src="<?= base_url() . 'uploads/franchises/' . esc($rz1['logo'], 'url') ?>">

                  </li>
                <?php } ?>
                <li>
                  <p class="title"><?= esc($rz1['title']) ?></p>
                  <?php
                  if (!empty($rz1['address'])) {
                  ?>
                    <p class="para"><?php echo $rz1['address']; /* admin rich HTML (WYSIWYG), raw as in CI3 */ ?></p>
                  <?php } ?>
                </li>

              </ul>
            </div>
          </div>
          <?php
          if (!empty($rz1['project_description'])) {
          ?>
            <div class="bottom-wrp">
              <p class="close-btn">Close</p>
              <p class="title">About Coldwell Banker <?= esc($rz1['title']) ?></p>
              <div class="para">
                <p><?php echo $rz1['project_description']; /* admin rich HTML (WYSIWYG), raw as in CI3 */ ?></p>
              </div>
              <p class="read-more"><span>+ Read More</span></p>
            </div>
          <?php } ?>
        </div>

        <div class="specialities-wrp">
          <div class="left">
            <?php
            if (!empty($rz1['office_specialities'])) {
            ?>
              <div class="top-wrp">
                <div>
                  <img src="<?php echo base_url(); ?>assets/images/star-01.png">
                </div>
                <div>
                  <p class="title">Services Offered</p>
                  <?php
                  $specialities = explode(",", $rz1['office_specialities']);
                  foreach ($specialities as $row) {
                  ?>
                    <p class="line">- <?= esc($row) ?></p>
                  <?php  } ?>
                </div>
              </div>

              <div class="bottom-wrp">
                <div>
                  <img src="<?php echo base_url(); ?>assets/images/location-01.png">
                </div>
                <div>
                  <p class="title">Speciality Markets</p>
                  <p class="line">- <?= esc($rz1['speciality_market']) ?></p>
                </div>
              </div>
            <?php } ?>
          </div>

          <div class="right">

            <!--                       <div class="video-player">
                        <a class="video" video-url="<?php //echo $rz1['video_url'];
                                                    ?>"><img src="<?php //echo base_url(); 
                                                                                                  ?>assets/images/videosample.png" alt=""/>
                        </a>
                      </div> -->
            <?php
            if (!empty($rz1['video_url'])) {

              $videos = explode(';', $rz1['video_url']);
              //print_r();
              foreach ($videos as $vid) {
            ?>
                <div class="video-player">
                  <a class="video" video-url="<?= esc($vid, 'attr') ?>">


                    <?php
                    $urlArr = explode("/", $vid);
                    $urlArrNum = count($urlArr);
                    $youtubeVideoId = $urlArr[$urlArrNum - 1];
                    $thumbURL = 'http://img.youtube.com/vi/' . $youtubeVideoId . '/0.jpg';
                    echo '<img src="' . esc($thumbURL, 'attr') . '"/>';
                    ?>

                  </a>
                </div>
            <?php }
            } ?>

          </div>
        </div>
        <?php
        if (!empty($testimonials)) {
        ?>
          <div class="testimonials-wrp">
            <div class="container">
              <p class="title">Testimonials</p>
              <div id="testi" class="owl-carousel owl-theme">
                <?php
                foreach ($testimonials as $testimonial) {
                  // CI3 fetched the image over HTTP (getimagesize(base_url() . ...)) and printed
                  // "Trying to access array offset on false" warnings when it was missing;
                  // the same file is checked on disk instead.
                  $mainImg = FCPATH . 'assets/images/testimonials/' . $rz1['id'] . '-' . $testimonial['id'] . '.jpg';
                  $img_size = is_file($mainImg) ? getimagesize($mainImg) : false;
                ?>
                  <div class="item">
                    <?php if ($rz1['id'] == 8 && ($img_size[0] ?? 0) > 0) { ?>
                      <div style="width:30%;float:left;">
                        <img src="<?php echo base_url(); ?>assets/images/testimonials/<?= esc($rz1['id'] . '-' . $testimonial['id'] . '.jpg', 'url') ?>" width="" />
                      </div>
                    <?php } ?>

                    <div <?php if ($rz1['id'] == 8 && ($img_size[0] ?? 0) > 0) { ?> style="width:70%;float:right;" <?php } ?>>
                      <img src="<?php echo base_url(); ?>assets/images/upper-quote.png">
                      <p class="para"><?php echo $testimonial['description']; /* admin rich HTML (WYSIWYG), raw as in CI3 */ ?></p>
                      <p class="position">
                        <i><?= esc($testimonial['name']) ?></i><?= esc($testimonial['designation']) ?>
                      </p>
                    </div>
                  </div>

              <?php }
              }
              ?>
              </div>
            </div>
          </div>
      </div>
    </section>
    <?php
    if (!empty($rz2)) { ?>
      <section class="business-owner-wrp">
        <div class="container">
          <p class="title">Business Owners</p>
          <div id="demo1" class="owl-carousel owl-theme">

            <?php $i = 0;
            foreach ($rz2 as $rz) { ?>
              <div class="item business-item" id="<?php if (!empty($rz['description'])) {
                                                    echo esc($rz['id'], 'attr');
                                                  } ?>">
                <!-- <a href="<?php echo base_url(); ?>uploads/franchise_owners/<?= esc($rz['image'], 'url') ?>" class="testi-popup">
                <img src="<?php echo base_url(); ?>uploads/franchise_owners/<?= esc($rz['image'], 'url') ?>">
                <p class="name"><?= esc($rz['name']) ?></p>
                <p class="position"><?= esc($rz['position_name']) ?></p>
              </a> -->
                <img src="<?php echo base_url(); ?>uploads/franchise_owners/<?= esc($rz['image'], 'url') ?>">
                <p class="name"><?= esc($rz['name']) ?></p>
                <p class="position"><?= esc($rz['position_name']) ?></p>
              </div>
            <?php  } ?>
          </div>
        </div>
      </section>
    <?php } ?>

    <?php
    if (!empty($rz4)) { ?>
      <section class="business-owner-wrp real-estate-wrp">
        <div class="container">
          <p class="title">Real Estate Specialists</p>
          <div id="demo2" class="owl-carousel owl-theme">


            <?php $i = 0;
            foreach ($rz4 as $rz) { ?>
              <div class="item real-estate-item" id="<?= esc($rz['id'], 'attr') ?>">
                <!-- <a href="<?php echo base_url(); ?>uploads/specialist/<?= esc($rz['image'], 'url') ?>" class="testi-popup1">
                                  <img src="<?php echo base_url(); ?>uploads/specialist/<?= esc($rz['image'], 'url') ?>">
                                  <p class="name"><?= esc($rz['name']) ?></p>
                                  <p class="position"><?= esc($rz['title']) ?></p>
                              </a> -->
                <img src="<?php echo base_url(); ?>uploads/specialist/<?= esc($rz['image'], 'url') ?>">
                <p class="name"><?= esc($rz['name']) ?></p>
                <p class="position"><?= esc($rz['title']) ?></p>
              </div>
            <?php } ?>

          </div>
        </div>
      </section>
    <?php } ?>

    <?php
    if (!empty($rz1['location_description'])) {
      // CI3 also looked up the city name here ($rz3) but never used it; the query is dropped.
    ?>
      <section class="neighbourhood-wrp">
        <div class="container">
          <p class="title">About <?= esc($rz1['location']) ?> </p>
          <p class="para"><?php echo $rz1['location_description']; /* admin rich HTML (WYSIWYG), raw as in CI3 */ ?></p>
        </div>
      </section>
      <hr>
    <?php } ?>

    <div class="clear"></div>
    <?= $this->include('partials/sub_footer') ?>
  </article>
  <section class="lightbox_bg agent-form" id="disclaim" style="display: none">
    <div class="light_inner">
      <div class="disc-wrap">
        <a href="javascript:void()" class="close_popup">X</a>
        <h2>Drop in your contact details</h2>
        <div class="message <?php if ($flash_class != '') {
                              echo esc($flash_class, 'attr');
                            } ?>"><?php if ($flash_message != "") {
                                                                                                                              echo esc($flash_message);
                                                                                                                            } ?></div>
        <form id="franchisee_contact" method="post" action="<?php echo base_url(); ?>franchisee_details_enquiry/<?= esc($rz1['id'], 'url') ?>">
          <?= csrf_field() ?>
          <div class="form-row"><input type="text" name="fname" id="fname" placeholder="First Name"></div>
          <div class="form-row"><input type="text" name="lname" id="lname" placeholder="Last Name"></div>
          <div class="form-row"><input type="text" name="email" id="email" placeholder="Email"></div>
          <div class="form-row"><input type="text" name="phone" id="phone" placeholder="Phone"></div>
          <div class="form-row-last"><span class="rdolbl">I am interested in</span>
            <input type="checkbox" name="interest[]" class="interest" value="Buying"> Buying
            <input type="checkbox" name="interest[]" class="interest" value="Selling"> Selling
            <input type="checkbox" name="interest[]" class="interest" value="Renting"> Renting
          </div>
          <span id="enquire_popup_response1"></span>
          <input type="submit" name="" id="index_submit" value="Send Enquiry" class="sub-btn">
        </form>
      </div>
    </div>
  </section>

  <section class="lightbox_bg video-popup" id="disclaim" style="display: none">
    <div class="light_inner">
      <div class="video-wrap ">
        <a href="javascript:void()" class="close_popup">X</a>

        <iframe width="560" height="315" id="iframe_video" src="https://www.youtube.com/embed/PDugSNuQYRY" frameborder="0" allowfullscreen></iframe>

      </div>
    </div>
  </section>









  <?= $this->include('partials/footer') ?>
  <section class="lightbox_bg" id="franchisee-details-iframe" style="display:none;">
    <div class="light_inner">
      <div class="corporate_team_lightbox">
        <a href="javascript:void()" class="close_popup">X</a>
        <div id="team_html">
          <div class="team_mem"><img src="<?php //echo base_url(); 
                                          ?>assets/images/shiraz-padhihar.jpg"></div>
          <div class="member_details">
            <h2>Shiraz Padhiar<br> <span>Business Head</span></h2>
            <div class="light_divider"></div>
            <p>He is an IT graduate with Masters in International Business & Marketing from University of East London,UK. Having 12+ years of Professional experience with Various MNC companies like Tesco, Telewest UK, IBM, Spice Digital, Utopia Mobile , Remax , Square Yards. He has Professional Work experience in Different Cities like Dubai, London, Banglore, Mumbai, Ahmedabad. He believes that a Successful real estate transaction depends upon your depth knowledge about Product, Ethics towards client, and identification of proper need of a client. Now He is handling a Business Head profile at Coldwell Banker Kd properties</p>
            <div class="light_divider"></div>
          </div>
        </div>
      </div>
    </div>
  </section>



  <script type="text/javascript">
    $('#demo1').owlCarousel({
      singleItem: true,
      items: 3,
      loop: false,
      nav: true,
      dots: true,
      margin: 40,
      dotData: true,
      lazyLoad: true,
      //datainterval:false,
      //title:false,
      //dotsContainer: '#carousel-custom-dots', 
      // scrollPerPage : true,
      //  slideBy: 1,
      //  center: true,
      pagination: false,
      autoplay: false,
      autoplayTimeout: 5000,
      autoplayHoverPause: true,
      smartSpeed: 1500,
      transitionStyle: "fadeUp",
      startPosition: 'URLHash',
      URLhashListener: true,
      useMouseWheel: true,
      responsive: {
        0: {
          items: 1
        },
        600: {
          items: 3
        },
        1000: {
          items: 3
        }
      }
    });
    $('#testi').owlCarousel({
      singleItem: true,
      items: 1,
      loop: false,
      nav: false,
      dots: true,
      margin: 0,
      dotData: true,
      lazyLoad: true,
      pagination: false,
      autoplay: false,
      autoplayTimeout: 5000,
      autoplayHoverPause: true,
      smartSpeed: 1500,
      transitionStyle: "fadeUp",
      startPosition: 'URLHash',
      URLhashListener: false,
      useMouseWheel: false,
      pagination: false
    });

    $(".testi-popup").click(function() {
      $.fancybox({
        'padding': 0,
        'autoScale': false,
        'transitionIn': 'none',
        'transitionOut': 'none',
        'title': this.title,
        'width': 640,
        'height': 385,
        'href': this.href.replace(new RegExp("watch\\?v=", "i"), 'v/'),
        'type': 'swf',
        'swf': {
          'wmode': 'transparent',
          'allowfullscreen': 'true'
        }
      });

      return false;
    });








    $('#demo2').owlCarousel({
      singleItem: true,
      items: 3,
      loop: false,
      nav: true,
      dots: true,
      margin: 40,
      dotData: true,
      lazyLoad: true,
      //datainterval:false,
      //title:false,
      //dotsContainer: '#carousel-custom-dots', 
      // scrollPerPage : true,
      //  slideBy: 1,
      //  center: true,
      pagination: false,
      autoplay: false,
      autoplayTimeout: 5000,
      autoplayHoverPause: true,
      smartSpeed: 1500,
      transitionStyle: "fadeUp",
      startPosition: 'URLHash',
      URLhashListener: true,
      useMouseWheel: true,
      responsive: {
        0: {
          items: 1
        },
        600: {
          items: 3
        },
        1000: {
          items: 3
        }
      }
    });

    $(".testi-popup1").click(function() {
      $.fancybox({
        'padding': 0,
        'autoScale': false,
        'transitionIn': 'none',
        'transitionOut': 'none',
        'title': this.title,
        'width': 640,
        'height': 385,
        'href': this.href.replace(new RegExp("watch\\?v=", "i"), 'v/'),
        'type': 'swf',
        'swf': {
          'wmode': 'transparent',
          'allowfullscreen': 'true'
        }
      });

      return false;
    });

    $('.franchise-wrp .find-office .bottom-wrp .read-more span').click(function() {
      if ($('.franchise-wrp .find-office .bottom-wrp').hasClass('active')) {
        $('.franchise-wrp .find-office .bottom-wrp').removeClass('active')
        $('.franchise-wrp .find-office .bottom-wrp .read-more span').text('+ Read More');
        $('.franchise-wrp .find-office .bottom-wrp .close-btn').css('display', 'none');
      } else {
        $('.franchise-wrp .find-office .bottom-wrp').addClass('active')
        $('.franchise-wrp .find-office .bottom-wrp .read-more span').text('- Minimize');
        $('.franchise-wrp .find-office .bottom-wrp .close-btn').css('display', 'block');
      }
    })

    $('.franchise-wrp .find-office .bottom-wrp .close-btn').click(function() {
      $(this).css('display', 'none');
      $('.franchise-wrp .find-office .bottom-wrp').removeClass('active')
      $('.franchise-wrp .find-office .bottom-wrp .read-more span').text('+ Read More');
    })

    $(".close_popup").click(function() {
      $(".agent-form").hide();
    });

    $(".enquire-btn").click(function() {
      $(".agent-form").show();
    });
    $(".close_popup").click(function() {
      $(".video-popup").hide();
    });

    $(".video").click(function() {
      //alert($(this).attr('video-url')); 
      $("#iframe_video").attr("src", $(this).attr('video-url'))
      $(".video-popup").show();
    });
  </script>

  <script>
    function printDiv() {
      //alert();

      var divToPrint = document.getElementById('top-wrp-print');

      var newWin = window.open('', 'Print-Window');

      newWin.document.open();

      newWin.document.write('<html><body onload="window.print()">' + divToPrint.innerHTML + '</body></html>');

      newWin.document.close();

      setTimeout(function() {
        newWin.close();
      }, 10);

    }


    $(document).ready(function() {
      $(document).on("click", "#print_page", function() {
        printDiv();
      });

      // $(".member_details").mCustomScrollbar();
      $(document).on("click", ".business-item", function() {
        var id = $(this).attr("id");
        //alert(id)
        if (id != "") {
          $("#franchisee-details-iframe").hide();
          $.ajax({
            url: baseUrl + 'get_owner_info',
            type: 'POST',
            data: {
              user_id: id
            },
            success: function(response) {
              //console.log(response);
              $("#franchisee-details-iframe").show();
              $("#team_html").empty();
              $("#team_html").html(response);
              $('#franchisee-details-iframe').show();
            },
            error: function(jqXHR, textStatus, errorThrown) {
              alert('Some error occurred');
            }
          });
        }

      });
      $(document).on("click", ".real-estate-item", function() {
        var id = $(this).attr("id");
        if (id != "") {

          $("#franchisee-details-iframe").hide();
          $.ajax({
            url: baseUrl + 'get_specialist_info',
            type: 'POST',
            data: {
              user_id: id
            },
            success: function(response) {
              console.log(response);
              $("#franchisee-details-iframe").show();
              $("#team_html").empty();
              $("#team_html").html(response);
              $('#franchisee-details-iframe').show();
            },
            error: function(jqXHR, textStatus, errorThrown) {
              alert('Some error occurred');
            }
          });
        }

      });
      $('.close_popup').click(function() {
        $('#corporate_team_iframe').hide();
      });
    });
  </script>
</body>

</html>