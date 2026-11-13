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
		
		  <div class="nav-tabs-custom">
	            <ul class="nav nav-tabs">
	               	<li class="<?php if ($form_title=="Life at coldwell banker | Update") {  echo 'active'; }?>">
			            <a href="<?php echo base_url(); ?>admin/section/life_at_coldwell_banker"  aria-expanded="true">Details
			            </a>
		        	</li>
		        	<li class="<?php if ($form_title=="Life at coldwell banker images | Add/Update") {  echo 'active'; }?>">
						<a href="<?php echo base_url(); ?>admin/section/life_at_coldwell_banker_images"  aria-expanded="false">Images
						</a>
					</li>
	            </ul>
			</div>
		
		<!-- /.box-header -->
		<!-- form start -->
		<form class="form-horizontal" autocomplete="off" enctype="multipart/form-data" name="franchise_form" id="franchise_form"  action="" method="post">
			<?= csrf_field() ?>
			<div class="box-body">
				<div class="form-group">
					<label for="exampleInputFile" class="col-sm-2 control-label">Upload Image</label>
					<div class="col-sm-10">
						<input type="file" name="image" id="image">
						<p class="help-block">You can upload image</p>
						<span id="small_image_error" class="text-red"></span>
					</div>
                </div>
				
			</div>
			<?php
				if (!empty($image_info) && !empty($image_info['image'])){ ?>
					<div class="col-sm-10 col-sm-offset-2">
						<div class="panel panel-default">
							<div class="panel-body">
								<img src="<?php echo base_url(); ?>uploads/life_at_coldwell_banker/<?php echo esc($image_info['image']); ?>" width="100px" alt="<?php echo esc($image_info['image']) ; ?>" >
							</div>
						</div>
					</div>
				<?php } ?>

				<input type="hidden" name="filetype" value="image" id="filetype">

			<!-- /.box-body -->
			<div class="box-footer">
				<button type="submit" class="btn btn-info pull-right">Update</button>
			</div>
			<!-- /.box-footer -->
		</form>
	  </div>
    </div>
</div>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/admin/plugins/datepicker/bootstrap-datepicker.js"></script>

<script>
$(document).ready(function(){

	$.fn.datepicker.defaults.format = "dd-mm-yyyy";
	$('#start_date').datepicker();
	$('#end_date').datepicker();


	$(document).on("submit","#franchise_form",function(){

		var BASE_URL = '<?php echo base_url();?>';
		  
		var dRegExp = /^[A-z0-9 ]+$/;
		var small_image_error = false;
		var title_error = false;

		var image = $('#image').val();

		var check = image.split(".");
      	var files = $("#image")[0].files[0];
      	var formdata = new FormData(this);

		if(image != ""){

	     	if(check[check.length-1] == 'jpg' || check[check.length-1] == 'JPEG' || check[check.length-1] == 'png' || check[check.length-1] == 'JPG'){    	
		    		$('#small_image_error').show().html('');
		        	small_image_error = false;

		    }else{
		    	$('#small_image_error').show().html('Please upload valid image');
				small_image_error = true; 
			    return false;		    	
		    }
		}else{
	    	$('#small_image_error').show().html('Please upload valid image');
			small_image_error = true; 
		    return false;		    	
	    }

	    if(image != ""){
			    $.ajax({
		            url: BASE_URL+'admin/section/check_mime',
		            type: 'POST',
					data:formdata,
					mimeTypes:"multipart/form-data",
					processData: false,
					contentType: false,
					async:false,
		            success: function (response) { 
		               if(response == '2'){

		               		$('#small_image_error').show().html('Please upload valid image');    
						    small_image_error = true;
						    return false;
		               }else{
		               		$('#small_image_error').show().html('');
			        		small_image_error = false;
		               }

		            },
		            error: function (jqXHR, textStatus, errorThrown) {
		            }
		        });
			}

		if(!small_image_error && !title_error){
		  return true;
		}else{
		  return false;
		}

		});
});
</script>
