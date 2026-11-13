
AOS.init({easing: 'ease-in-out-sine'});

if ( $(window).width() <= 1024 ) {
    $("section, div, a.view-proj, h2, h3, aside, form, ul, li, span").removeAttr ("data-aos");
}
$('#menu ul').sliding_menu_js({});

$('body').prepend('<a href="javascript:void()" class="back-to-top">Back to Top</a>');
var amountScrolled = 300;
$(window).scroll(function () {
    if ($(window).scrollTop() > amountScrolled) {
        $('a.back-to-top').fadeIn('slow');
    } else {
        $('a.back-to-top').fadeOut('slow');
    }
});
$('a.back-to-top').click(function () {
    $('html, body').animate({
        scrollTop: 0
    }, 700);
    return false;
});

// $('#search_result ul li').click(function(){
//     console.log('click')
//     var search_text = $(this).text();
//     $('#search_by_name').text(search_text);
// });

$(document).ready(function() {

    if($('.bottom-wrp .para').height() > 80){
        // $('.franchise-wrp .find-office .bottom-wrp .close-btn').show();
        $('.franchise-wrp .find-office .bottom-wrp .read-more').show();
    }else{
        $('.franchise-wrp .find-office .bottom-wrp .close-btn').hide();
        $('.franchise-wrp .find-office .bottom-wrp .read-more').hide();
    }


if ($(document).width() <= 480 ) {
    $('img.ramn').attr("src","http://coldwellbanker.netbizlabs.com/assets/images/ramnik-mob.jpg")
}

$('.slide-txt').css({"margin-top": $('header').innerHeight()})
$('.inner_banner_container .banner_sidebar').css({"height": $('.inner_banner').height()})
$('.frnc_right').css({"min-height": $('.frnc-wrap').height()})

$("#owl_demo1").owlCarousel({
    nav: true,
    items: 1,
    loop: true,
    autoplay: true,
});

$('#gallery').owlCarousel({
    loop:false,
    nav: true,
    margin: 30,
    items: 4,
    responsive:{
        0:{
            items:1
        },
        480:{
            items:2
        },
        800:{
            items:4,
            nav: true,
        }
    }
});

$('#testimonials').owlCarousel({
    loop:false,
    nav: false,
    dots: true,
    margin: 10,
    items: 1,
    singleitem: true,   
});

$('#nri-events').owlCarousel({
    loop:false,
    nav: true,
    dots: false,
    margin: 40,
    items: 2,
    responsive:{
        0:{
            items:1
        },
        801:{
            items:2
        }
    }
});

$('#testimonial-gallery').owlCarousel({
    loop:false,
    autoplay: true,
    nav: true,
    margin: 10,
    items: 4,
    responsive:{
        0:{
            items:1
        },
        480:{
            items:2
        },
        800:{
            items:4
        }
    }
});

$('#success-stories').owlCarousel({
    loop:false,
    autoplay: true,
    nav: true,
    margin: 10,
    items: 4,
    responsive:{
        0:{
            items:1
        },
        480:{
            items:2
        },
        800:{
            items:3
        }
    }
});

$('.fancybox').fancybox({'loop': false, });

$(function() {
    var GammaSettings = {
            viewport : [ {
                width : 1200,
                columns : 5
            }, {
                width : 900,
                columns : 4
            }, {
                width : 500,
                columns : 3
            }, { 
                width : 320,
                columns : 2
            }, { 
                width : 0,
                columns : 2
            } ]
    };
});


    if($(window).width() > 480){
        if($('#demo1 .owl-item').length > 3 ){
            console.log('length')
            $('#demo1 .owl-nav').show();
        }
        else{
            $('#demo1 .owl-nav').hide();
        }

        if($('#demo2 .owl-item').length > 3 ){
            console.log('length')
            $('#demo2 .owl-nav').show();
        }
        else{
            $('#demo2 .owl-nav').hide();
        }
    }else{
        if($('#demo1 .owl-item').length > 1 ){
            console.log('length')
            $('#demo1 .owl-nav').show();
        }
        else{
            $('#demo1 .owl-nav').hide();
        }

        if($('#demo2 .owl-item').length > 1 ){
            console.log('length')
            $('#demo2 .owl-nav').show();
        }
        else{
            $('#demo2 .owl-nav').hide();
        }
    }

$(".menu_icon").click(function(){	
	$("#menu").addClass("big_form");
	$(".enquire_form").slideUp();
	$(".black_overlay").show();
});

$("#menu_close,.black_overlay").click(function(){	
	$("#menu").removeClass("big_form");
	$(".black_overlay").hide();
});


$("#form_close").click(function(){	
		$(".enquire_form").slideUp();
});

$(".enquire_btn").click(function(){	
	$(".enquire_form").slideDown();
});


//     $('.video-colleft .play-icon').click(function() {
//         $('.video-colleft .play-icon').show();
//         $('.video-colleft video').trigger('pause').removeAttr('controls');
//         $(this).parent().find("video").trigger('play').attr('controls', 'controls');
//         $(this).hide();
// });   


    $('.video-colleft,.play-icon').click(function() {
        $('#video_iframe').show();        
    });    

    $('.close_popup').click(function() {
        $('#video_iframe,#corporate_team_iframe').hide();        
    }); 
    
    /*$('.team_img').click(function() {
        $('#corporate_team_iframe').show();        
    }); */

    $('.t-item').click(function() {
        $('#franchisee-details-iframe').show();        
    }); 
     

/*    $('.apply-now').click(function() {
        $('#apply-now-frame').show();        
    }); */
    
    $('.disclaimer-btn').click(function() {
        $('#disclaim').show();        
    }); 

    $('.close_popup').click(function() {
        $('#apply-now-frame, #franchisee-details-iframe, #disclaim').hide();        
    });

    $('.hm-enq-btn').click(function() {
        $('#home-enquire').show();        
    }); 

    $('.close-home-enq').click(function() {
        $('#home-enquire').hide();        
    });



    if ($('.acc-box').length) {
    var $box = $('.acc-box');
    $box.each(function() {
        var $trigger = $('.acc-trigger', $(this));
        
        $trigger.on('click', function() {
            var $this = $(this);
            if ($this.data('mode') === 'toggle') {
                $this.toggleClass('active').next().stop(true, true).slideToggle(300);
            } else {
                if ($this.next().is(':hidden')) {
                    $trigger.removeClass('active').next().slideUp(300);
                    $this.toggleClass('active').next().slideDown(function(){
                        $this.find(".acc-trigger").attr("class","active");
                        $("html, body").animate({
                          scrollTop: $this.offset().top
                        },300);
                 });
                } else if ($this.hasClass('active')) {
                    $this.removeClass('active').next().slideUp(300);
                }
            }
            return false;        
        });
    });
    }

});

$(window).scroll(function(){

    if($("body").scrollTop() >= $(".inner_banner_container").innerHeight()){
    $(".our-franch .sub-nav, #Learning .sub-nav").addClass("sticked");
    }else {
        $(".our-franch .sub-nav, #Learning .sub-nav").removeClass("sticked");
    }


    if($('body').scrollTop() >= $("#overview1").offset().top){
        $('#Learning .sub-nav li a').removeClass('active');
        $('#Learning .sub-nav li:first-child a').addClass('active');
    }

    if($('body').scrollTop() >= $("#learn-university1").offset().top){
        $('#Learning .sub-nav li a').removeClass('active');
        $('#Learning .sub-nav li:nth-child(2) a').addClass('active');
    }

    if($('body').scrollTop() >= $("#learn-events1").offset().top){
        $('#Learning .sub-nav li a').removeClass('active');
        $('#Learning .sub-nav li:nth-child(3) a').addClass('active');
    }

    if($('body').scrollTop() >= $("#learn-gallery1").offset().top - 80){
        $('#Learning .sub-nav li a').removeClass('active');
        $('#Learning .sub-nav li:nth-child(4) a').addClass('active');
    }
})

// $(window).scroll(function() {
// var target = this.hash; // gets the #hash
//         $target = $(target); //
//         $('html, body').stop().animate({
//             'scrollTop': $target.offset().top+2 // scrolls to the link
//         }, 500, 'swing', function () {
//             window.location.hash = target;
//             $(document).on("scroll", onScroll);
//         });
//     });

//         function onScroll(event){
//         var scrollPosition = $(document).scrollTop();
//         $('ul li a').each(function () {
//             var currentLink = $(this);
//             var refElement = $(currentLink.attr("href"));
//             if (refElement.position().top <= scrollPosition && refElement.position().top + refElement.height() > scrollPosition) {
//                 $('ul li a.active').removeClass('active');
//                 $(this).addClass('active');
//             }
//             else{
//                 currentLink.removeClass("active");
//             }
//         });
//     }



$("#Learning .sub-nav li a").click(function() {
  $("#Learning .sub-nav li a").removeClass("active");
  $(this).addClass("active");
})

$(function() {
  $('a[href*="#"]:not(.our-franch .sub-nav li a)').click(function() {
      var target = $(this.hash);
      target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
      if (target.length) {
        $('html, body').animate({
          scrollTop: target.offset().top
        }, 1000);
        return false;
      }
    });
});

function openCity(cityName,id) {
    var i;
    var x = document.getElementsByClassName("city");
    for (i = 0; i < x.length; i++) {
        x[i].style.display = "none"; 
    }
    var y = document.getElementsByClassName("not_active");
    for (i = 0; i < y.length; i++) {
        y[i].style.background = "#0077B9"; 
    }
    document.getElementById(cityName).style.display = "block"; 
    document.getElementById(id).style.background = "#5595F7"; 
    console.log(id);
}

$(".our-franch .sub-nav li a").click(function() {
  $(".our-franch .sub-nav li a").removeClass("active");
  $(this).addClass("active");
   var target = $(this.hash);
   target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
   if (target.length) {
        $('html, body').animate({
          scrollTop: target.offset().top - 100
        }, 1000);
        return false;
    }
})

$(".our-franch .sub-nav li:first-child a").click(function() {
   var target = $(this.hash);
   target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
   if (target.length) {
        $('html, body').animate({
          scrollTop: target.offset().top - 2
        }, 1000);
        return false;
    }
})

$(window).load(function(){
    $('.slide-txt').css({"margin-top": $('header').innerHeight()})
    $('.inner_banner_container .banner_sidebar').css({"height": $('.inner_banner').height()})
    $('.frnc_right').css({"min-height": $('.frnc-wrap').height()})
      
})

   function openCity(evt, cityName) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    document.getElementById(cityName).style.display = "block";
    evt.currentTarget.className += " active";
    }

    document.getElementById("defaultOpen").click();


// if('project_description').html() == "") {
//     $('.frnc_left h2').hide();
// }


if ($(document).width() <= 480 ) {
    $('img.ramn').attr("src","http://coldwellbanker.netbizlabs.com/assets/images/ramnik-mob.jpg")
}