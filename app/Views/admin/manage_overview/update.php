<div class="row">
	<!-- left column -->
	<div class="col-md-12">
	  <!-- general form elements -->
	  <div class="box box-primary">
		<div class="box-header with-border">
		  <h3 class="box-title"><?php echo esc($form_title);?></h3>
		</div>
		<!-- /.box-header -->
		<!-- form start -->
		<form class="form-horizontal" autocomplete="off" enctype="multipart/form-data" name="overview" id="form_overview"  action="<?php echo base_url(); ?>admin/section/update_overview/<?php 
            if (!empty($page_info)) {
                echo $page_info['id'];
            }
            ?>" method="post">
			<?= csrf_field() ?>
			<div class="box-body">
			
				<div class="form-group">
					<label for="content" class="col-sm-2 control-label">Overview</label>
					<div class="col-sm-10">
						<textarea class="textarea" placeholder="Overview" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;" name="overview" id="overview"><?php
                            if (!empty($page_info)) {
                                echo esc(trim($page_info['overview']));
                            }
                            ?></textarea>
						<span id="overview_error" class="text-red"></span>
					</div>
				</div>

				<div class="form-group">
					<label for="content" class="col-sm-2 control-label">Coldwell Banker University</label>
					<div class="col-sm-10">
						<textarea class="textarea" placeholder="Overview" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;" name="university" id="university"><?php
                            if (!empty($page_info)) {
                                echo esc(trim($page_info['university']));
                            }
                            ?></textarea>
						<span id="university_error" class="text-red"></span>
					</div>
				</div>
				
				<div class="form-group">
					<label for="Upload Images" class="col-sm-2 control-label">Upload University Logo</label>
					<div class="col-sm-10">
						<input type="file" name="image" id="image" >
						
						<span id="small_image_error" class="text-red"></span>
					</div>
                </div>
				
				
			</div>

			<input type="hidden" name="isimage" value="<?php
                            if (!empty($page_info)) {
                                echo esc(trim($page_info['university_logo']));
                            }
                            ?>" id="isimage">
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

	$(document).on("submit","#form_overview",function(){
		  
		var BASE_URL = '<?php echo base_url();?>';

		  var dRegExp = /^[A-z0-9 ]+$/;
		  var overview_error = false;
		  var university_error = false;
		  var small_image_error = false;

		  var overview = $('#overview').val();
		  var university = $('#university').val();
		  var image = $('#image').val();  
	      var check = image.split(".");
	      var files = $("#image")[0].files[0];
	      var is_image = $('#isimage').val();  

	      var formdata = new FormData(this);

		    if (overview == "") {
		        $('#overview_error').show().html('Please provide overview');
		        overview_error = true;
		    }else{
		        $('#overview_error').show().html('');
		        overview_error = false;
		    }

		    if (university == "") {
		        $('#university_error').show().html('Please provide university');
		        university_error = true;
		    }else{
		        $('#university_error').show().html('');
		        university_error = false;
		    }

		    if(image != "" && is_image==""){

			     if(check[check.length-1] == 'JPG' || check[check.length-1] == 'jpg' || check[check.length-1] == 'JPEG' || check[check.length-1] == 'png'){    	
			    		$('#small_image_error').show().html('');
			        	small_image_error = false;

			    }else{
			    	$('#small_image_error').show().html('Please upload valid image');
					small_image_error = true; 
				    return false;		    	
			    }

			}else if(image == "" && is_image!=""){
		    		$('#small_image_error').show().html('');
		        	small_image_error = false;
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

			if(!overview_error  && !university_error && !small_image_error){
			  return true;
			}else{
			  return false;
			}

		});
});
</script>
