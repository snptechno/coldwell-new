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
		<form class="form-horizontal" autocomplete="off" enctype="multipart/form-data" name="project_banners" id="project_banners"  action="" method="post">
			<?= csrf_field() ?>
			<div class="form-group">
					<label for="meta_description" class="col-sm-2 control-label">Title</label>
					<div class="col-sm-10">
                        <input type="text" class="form-control" id="title" name="title" placeholder="Title" value="<?php
                            if (!empty($image_info)) {
                                echo esc($image_info['title']);
                            }
                            ?>" >
                            <span id="title_error" class="text-red"></span>
					</div>
				</div>

			<div class="form-group">
				<label for="meta_description" class="col-sm-2 control-label">Date</label>
				<div class="col-sm-10">
                    <input type="text" class="form-control" id="datepicker" name="datepicker" placeholder="Date" value="<?php
                        if (!empty($image_info)) {
                            echo esc($image_info['date_time']);
                        }
                        ?>" >
                        <span id="datepicker_error" class="text-red"></span>
				</div>
			</div>


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
				if (!empty($image_info) && !empty($image_info['image_name'])){ ?>
					<div class="col-sm-10 col-sm-offset-2">
						<div class="panel panel-default">
							<div class="panel-body">
								<img src="<?php echo base_url(); ?>uploads/learning_gallery/<?php echo esc($image_info['image_name']); ?>" width="100px" alt="<?php echo esc($image_info['image_name']) ; ?>" >
							</div>
						</div>
					</div>
				<?php } ?>
				<input type="hidden" value="<?php  if(!empty($image_info)){ echo esc($image_info['image_name']); } ?>" id="isimage" name="isimage"> 

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
  $( function() {
    $( "#datepicker" ).datepicker({
      changeMonth: true,
      changeYear: true,
      dateFormat: 'yy-mm-dd'
    });
  } );

  </script>
<script>
$(document).ready(function(){

	$(document).on("submit","#project_banners",function(){

		var BASE_URL = '<?php echo base_url();?>';
		  
		var dRegExp = /^[A-z0-9 ]+$/;
		var small_image_error = false;
		var title_error = false;
		var datepicker_error = false;

		var image = $('#image').val();
		var title = $('#title').val();
		var datepicker = $('#datepicker').val();
		var is_image = $('#isimage').val();

		var check = image.split(".");
      	var files = $("#image")[0].files[0];
      	var formdata = new FormData(this);

      	if (title == "") {
		        $('#title_error').show().html('Please provide title');
		        title_error = true;
		    }else{
		        $('#title_error').show().html('');
		        title_error = false;
		}

		if (datepicker == "") {
		        $('#datepicker_error').show().html('Please provide valid date ');
		        datepicker_error = true;
		    }else{
		        $('#datepicker_error').show().html('');
		        datepicker_error = false;
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

		
		if(image != "" && is_image == ""){

	     	if(check[check.length-1] == 'jpg' || check[check.length-1] == 'JPEG' || check[check.length-1] == 'png'|| check[check.length-1] == 'PNG'|| check[check.length-1] == 'jpeg'|| check[check.length-1] == 'JPG'){    	
		    		$('#small_image_error').show().html('');
		        	small_image_error = false;

		    }else{
		    	$('#small_image_error').show().html('Please upload valid image of 284*192 ');
				small_image_error = true; 
		    }
		   
		}else if(image == "" && is_image!=""){
	    		$('#small_image_error').show().html('');
	        	small_image_error = false;
	        	
		}else{
		    	$('#small_image_error').show().html('Please upload valid image');
				small_image_error = true; 
				 
	    }

		if(!small_image_error && !datepicker_error && !title_error){
		  return true;
		}else{
		  return false;
		}

		});
});
</script>
