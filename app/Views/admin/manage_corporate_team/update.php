<div class="row">
	<!-- left column -->
	<div class="col-md-12">
	  <!-- general form elements -->
	  <div class="box box-primary">
		<div class="box-header with-border">
		  <h3 class="box-title"></h3>
		  
		<!-- /.box-header -->
		<!-- form start -->
		<form class="form-horizontal" autocomplete="off" enctype="multipart/form-data" name="project_banners" id="project_banners"  action="" method="post">
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
                            if (!empty($franchise_info)) {
                                echo esc($franchise_info['name']);
                            }
                            ?>" >
                            <span id="name_error" class="text-red" style="display:block;"><?php echo validation_show_error('name');?></span>
					</div>
				</div>

				<div class="form-group">
					<label for="Floor Plan Name" class="col-sm-2 control-label">Title</label>

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
					<label for="content" class="col-sm-2 control-label">Description</label>
					<div class="col-sm-10">
						<textarea class="textarea" placeholder="Description" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;" name="description" id="description"><?php
                            if (!empty($franchise_info)) {
                                echo esc(trim($franchise_info['description']));
                            }
                            ?></textarea>
						<span id="description_error" class="text-red" style="display:block;"><?php echo validation_show_error('description');?></span>
					</div>
			</div>

				<div class="form-group">
					<label for="Upload Images" class="col-sm-2 control-label">Upload Image</label>
					<div class="col-sm-10">
						<input type="file" name="image" id="image" >
						<p class="help-block">You can upload image</p>
						<span id="small_image_error" class="text-red"></span>
					</div>
                </div>
                <?php if(!empty($franchise_info)){?>
				<div class="col-sm-10 col-sm-offset-2">
					<div class="panel panel-default">
						<div class="panel-body">
							
							<img src="<?php echo base_url(); ?>uploads/corporate_team/<?php echo esc($franchise_info['image']); ?>" width="100px" alt="<?php echo esc($franchise_info['name']) ; ?>" title="<?php echo esc($franchise_info['name']) ; ?>">
							
						</div>
					</div>
				</div>
				<?php } ?>

			<input type="hidden" value="<?php  if(!empty($franchise_info)){ echo esc($franchise_info['image']); } ?>" id="isimage" name="isimage"> 


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

	$(document).on('click', '.remove_levels', function (e) {
		var project_id = $(this).attr("project-id");
		var image_id = $(this).attr("image-id");
		var base_url = "<?php echo base_url();?>";
		$(document).on('click', '#delete', function (e) {			
			$(location).attr('href', base_url+'admin/section/delete_corporate_team/'+project_id+'/'+image_id)
		});
	});

	$(document).on("submit","#project_banners",function(){

		var BASE_URL = '<?php echo base_url();?>';
		  
		var dRegExp = /^[A-z0-9 ]+$/;
		var small_image_error = false;
		var title_error = false;
		var name_error = false;
		var description_error = false;

		var image = $('#image').val();
		var existing_image = $('#existing_image').val();
		var name = $('#name').val();
		var title = $('#title').val();
		var description = $('#description').val();
		var is_image = $('#isimage').val();  

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

	    if (title == "") {
	        $('#title_error').show().html('Please provide title');
	        title_error = true;
	    }else{
	        $('#title_error').show().html('');
	        title_error = false;
	    }

	    if (description == "") {
	        $('#description_error').show().html('Please provide description');
	        description_error = true;
	    }else{
	        $('#description_error').show().html('');
	        description_error = false;
	    }
      	
		if(image != "" && is_image == ""){

	   		 if(image != "" && name != "" && title != "" && description != ""){
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
		   
		}else if(image == "" && is_image!=""){
	    		$('#small_image_error').show().html('');
	        	small_image_error = false;
	        	
		}else if(image != "" && is_image != ""){

	   		 if(image != "" && name != "" && title != "" && description != ""){
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
		   
		}else{
		    	$('#small_image_error').show().html('Please upload valid image');
				small_image_error = true; 
				 
	    }

		if(!name_error && !title_error  && !description_error  && !small_image_error){
		  return true;
		}else{
		  return false;
		}

		});
});
</script>
