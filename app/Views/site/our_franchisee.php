<!doctype html>
<html>

<head>
    <title><?= esc($page_info->title) ?></title>
	<meta name="description" content="<?= esc($page_info->meta_description, 'attr') ?>" />
	<meta name="keywords" content="<?= esc($page_info->meta_keywords, 'attr') ?>" />
	<?= $this->include('partials/header') ?>
	  <?= $this->include('partials/ga_code_header') ?>
</head>

<body class="franchise-filter">
    <article class="aos-all">
        <section>
            <div class="container">
                <?php $this->setVar('act', $page_active); // CI3: $data1['act'] for the navigation partial ?><?= $this->include('partials/navigation_w') ?>
            </div>
        </section>
        <section class="inner_banner_container">
            <div class="main-frnachisee-bg">
<!--                 <div class="slide-txt">
                	<ul>
                		<li>
                			<img src="<?php //echo base_url(); ?>assets/images/cb-white-icon-01.png">
                		</li>
                		<li>
                			<p class="name">Anjaneya Spaces</p>
                			<p class="add">Chembur</p>
                		</li>
                	</ul>
                </div> -->
            </div>
        </section>
        	<?php
        	/*-------------------------get all city------------------*/
			// $rz1 (cities with active franchises) and $clean (the slug helper the
			// CI3 view declared here) are passed in by Site\Franchise::ourFranchise().
			/*------------------------------------------------------*/
        	?>

        <section class="filter-wrp">
            <div class="container">
                <div class="aos-item find-office" data-aos="fade-up">
                  <p class="p1">Find an office</p>
                  <div class="form-wrp">
                    <form id="search_by_location">
                      <div class="select-wrp city-wrp">
                        <select id="city" name="city">
							<option value="">- Select City -</option>
							<?php foreach ($rz1 as $city) { ?>
							<option value="<?= esc($city['id'], 'attr') ?>"><?= esc($clean($city['city_name'])) ?></option>
							<?php } ?>
                        </select>
                      </div>
                      <div class="select-wrp area-wrp">
                        <select id="location" name="location">
                          <option>- Select Area -</option>
                        </select>
                      </div>
                      <input name="button" type="button" value="Find Now" id="find_now">
                    </form>
                  </div>
                  <span class="line"></span>
                  <div class="search-box-wrp">
                  	<!-- <input id="search_by_name" name="search_by_name" type="text" placeholder="Search" class="search"> -->
                  	<form id="search_by" name="search_by">
		       			<input type="textbox" id="search_by_name" name="search_by_name" placeholder="Search by name"  class="search">
		       			<input type="button" name="search_btn" id="search_btn" class="form-control btn">
		       		</form>
		       		<div class="" id="search_result">
		       		</div>
                  </div>
                </div>
            </div>
        </section>

        <section class="filter-result aos-item"data-aos="fade-up">
            <div class="container">
              <div class="result-head">
                <p class="heading"></p>
                <div class="change-view">
                  <div class="grid">
                    <img src="<?php echo base_url(); ?>assets/images/grid-view-icon.png">
                    <p>Grid View</p>
                  </div>
                  <div class="map">
                    <img src="<?php echo base_url(); ?>assets/images/location-icon-blue.png">
                    <p>Map View</p>
                  </div>
                </div>
              </div>
            </div>
        </section>

        <!-- <div class="container no-padding col-10" >
        	<?php
        	/*-------------------------get all city------------------*/
			/*------------------------------------------------------*/
        	?>
       		<form id="search_by_location" >
       			<select id="city" name="city" class="form-control" style="width:100px;padding:5px">
       				<option value="">Select City</option>
       				<?php foreach ($rz1 as $city) { ?>
       					<option value="<?= esc($city['id'], 'attr') ?>"><?= esc($clean($city['city_name'])) ?></option>
       				<?php } ?>

       			</select>
       			<select id="location" name="location" class="form-control" style="width:100px;padding:5px">
       				<option value="">Select Location</option>
       			</select>
       			<input type="button" name="find_now" id="find_now" placeholder="Find Now" value="Find Now" class="form-control btn" style="width:100px;padding:5px; ">
       		</form>
       		<br/><br/>

       		<form id="search_by">
       			<input type="textbox" id="search_by_name" name="search_by_name" placeholder="search" style="width:300px;padding:5px">
       			<input type="button" name="search_btn" id="search_btn" class="form-control btn" style="width:100px;padding:5px; ">
       		</form>	
        </div>	 -->

        <section class="our-franch" id="franchisee">
            <div class="container no-padding">
            	<div class="grid-view-wrp active">
				<?php
				// $rz2: cities with active franchises; $franchises_by_city[city id]: that city's
				// active franchises by title (Site\Franchise::ourFranchise()).
				if(!empty($rz2)){
					$i=1;
					foreach($rz2 as $rz) {?>
						<div class="our-frnch-main" id="list_fra">
							<div class="ahm-box " city-name="<?php echo 'city_'.$i; ?>" id="<?= esc($clean($rz['city_name']), 'attr') ?>">
								<h3><span class="span<?php if (($i % 2) == 1){echo '2';}else{echo '1';}?>"><?= esc($rz['city_name']) ?></span></h3>
								<?php
								$rz3 = $franchises_by_city[$rz['id']] ?? [];
								$array_part = array_chunk($rz3, 4);
								$j=0;
								foreach ($array_part as $row) {?>
									<div class="itms-row">
								<?php if ($j == 0){ ?>
										<div class="top-rod"></div>
								<?php }else{ ?>
										<div class="top-rod m-t-40"></div>
								<?php	} 
									foreach ($row as $line) {
								?>		
											<div class="frch-item">
												<div class="item-tit">
													<img src="<?php echo base_url(); ?>assets/images/cb-white-icon.png">
													<span><?= esc($line['title']) ?></span>
													<small><?= esc($line['location']) ?></small>

													<?php if($line['franchise_status']=="Coming Soon"){ ?>
														<span class="cm-soon">Coming Soon</span>
													<?php }

													 if($line['franchise_status']=="External Link"){ ?>
														<a href="<?= esc($line['external_link'], 'attr') ?>" target="_blank"></a>
													<?php }
													$title = $clean(strtolower($line['title']));
													$fran_name = str_replace(array('\'', ' '),'-',$title);
													if($line['franchise_status']=="Exits"){ ?>
														<a href="<?php echo base_url(); ?>our-franchise-details/coldwell-banker-<?= esc($fran_name, 'url') ?>/<?= esc($line['id'], 'url') ?>"></a>
													<?php } ?>
												</div>
											</div>
								<?php
									}
								?>
									</div>
								<?php 
									$j=$j+1;
								}?>
							</div>
						</div>
						<div class="clear"></div>
					
				<?php
					$i=$i+1;}
				}
				?>
				</div>
				<input type="hidden" id="map_loc" name="map_loc">
				<input type="hidden" id="action_type" name="action_type">
                
                <div class="map-view-wrp ">
					<div id="map" style="height: 400px;">  <!-- class="sub-nav" --> 

					</div>
                </div>
                <!-- <span id="franchisee_form" class="blue-btn aos-item"data-aos="zoom-in">I am interested in real estate franchise</span> -->
            	
            </div>
        </section>
        <div class="clear"></div>

        <section class="still-queries-wrp">
        	<div class="container">
        		<p class="title">Still have queries?</p>
        		<div>
        			 <div class="home_bottom_section">
		                <h4>Our expert advisors are just a phone call away.</h4>
		                <span><!-- Call --> <img src="<?php echo base_url(); ?>assets/images/call-icon.png" alt="" style="vertical-align: top"> +91-8587819200
		                <small></small></span>
		            </div>
        			<div class="franchisee-form" id="franchisee_form">
        				<p class="">Request a Call Back</p>
						<div class="message <?php if($flash_class!=''){ echo esc($flash_class, 'attr');}?>"><?php if($flash_message!=""){ echo esc($flash_message);}?></div>
						<form id="franchiseeForm" method="post" action="<?php echo base_url(); ?>franchisee_enquiry">
<?= csrf_field() ?>
							<div class="form-rw">
								<input type="text" name="name" id="fr_name" placeholder="Name*">
							</div>
							<div class="form-rw">
								<input type="text" name="email" id="fr_email" placeholder="Email*">
							</div>
							<div class="form-rw">
								<input type="text" name="phone" id="fr_phone" placeholder="Phone*">
							</div>
							<div class="form-rw">
								<p class="all-field" style="font-size: 10px;">*All Fields mandatary.</p>
								<input type="submit" name="" id="" value="SEND REQUEST">
							</div>
						</form>
                	</div>
        		</div>
        	</div>
        </section>
        <div class="clear"></div>
        
		<?= $this->include('partials/sub_footer') ?>

    </article>
    <?= $this->include('partials/footer') ?>
<script>
$(document).ready(function(){
	//var load_view_type = "onload";
	
	//$('.change-view .map').hide();

	$('.change-view .grid').click(function(){
		$('.grid-view-wrp').addClass('active');
		$('.map-view-wrp').removeClass('active');
	})

	$('.change-view .map').click(function(){
		$('.map-view-wrp').addClass('active');
		$('.grid-view-wrp').removeClass('active');

		var type = $("#action_type").val();
		
		if(type!=""){
			map_view(type);
	    }
	})



	var base_url = "<?php echo base_url();?>";

	$.ajax({
	type: "POST",
	url: base_url+"get_all_franchisee_location",
	success: function(response){

		var obj = $.parseJSON(response);
		$("#map_loc").val(obj['map_arr']);
		$("#action_type").val(obj['action'][0]);
		var type = $("#action_type").val();
		/*console.log(type);*/
		if(type!=""){
			map_view(type);
		}
	}
	});

/*------------------------get location---------------------*/
	$(document).on("change","#city", function(){
		var city_id = $(this).val();

		if(city_id!=''){
			$.ajax({
				type: "POST",
				url: base_url+"get_franchisee_location_by_city",
				data: "city_id="+city_id,
				success: function(response){
					//location.href="get_ads.php";
					$("#location").html(response);
				}
			});
		}
	}); 
/*---------------------------------------------------------*/

/*--------------search by location and city---------------*/
	$(document).on("click","#find_now", function(){

		var city_id = $("#city").val();
		var location_name = $("#location").val();
		var city_error = false;
		var location_error = false;

		if(city_id!=""){
			$("city_error").html("");
			city_error = false;
		}else{
			$("city_error").html("Please select city");
			city_error = true;
		}

		if(location_name!=""){
			$("location_error").html("");
			location_error = false;
		}else{
			$("location_error").html("Please select franchises location");
			location_error = true;
		}
		var test = 1;
		if(city_error==false){
			if(test == 1){
				$.ajax({
					type: "POST",
					url: base_url+"search_franchisee_by_location",
					data: "search_id=1&city_id="+city_id+'&location_name='+location_name,
					success: function(response){
						test = test + 1;
						var obj = $.parseJSON(response);

						$("#franchisee .grid-view-wrp").html("");
						$("#franchisee .grid-view-wrp").html(obj['info']);
						$("#map_loc").val(obj['map_arr']);
						console.log(obj);

						$("#action_type").val(obj['action'][0]);
						var type = $("#action_type").val();
						if(type!=""){
							map_view(type);
						}
						$('.change-view .map').show();
							$('html, body').animate({
							scrollTop: $(".filter-wrp").offset().top
						}, 2000);
							
					}
				});
			}
		}else{
			return false;
		}

	}); 
/*--------------------------------------------------------*/
	$(document).on("click","#search_btn", function(){
		var search_by = $("#search_by_name").val();
		var search_by_error = false;

		if(search_by!=""){
			$("search_by_error").html("");
			search_by_error = false;
		}else{
			$("search_by_error").html("Please select city");
			search_by_error = true;
		}

		if(search_by_error==false){
			$.ajax({
				type: "POST",
				url: base_url+"search_franchisee_by_location",
				data: "search_id=2&search_by="+search_by,
				success: function(response){
					
					var obj = $.parseJSON(response);

					$("#franchisee .grid-view-wrp").html("");
					$("#franchisee .grid-view-wrp").html(obj['info']);
					$("#map_loc").val(obj['map_arr']);

					$("#action_type").val(obj['action'][0]);
					var type = $("#action_type").val();

					if(type!=""){
						map_view(type);
					}
				    $('html, body').animate({
					    scrollTop: $(".filter-wrp").offset().top
					 }, 2000);
					// map_view();
				}
			});
			return false;
		}else{
			return false;
		}

	});

	$(document).on("submit","#search_by", function(){
		var search_by = $("#search_by_name").val();
		var search_by_error = false;

		if(search_by!=""){
			$("search_by_error").html("");
			search_by_error = false;
		}else{
			$("search_by_error").html("Please select city");
			search_by_error = true;
		}

		if(search_by_error==false){
			$.ajax({
				type: "POST",
				url: base_url+"search_franchisee_by_location",
				data: "search_id=2&search_by="+search_by,
				success: function(response){
					
					
					var obj = $.parseJSON(response);

					$("#franchisee .grid-view-wrp").html("");
					$("#franchisee .grid-view-wrp").html(obj['info']);
					$("#map_loc").val(obj['map_arr']);

					$('.change-view .map').show();
					$("#action_type").val(obj['action'][0]);
					var type = $("#action_type").val();

					if(type!=""){
						map_view(type);
					}
				    $('html, body').animate({
					    scrollTop: $(".filter-wrp").offset().top
					 }, 2000);
					// map_view();
				}
			});
			return false;
		}else{
			return false;
		}

	});

	$(document).on("keyup","#search_by_name", function(){
		var scroll_h = $('.inner_banner_container').height() + $('.filter-wrp').height();

		var search_by = $("#search_by_name").val();
		if(search_by!=""){
			//alert(search_by);
			$.ajax({
				type: "POST",
				url: base_url+"get_franchisee_name",
				data: "search_by="+search_by,
				success: function(response){
					$("#search_result").html(response);

					$('#search_result ul li').click(function(){
					    var search_text = $(this).text();
					    console.log(search_text)
					    $('#search_by_name').val(search_text);
					    $('#search_btn').trigger('click');
					    $('#search_result').html("");
					    // $(window).scrollTop(scroll_h);
					    $('.change-view .map').show();
						$("#action_type").val(obj['action'][0]);
						var type = $("#action_type").val();

						if(type!=""){
							map_view(type);
						}
					    $('html, body').animate({
						    scrollTop: $(".filter-wrp").offset().top
						 }, 2000);
					});
				}
			});
		}else{
			$("#search_result").html("");

		}

	});	
});
</script>

<script src="http://maps.google.com/maps/api/js?key=AIzaSyA_p_yE6X7SI3hiXb40PZPs9RN0QRZweT4" type="text/javascript"></script>
<script>

function map_view(load_view_type){

var base_url = "<?php echo base_url();?>";

	var loc = $("#map_loc").val();

	loc  = loc .replace(/'/g, '"');
	var locations = JSON.parse("[" + loc + "]");
	console.log(locations);

	var zoom_point =0;
	var center_lat =0;
	var center_long =0;

	if(load_view_type == "onload"){
		zoom_point =4;
		center_lat = 20.5937;
		center_long = 78.9629;
	}

	if(load_view_type == "search"){
		zoom_point =12;
		center_lat = locations[0][1];
		center_long = locations[0][2];
	}

	var map = new google.maps.Map(document.getElementById('map'), {
	  zoom: zoom_point,
	  center: new google.maps.LatLng(center_lat, center_long),
	  //center: new google.maps.LatLng(22.87444, 79.475098),
	  mapTypeId: google.maps.MapTypeId.ROADMAP
	});

	var infowindow = new google.maps.InfoWindow();

	var marker, i;
	//alert(locations.length)
	var string ='';
	for (i = 0; i < locations.length; i++) { 
	  marker = new google.maps.Marker({
		position: new google.maps.LatLng(locations[i][1], locations[i][2]),
		map: map,
		icon: base_url+"assets/images/map-locator.png",
		//title: 'Courtyard by Marriott'
		//pixelOffset: new google.maps.Size(100,140)
	  });
	string ='';

	 string = "<div class='search-tooltip'><label><strong>Name:</strong> </label> Coldwell Banker "+locations[i][0]+"</br><label><strong>Address:</strong> </label>"+locations[i][3]+"</br><a href='"+base_url+'our-franchise-details/coldwell-banker-'+locations[i][4]+"/"+locations[i][5]+"'>View Details >> </a></div>";


	  google.maps.event.addListener(marker, 'click', (function(marker, i) {
		return function() {
		  infowindow.setContent("<div class='search-tooltip'><label><strong>Name:</strong> </label> Coldwell Banker "+locations[i][0]+"</br><label><strong>Address:</strong> </label>"+locations[i][3]+"</br><a href='"+base_url+'our-franchise-details/coldwell-banker-'+locations[i][4]+"/"+locations[i][5]+"'>View Details >> </a></div>");
		  infowindow.open(map, marker);

		}
	  })(marker, i));
	}
}



</script>
</body>
</html>
