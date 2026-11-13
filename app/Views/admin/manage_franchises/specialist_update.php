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
		<form class="form-horizontal" autocomplete="off" enctype="multipart/form-data" name="project_banners" id="project_banners"  action="<?php echo base_url(); ?>admin/section/update_specialist/<?php 
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
                        <input type="text" class="form-control" id="title" name="title" placeholder="title" value="<?php
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
						<p class="help-block">You can upload banner image</p>
						<span id="small_image_error" class="text-red"></span>
					</div>
                </div>
                <?php if(!empty($franchise_info)){?>
				<div class="col-sm-10 col-sm-offset-2">
					<div class="panel panel-default">
						<div class="panel-body">
							
							<img src="<?php echo base_url(); ?>uploads/specialist/<?php echo esc($franchise_info['image']); ?>" width="100px" alt="<?php echo esc($franchise_info['name']) ; ?>" title="<?php echo esc($franchise_info['name']) ; ?>">
							
						</div>
					</div>
				</div>
				<?php } ?>
				<input type="hidden" name="isimage" value="<?php
                            if (!empty($franchise_info)) {
                                echo esc(trim($franchise_info['image']));
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
			$(location).attr('href', base_url+'admin/section/delete_project_banner/'+project_id+'/'+image_id)
		});
	});

	$(document).on("submit","#project_banners",function(){

		var BASE_URL = '<?php echo base_url();?>';
		  
		var dRegExp = /^[A-z0-9 ]+$/;
		var small_image_error = false;
		var title_error = false;
		var name_error = false;


		var image = $('#image').val();
		var name = $('#name').val();
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

		if(image != "" && is_image==""){

	     	if(check[check.length-1] == 'jpg' || check[check.length-1] == 'JPEG' || check[check.length-1] == 'png'){    	
		    		$('#small_image_error').show().html('');
		        	small_image_error = false;

		    }else{
		    	$('#small_image_error').show().html('Please upload valid image');
				small_image_error = true; 
		    }
		}else if(image == "" && is_image!=""){
    		$('#small_image_error').show().html('');
        	small_image_error = false;
		}else{
	    	$('#small_image_error').show().html('Please upload valid image');
			small_image_error = true; 
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


		if(!name_error && !small_image_error){
		  return true;
		}else{
		  return false;
		}

		});
});
</script>
