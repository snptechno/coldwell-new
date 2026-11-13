<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery-1.11.0.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/owl.carousel.min.js"></script>    
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/css3-animate-it.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery.sliding_menu.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/fancybox-min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery.smoothZoom.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/custom.js"></script>
<script src="<?php echo base_url(); ?>assets/build/js/utils.js"></script>
<script src="<?php echo base_url(); ?>assets/build/js/intlTelInput.js"></script>
<script src="<?php echo base_url(); ?>assets/js/jquery.validate.js"></script>
<script src="<?php echo base_url(); ?>assets/js/form.js"></script>

<script>
$(document).ready(function(){
	setTimeout(function() {
		$('form :input[name=phone]').each(function(index) {
			$(this).intlTelInput({
				//defaultCountry: "auto",
					geoIpLookup: function(callback) {
						$.get("https://crm.netbizlabs.com/campaign/ip2nation").always(function(resp) {
							//console.log(resp);
							callback(resp);
						});
					},
					initialCountry: "auto",
					//nationalMode: false,
					//autoHideDialCode: false,
					utilsScript: baseUrl+"assets/build/js/utils.js"
				});
		});
	}, 1000);
});
</script>
<script type="text/javascript" >
  function setCookie(cname, cvalue, exdays) {
        var d = new Date();
        d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
        var expires = "expires="+d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }

    function getCookie(cname) {
        var name = cname + "=";
        var ca = document.cookie.split(';');
        for(var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
        return "";
    }
   $(document).ready(function($){
    var user = getCookie("agreecheck");
        if (user != "") {
            $('.disclaimer-wrapper').hide();
        } 
        else{
          $('.disclaimer-wrapper').show();
          
        }

    $('#disclaimer').click(function(){
           $('.disclaimer-wrapper').hide();
           setCookie("agreecheck", "set", 365);
        });
      });  
</script>
