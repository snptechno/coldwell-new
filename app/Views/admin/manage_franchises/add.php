<div class="row">
	<!-- left column -->
	<div class="col-md-12">
	  <!-- general form elements -->
	  <div class="box box-primary">
		<div class="box-header with-border">
		  <h3 class="box-title"></h3>
		  <div class="box box-primary">
		<div class="box-header with-border">
		  <h3 class="box-title"></h3>
		  <?php if(!empty($franchise_info) && $franchise_info['id']!=""){?>
		  <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
               	<li class="<?php if (!empty($franchise_info) && $form_title=="Franchise | Add/Update") {  echo 'active'; }?>">
		            <a href="<?php echo base_url(); ?>admin/section/add_franchise/<?php 
		            if (!empty($franchise_info)) { echo $franchise_info['id'];}?>"  aria-expanded="true">Update Franchise
		            </a>
	        	</li>
	        	<li class="<?php if ($form_title=="Franchises Owners") {  echo 'active'; }?>">
					<a href="<?php echo base_url(); ?>admin/section/franchise_owners_list/<?php 
					if (!empty($franchise_info)) { echo $franchise_info['id'];} ?>"  aria-expanded="false">Manage Franchises Owners </a>
				</li>
	        	<li class="<?php if ($form_title=="Real Estate Specialist") {  echo 'active'; }?>">
					<a href="<?php echo base_url(); ?>admin/section/specialist_list/<?php 
					if (!empty($franchise_info)) { echo $franchise_info['id'];} ?>"  aria-expanded="false">Manage Franchises Real Estate Specialist
					</a>
				</li>
				<li class="<?php if ($form_title=="Franchiese Testimonials") {  echo 'active'; }?>">
					<a href="<?php echo base_url(); ?>admin/section/manage_franchiese_testimonials/<?php 
					if (!empty($franchise_info)) { echo $franchise_info['id'];} ?>"  aria-expanded="false">Manage Franchises Testimonials
					</a>
				</li>
            </ul>
		</div>
		<?php } ?>
		<!-- /.box-header -->
		<!-- form start -->
		<form class="form-horizontal" autocomplete="off" enctype="multipart/form-data" name="franchise_form" id="franchise_form"  action="" method="post">
			<?= csrf_field() ?>
			<div class="form-group">
					<label for="meta_description" class="col-sm-2 control-label">Title</label>
					<div class="col-sm-10">
                        <input type="text" class="form-control" id="title" name="title" placeholder="Title" value="<?php
                            if (!empty($franchise_info)) {
                                echo esc($franchise_info['title']);
                            }
                            ?>" >
                            <span id="title_error" class="text-red" style="display:block;"><?php echo validation_show_error('title');?></span>
					</div>
			</div>

			<div class="form-group">
				<label for="content" class="col-sm-2 control-label">Project Description</label>
				<div class="col-sm-10">
					<textarea class="textarea" placeholder="Project Description" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;" name="project_description" id="project_description"><?php
                        if (!empty($franchise_info)) {
                            echo esc(trim($franchise_info['project_description']));
                        }
                        ?></textarea>
					<span id="project_description_error" class="text-red" style="display:block;"><?php echo validation_show_error('project_description');?></span>
				</div>
			</div>

			<div class="form-group">
					<label for="meta_description" class="col-sm-2 control-label">Mobile No</label>
					<div class="col-sm-10">
                        <input type="text" class="form-control" id="mobile_no" name="mobile_no" placeholder="Mobile No" value="<?php
                            if (!empty($franchise_info)) {
                                echo esc($franchise_info['mobile_no']);
                            }
                            ?>" >
                            <span id="mobile_no_error" class="text-red" style="display:block;"><?php echo validation_show_error('mobile_no');?></span>
					</div>
			</div>

			<div class="form-group">
					<label class="col-sm-2 control-label">Email Id</label>
					<div class="col-sm-10">
                        <input type="text" class="form-control" id="email_id" name="email_id" placeholder="Email Id" value="<?php
                            if (!empty($franchise_info)) {
                                echo esc($franchise_info['email_id']);
                            }
                            ?>" >
                            <span id="mobile_no_error" class="text-red" style="display:block;"><?php echo validation_show_error('email_id');?></span>
					</div>
			</div>
			<div class="form-group">
				<label for="Upload Images" class="col-sm-2 control-label">Upload Logo</label>
				<div class="col-sm-10">
					<input type="file" name="image" id="image" >
					<p class="help-block"></p>
					<span id="small_image_error" class="text-red"></span>
				</div>
			</div>
			<?php if( isset($franchise_info['logo']) && $franchise_info['logo']!=""){?>
			<div class="col-sm-10 col-sm-offset-2">
				<div class="panel panel-default">
					<div class="panel-body">
						<img src="<?php echo base_url(); ?>uploads/franchises/<?php echo esc($franchise_info['logo']); ?>" width="100px" alt="<?php echo esc($franchise_info['title']) ; ?>" title="<?php echo esc($franchise_info['title']) ; ?>">
					</div>
				</div>
			</div>
			<?php } ?>

			<div class="form-group">
					<label for="meta_description" class="col-sm-2 control-label">Website Url</label>
					<div class="col-sm-10">
                        <input type="text" class="form-control" id="web_url" name="web_url" placeholder="Website Url" value="<?php
                            if (!empty($franchise_info)) {
                                echo esc($franchise_info['website_url']);
                            }
                            ?>" >
                            <span id="web_url_error" class="text-red" style="display:block;"></span>
					</div>
			</div>
			<strong style="padding-left:180px">(Add Multiple Video Using ";")</strong>
			<div class="form-group">
					<label  class="col-sm-2 control-label">Video Url</label>
					<div class="col-sm-10">
                        <input type="text" class="form-control" id="video_url" name="video_url" placeholder="Video Url" value="<?php
                            if (!empty($franchise_info)) {
                                echo esc($franchise_info['video_url']);
                            }
                            ?>" >
                            <span id="video_url_error" class="text-red" style="display:block;"></span>
					</div>
			</div>
			

			<div class="form-group">
					<label for="meta_description" class="col-sm-2 control-label">City</label>
					<div class="col-sm-10">

					<select id="city" name="city" class="form-control">
						<option value="">Select City</option>
						<?php 
							foreach($city_list as $city_item){

								if(isset($franchise_info['office_specialities'])){

									if($franchise_info['city'] == $city_item['id']){
										$selected = 'selected=selected';
									}else{
										$selected = '';
									}

								}else{
									$selected = '';
								}

								echo '<option value="'.esc($city_item['id']).'" '.$selected.'>'.esc($city_item['city_name']).'</option>';
							}

						?>
					</select>

                    <span id="city_error" class="text-red" style="display:block;"><?php echo validation_show_error('city');?></span>
					</div>
				</div>


			<div class="form-group">
					<label for="meta_description" class="col-sm-2 control-label">Location</label>
					<div class="col-sm-10">
                        <input type="text" class="form-control" id="location" name="location" placeholder="Location" value="<?php
                            if (!empty($franchise_info)) {
                                echo esc($franchise_info['location']);
                            }
                            ?>" >
                            <span id="location_error" class="text-red" style="display:block;"><?php echo validation_show_error('location');?></span>
					</div>
			</div>

			<div class="form-group">
					<label for="content" class="col-sm-2 control-label">Address</label>
					<div class="col-sm-10">
						<textarea class="textarea" placeholder="Address" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;" name="address" id="address"><?php
                            if (!empty($franchise_info)) {
                                echo esc(trim($franchise_info['address']));
                            }
                            ?></textarea>
						<span id="location_description_error" class="text-red" style="display:block;"><?php echo validation_show_error('address');?></span>
					</div>
			</div>

			<div class="form-group">
					<label  class="col-sm-2 control-label">Latitude</label>
					<div class="col-sm-10">
                        <input type="text" class="form-control" id="latitude" name="latitude" placeholder="Latitude" value="<?php
                            if (!empty($franchise_info)) {
                                echo esc($franchise_info['latitude']);
                            }
                            ?>" >
                            <span id="latitude_error" class="text-red" style="display:block;"><?php echo validation_show_error('latitude');?></span>
					</div>
			</div>

			<div class="form-group">
					<label for="meta_description" class="col-sm-2 control-label">Longitude</label>
					<div class="col-sm-10">
                        <input type="text" class="form-control" id="longitude" name="longitude" placeholder="Longitude" value="<?php
                            if (!empty($franchise_info)) {
                                echo esc($franchise_info['longitude']);
                            }
                            ?>" >
                            <span id="location_error" class="text-red" style="display:block;"><?php echo validation_show_error('longitude');?></span>
					</div>
			</div>

			<div class="box-body">
				<div class="form-group">
					<label for="exampleInputFile" class="col-sm-2 control-label">Status</label>
					<div class="col-sm-10">
						<input type="checkbox" name="franchise_status" value="Coming Soon" <?php if(isset($franchise_info['office_specialities']) && $franchise_info['franchise_status']=='Coming Soon') echo 'checked';?>>Coming Soon
						<input type="checkbox" name="franchise_status" value="Exits" <?php if(isset($franchise_info['office_specialities']) && $franchise_info['franchise_status']=='Exits') echo 'checked';?>>Exits
						<input type="checkbox" name="franchise_status" value="External Link" <?php if(isset($franchise_info['office_specialities']) && $franchise_info['franchise_status']=='External Link') echo 'checked';?>>External Link
						<span id="franchise_status_error" class="text-red"><?php echo validation_show_error('franchise_status');?></span>
					</div>
                </div>
			</div>

			<div class="form-group">
					<label for="meta_description" class="col-sm-2 control-label">External Link</label>
					<div class="col-sm-10">
                        <input type="text" class="form-control" id="external_link" name="external_link" placeholder="External Link" value="<?php
                            if (!empty($franchise_info)) {
                                echo esc($franchise_info['external_link']);
                            }
                            ?>" >
					</div>
			</div>

			<div class="form-group">
					<label for="meta_description" class="col-sm-2 control-label">Speciality Market</label>
					<div class="col-sm-10">
                        <input type="text" class="form-control" id="sp_market" name="sp_market" placeholder="Speciality Market" value="<?php
                            if (!empty($franchise_info)) {
                                echo esc($franchise_info['speciality_market']);
                            }
                            ?>" >
					</div>
			</div>

			
			<div class="form-group">
					<label for="content" class="col-sm-2 control-label">Location Description</label>
					<div class="col-sm-10">
						<textarea class="textarea" placeholder="Location Description" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;" name="location_description" id="location_description"><?php
                            if (!empty($franchise_info)) {
                                echo esc(trim($franchise_info['location_description']));
                            }
                            ?></textarea>
						<span id="location_description_error" class="text-red" style="display:block;"><?php echo validation_show_error('location_description');?></span>
					</div>
			</div>

			<div class="box-body">
				<div class="form-group">
					<label for="exampleInputFile" class="col-sm-2 control-label">Office Specialities</label>
					<div class="col-sm-10">
					<?php if(isset($franchise_info['office_specialities'])){
							$set1 = explode(",",$franchise_info['office_specialities']);
					}?>
						<input type="checkbox" name="office_speciality[]" value="Primary" <?php if(isset($set1) && in_array('Primary', $set1)) echo 'checked';?>>Primary
						<input type="checkbox" name="office_speciality[]" value="Resale" <?php if(isset($set1) && in_array('Resale', $set1)) echo 'checked';?>>Resale
						<input type="checkbox" name="office_speciality[]" value="Rental" <?php if(isset($set1) && in_array('Rental', $set1)) echo 'checked';?>>Rental
						<input type="checkbox" name="office_speciality[]" value="Property Management" <?php if(isset($set1) && in_array('Property Management', $set1)) echo 'checked';?>>Property Management
						<span id="office_specialities_error" class="text-red"></span>
					</div>
                </div>
				
			</div>
			
			<div class="box-body">
				<div class="form-group">
					<label for="exampleInputFile" class="col-sm-2 control-label">Languages</label>
					<div class="col-sm-10">
					<?php if(isset($franchise_info['languages'])){
							$set = explode(",",$franchise_info['languages']);
					}
					?>
						<input type="checkbox" name="languages[]" value="English" <?php if(isset($set) && in_array('English', $set)) echo 'checked=checked';?>>English
						<input type="checkbox" name="languages[]" value="Hindi" <?php if(isset($set) && in_array('Hindi', $set)) echo 'checked=checked';?>>Hindi
						<input type="checkbox" name="languages[]" value="Marathi" <?php if(isset($set) && in_array('Marathi', $set)) echo 'checked=checked';?>>Marathi
						<input type="checkbox" name="languages[]" value="Gujarati" <?php if(isset($set) && in_array('Gujarati', $set)) echo 'checked=checked';?>>Gujarati
						<input type="checkbox" name="languages[]" value="Kannada" <?php if(isset($set) && in_array('Kannada', $set)) echo 'checked=checked';?>>Kannada
						<input type="checkbox" name="languages[]" value="Tamil" <?php if(isset($set) && in_array('Tamil', $set)) echo 'checked=checked';?>>Tamil
						<input type="checkbox" name="languages[]" value="Telugu" <?php if(isset($set) && in_array('Telugu', $set)) echo 'checked=checked';?>>Telugu
						<input type="checkbox" name="languages[]" value="Punjabi" <?php if(isset($set) && in_array('Punjabi', $set)) echo 'checked=checked';?>>Punjabi
						<input type="checkbox" name="languages[]" value="Creole" <?php if(isset($set) && in_array('Creole', $set)) echo 'checked=checked';?>>Creole
						<input type="checkbox" name="languages[]" value="Sindhi" <?php if(isset($set) && in_array('Sindhi', $set)) echo 'checked=checked';?>>Sindhi
						<input type="checkbox" name="languages[]" value="Urdu" <?php if(isset($set) && in_array('Urdu', $set)) echo 'checked=checked';?>>Urdu
					</div>
                </div>
				
			</div>

			<!-- /.box-body -->
			<div class="box-footer">
				<button type="submit" class="btn btn-info pull-right">Update</button>
			</div>
			<!-- /.box-footer -->
		</form>
	  </div>
    </div>
</div>


<script>
$(document).ready(function(){

	$(document).on("submit","#franchise_form",function(){

			var BASE_URL = '<?php echo base_url();?>';
			  
			var dRegExp = /^[A-z0-9 ]+$/;
			var title_error = false;
			var mobile_no_error = false;
			var city_error = false;
			var location_error = false;


			var title = $('#title').val();
			var mobile_no = $('#mobile_no').val();
			var city = $('#city').val();
			var location = $('#location').val();

	      	if (title == "") {
		        $('#title_error').show().html('Please provide title');
		        title_error = true;
		    }else{
		        $('#title_error').show().html('');
		        title_error = false;
		    }

		    if (city == "") {
		        $('#city_error').show().html('Please provide city');
		        city_error = true;
		    }else{
		        $('#city_error').show().html('');
		        city_error = false;
		    }

		    if (location == "") {
		        $('#location_error').show().html('Please provide location');
		        location_error = true;
		    }else{
		        $('#location_error').show().html('');
		        location_error = false;
		    }

			if(!title_error  && !city_error && !location_error ){
			  return true;
			}else{
			  return false;
			}

		});
});
</script>
