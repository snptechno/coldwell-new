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
		<form class="form-horizontal" autocomplete="off" enctype="multipart/form-data" name="franchise_form" id="franchise_form"  action="" method="post">
			<?= csrf_field() ?>
			<div class="form-group">
				<label for="meta_description" class="col-sm-2 control-label">Research Type</label>
				<div class="col-sm-10">
					<select id="type" name="type" class="form-control">
						<option value="">Select Type</option>						
						<option value="Project Reports" <?php echo (!empty($research_info) && $research_info['type'] == 'Project Reports') ? 'selected=selected' : ''; ?>>Project Reports</option>
						<option value="Area Insights" <?php echo (!empty($research_info) && $research_info['type'] == 'Area Insights') ? 'selected=selected' : ''; ?>>Area Insights</option>						
					</select>
	                <span id="type_error" class="text-red" style="display:block;"><?php echo validation_show_error('type');?></span>
				</div>
			</div>

			<div class="form-group">
				<label for="meta_description" class="col-sm-2 control-label">Title</label>
				<div class="col-sm-10">
                    <input type="text" class="form-control" id="title" name="title" placeholder="Title" value="<?php
                        if (!empty($research_info)) {
                            echo esc($research_info['title']);
                        }
                        ?>" >
                        <span id="title_error" class="text-red" style="display:block;"><?php echo validation_show_error('title');?></span>
				</div>
			</div>

			<div class="form-group">
				<label for="content" class="col-sm-2 control-label">Description</label>
				<div class="col-sm-10">
					<input type="text" class="form-control" id="description" name="description" placeholder="Description" value="<?php
                        if (!empty($research_info)) {
                            echo esc($research_info['description']);
                        }
                        ?>" >
					<span id="description_error" class="text-red" style="display:block;"><?php echo validation_show_error('description');?></span>
				</div>
			</div>
			
			
			<div class="box-body">
				<div class="form-group">
					<label for="exampleInputFile" class="col-sm-2 control-label">Research Image</label>
					<div class="col-sm-10">
						<input type="file" name="image" id="image">
						<p class="help-block">Upload Research image</p>
						<span id="small_image_error" class="text-red"></span>
					</div>
                </div>
				
			</div>
			<?php
				if (!empty($research_info) && !empty($research_info['image_name'])){ ?>
					<div class="col-sm-10 col-sm-offset-2">
						<div class="panel panel-default">
							<div class="panel-body">
								<img src="<?php echo base_url(); ?>uploads/research/<?php echo esc($research_info['image_name']); ?>" width="100px" alt="<?php echo esc($research_info['image_name']); ?>" >
							</div>
						</div>
					</div>

					<input type="hidden" value="<?php echo esc($research_info['image_name'])?>" id="existing_image">

				<?php } ?>

				<input type="hidden" name="filetype" value="image" id="filetype">
				
			<div class="box-body">
				<div class="form-group">
					<label for="exampleInputFile" class="col-sm-2 control-label">Research document</label>
					<div class="col-sm-10">
						<input type="file" name="documents" id="documents">
						<p class="help-block">Upload Research document</p>
						<span id="documents_error_error" class="text-red"></span>
					</div>
                </div>
				
			</div>	
			<?php
				if (!empty($research_info) && !empty($research_info['pdf_name'])){ ?>
					<input type="hidden" value="<?php echo esc($research_info['pdf_name'])?>" id="existing_document">
				<?php } ?>

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
		var small_image_error = false;
		var title_error = false;
		var type_error = false;
		var documents_error = false;

		var image = $('#image').val();
		var existing_image = $('#existing_image').val();
		var type = $('#type').val();
		var title = $('#title').val();
		var documents = $('#documents').val();
		var existing_document = $('#existing_document').val();

		var check = image.split(".");
      	var check1 = documents.split(".");
      	var formdata = new FormData(this);

      	if (type == "") {
	        $('#type_error').show().html('Please select type');
	        type_error = true;
	    }else{
	        $('#type_error').show().html('');
	        type_error = false;
	    }

      	if (title == "") {
	        $('#title_error').show().html('Please provide title');
	        title_error = true;
	    }else{
	        $('#title_error').show().html('');
	        title_error = false;
	    }

		if(image != "" && existing_image == undefined){

	     	if(check[check.length-1] == 'jpg' || check[check.length-1] == 'JPEG' || check[check.length-1] == 'png'){    	
		    		$('#small_image_error').show().html('');
		        	small_image_error = false;

		    }else{
		    	$('#small_image_error').show().html('Please upload valid image');
				small_image_error = true; 
			    return false;		    	
		    }
		}else{
			if(existing_image == "" && image == ""){
				$('#small_image_error').show().html('Please upload valid image');
				small_image_error = true; 
			    return false;
			}else{
				$('#small_image_error').show().html('');
				small_image_error = false; 
			}
	    }

			if(!small_image_error && !type_error && !title_error  && !documents_error){
			  return true;
			}else{
			  return false;
			}

		});
});
</script>
