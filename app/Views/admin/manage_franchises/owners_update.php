<div class="row">
	<!-- left column -->
	<div class="col-md-12">
	  <!-- general form elements -->
	  <div class="box box-primary">
		<div class="box-header with-border">
		  <h3 class="box-title"></h3>
		  <?php if(!empty($fran_id) && $fran_id!=""){?>
			<div class="nav-tabs-custom">
				<ul class="nav nav-tabs">
	               	<li class="<?php if (!empty($franchise_info) && $form_title=="Franchise | Add/Update") {  echo 'active'; }?>">
			            <a href="<?php echo base_url(); ?>admin/section/add_franchise/<?php 
			            if (!empty($fran_id)) { echo $fran_id;}?>"  aria-expanded="true">Update Franchise
			            </a>
		        	</li>
		        	<li class="<?php if ($form_title=="Franchises Owners") {  echo 'active'; }?>">
						<a href="<?php echo base_url(); ?>admin/section/franchise_owners_list/<?php 
						if (!empty($fran_id)) { echo $fran_id;} ?>"  aria-expanded="false">Manage Franchises Owners </a>
					</li>
		        	<li class="<?php if ($form_title=="Real Estate Specialist") {  echo 'active'; }?>">
						<a href="<?php echo base_url(); ?>admin/section/specialist_list/<?php 
						if (!empty($fran_id)) { echo $fran_id;} ?>"  aria-expanded="false">Manage Franchises Real Estate Specialist
						</a>
					</li>
					
				</ul>
			</div>
			<?php } ?>
		<!-- /.box-header -->
		<!-- form start -->
		<form class="form-horizontal" autocomplete="off" enctype="multipart/form-data" name="project_banners" id="project_banners"  action="<?php echo base_url(); ?>admin/section/update_franchise_owner/<?php 
            if (!empty($fran_id)) {
                echo $fran_id;
            }if (!empty($specialist_id)) {
                echo '/'.$specialist_id;
            }
            ?>" method="post">
			<?= csrf_field() ?>
			<div class="box-body">
				<?php if(session()->getFlashdata('project_success_msg')!=""){ ?>
				<div class="alert alert-success alert-dismissible">
	                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
	                <?php echo esc(session()->getFlashdata('project_success_msg'));?>
                </div>
                <?php } ?>
				<div class="form-group">
					<label for="Floor Plan Name" class="col-sm-2 control-label">Name</label>

					<div class="col-sm-10">
                        <input type="text" class="form-control" id="name" name="name" placeholder="Name" value="<?php
                            if (!empty($franchise_owner_info)) {
                                echo esc($franchise_owner_info['name']);
                            }
                            ?>" >
                            <span id="name_error" class="text-red" style="display:block;"><?php echo validation_show_error('name');?></span>
					</div>
				</div>
				<div class="form-group">
					<label  class="col-sm-2 control-label">Position Name</label>
					<div class="col-sm-10">
                        <input type="text" class="form-control" id="position_name" name="position_name" placeholder="Position Name" value="<?php
                            if (!empty($franchise_owner_info)) {
                                echo esc($franchise_owner_info['position_name']);
                            }
                            ?>" >
                            
					</div>
				</div>
				
				<div class="form-group">
					<label for="Floor Plan Name" class="col-sm-2 control-label">Gender</label>

					<div class="col-sm-10">
                        <input type="radio" id="gender" name="gender" value="Male" <?php
                            if (!empty($franchise_owner_info)) {
                                if($franchise_owner_info['gender'] == 'Male'){
									echo 'checked';
								}
                            }
                            ?> >Male
							<br>
						<input type="radio" id="gender" name="gender" value="Female" <?php
							if (!empty($franchise_owner_info)) {
								if($franchise_owner_info['gender'] == 'Female'){
									echo 'checked';
								}
							}
							?> >Female
                            <span id="gender_error" class="text-red" style="display:block;"></span>
					</div>
				</div>

				<div class="form-group">
					<label for="content" class="col-sm-2 control-label">Description</label>
					<div class="col-sm-10">
						<textarea class="textarea" placeholder="Description" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;" name="description" id="description"><?php
                            if (!empty($franchise_owner_info)) {
                                echo esc(trim($franchise_owner_info['description']));
                            }
                            ?></textarea>
						<span id="description_error" class="text-red" style="display:block;"><?php echo validation_show_error('description');?></span>
					</div>
			</div>
				<div class="form-group">
					<label for="Upload Images" class="col-sm-2 control-label">Upload Image</label>
					<div class="col-sm-10">
						<input type="file" name="image" id="image" >
						<p class="help-block">You can upload banner image of 500*500 size</p>
						<span id="small_image_error" class="text-red"></span>
					</div>
                </div>
                <?php if( isset($franchise_owner_info['image']) && $franchise_owner_info['image']!=""){?>
				<div class="col-sm-10 col-sm-offset-2">
					<div class="panel panel-default">
						<div class="panel-body">
							
							<img src="<?php echo base_url(); ?>uploads/franchise_owners/<?php echo esc($franchise_owner_info['image']); ?>" width="100px" alt="<?php echo esc($franchise_owner_info['name']) ; ?>" title="<?php echo esc($franchise_owner_info['name']) ; ?>">
							
						</div>
					</div>
				</div>
				<?php } ?>
				<input type="hidden" name="isimage" value="<?php
                            if (!empty($franchise_owner_info)) {
                                echo esc(trim($franchise_owner_info['image']));
                            }
                            ?>" id="isimage">

                <input type="hidden" name="dimession_error"  id="dimession_error">

			<!-- /.box-body -->
			<div class="box-footer">
				<button type="submit" class="btn btn-info pull-right">Update</button>
			</div>
			<!-- /.box-footer -->
		</form>
	  </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Delete</h4>
      </div>
       <div class="modal-body">
	        <p>You are about to delete.</p>
	        <p>Do you want to proceed?</p>
	    </div>
      <div class="modal-footer">
        <button type="button" id="delete" class="btn btn-primary">Yes</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
      </div>
    </div>
  </div>
</div>  
<script>
$(document).ready(function(){

	var _URL = window.URL || window.webkitURL;
	$("#image").change(function (e) {
	    var file, img;
	    if ((file = this.files[0])) {
	        img = new Image();
	        img.onload = function () {
	            if(this.width != this.height){
	            	$('#dimession_error').val('1');
	            }
	        };
	        img.src = _URL.createObjectURL(file);
	    }
	});

	$(document).on('click', '.remove_levels', function (e) {
		var project_id = $(this).attr("project-id");
		var image_id = $(this).attr("image-id");
		var base_url = "<?php echo base_url();?>";
		$(document).on('click', '#delete', function (e) {			
			$(location).attr('href', base_url+'admin/section/delete_project_banner/'+project_id+'/'+image_id)
		});
	});

	$(document).on("submit","#project_banners",function(){

		var BASE_URL = '<?php echo base_url();?>';
		  
		var dRegExp = /^[A-z0-9 ]+$/;
		var small_image_error = false;
		var name_error = false;
		var gender_error = false;

		var image = $('#image').val();
		var name = $('#name').val();
		var description = $('#description').val();
		var is_image = $('#isimage').val();  
		var dimession_error = $('#dimession_error').val();  

		var check = image.split(".");
      	var files = $("#image")[0].files[0];
      	var formdata = new FormData(this);

      	if (name == "") {
	        $('#name_error').show().html('Please provide name');
	        name_error = true;
		}else{
	        $('#name_error').show().html('');
	        name_error = false;
		}
		
		if ($('input[name=gender]:checked').length<=0) {
	        $('#gender_error').show().html('Please select gender');
	        gender_error = true;
		}else{
	        $('#gender_error').show().html('');
	        gender_error = false;
		}

		if(image != ""){
			if(image != "" && is_image==""){

		     	if((check[check.length-1] == 'jpg' || check[check.length-1] == 'JPEG' || check[check.length-1] == 'png'|| check[check.length-1] == 'PNG'|| check[check.length-1] == 'jpeg' || check[check.length-1] == 'JPG') && dimession_error!=1){    	
			    		$('#small_image_error').show().html('');
			        	small_image_error = false;

			    }else{
			    	$('#small_image_error').show().html('Please upload valid image of 500 * 500');
					small_image_error = true; 
			    }
			}else if(image == "" && is_image!="" && dimession_error!=1){
	    		$('#small_image_error').show().html('');
	        	small_image_error = false;
			}else if(image != "" && is_image!="" && dimession_error!=1){
		     	if((check[check.length-1] == 'jpg' || check[check.length-1] == 'JPEG' || check[check.length-1] == 'png'|| check[check.length-1] == 'PNG'|| check[check.length-1] == 'jpeg' || check[check.length-1] == 'JPG') && dimession_error!=1){    	
			    		$('#small_image_error').show().html('');
			        	small_image_error = false;
			    }else{
			    	$('#small_image_error').show().html('Please upload valid image of 500 * 500');
					small_image_error = true; 
			    }
			}else{
		    	$('#small_image_error').show().html('Please upload valid image');
				small_image_error = true; 
		    }
		}else{
			$('#small_image_error').show().html('');
	        small_image_error = false;
		}

		if(!name_error && !small_image_error && !gender_error){
		  return true;
		}else{
		  return false;
		}

		});
});
</script>
