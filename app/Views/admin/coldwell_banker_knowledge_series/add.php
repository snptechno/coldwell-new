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
		<form class="form-horizontal" autocomplete="off" enctype="multipart/form-data" name="knowledge_series_from" id="knowledge_series_from"  action="" method="post">
			<?= csrf_field() ?>
			<div class="form-group">
				<label for="meta_description" class="col-sm-2 control-label">Title</label>
				<div class="col-sm-10">
                    <input type="text" class="form-control" id="title" name="title" placeholder="Title" value="<?php
                        if (!empty($news_knowledge_series)) {
                            echo esc($news_knowledge_series['title']);
                        }
                        ?>" >
                        <span id="title_error" class="text-red" style="display:block;"><?php echo validation_show_error('title');?></span>
				</div>
			</div>

			<div class="form-group">
				<label for="Description" class="col-sm-2 control-label">Description</label>

				<div class="col-sm-10">
				<textarea class="textarea" placeholder="Description" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;" name="description" id="description"><?php
                        if (!empty($news_knowledge_series)) {
                            echo esc($news_knowledge_series['description']);
                        }
                        ?></textarea>
                <span id="description_error" class="text-red"></span>
				</div>
			</div>

			<div class="form-group">
				<label for="meta_description" class="col-sm-2 control-label">External Link</label>
				<div class="col-sm-10">
                    <input type="text" class="form-control" id="external_link" name="external_link" placeholder="External Link" value="<?php
                        if (!empty($news_knowledge_series)) {
                            echo esc($news_knowledge_series['external_link']);
                        }
                        ?>" >
				</div>
			</div>

			<div class="box-body">
				<div class="form-group">
					<label for="exampleInputFile" class="col-sm-2 control-label">News Image</label>
					<div class="col-sm-10">
						<input type="file" name="image" id="image">
						<p class="help-block">Upload News image</p>
						<span id="small_image_error" class="text-red"></span>
					</div>
                </div>
				
			</div>
			<?php
				if (!empty($news_knowledge_series) && !empty($news_knowledge_series['image'])){ ?>
					<div class="col-sm-10 col-sm-offset-2">
						<div class="panel panel-default">
							<div class="panel-body">
								<img src="<?php echo base_url(); ?>uploads/knowledge_series/<?php echo esc($news_knowledge_series['image']); ?>" width="100px" alt="<?php echo esc($news_knowledge_series['image']); ?>" >
							</div>
						</div>
					</div>

					<input type="hidden" value="<?php echo esc($news_knowledge_series['image'])?>" id="existing_image">

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


<script>
$(document).ready(function(){

	$(document).on("submit","#knowledge_series_from",function(){

		var BASE_URL = '<?php echo base_url();?>';
		  
		var dRegExp = /^[A-z0-9 ]+$/;
		var name_error = false;
		var small_image_error = false;


		var image = $('#image').val();
		var title = $('#title').val();
		var existing_image = $("#existing_image").val();

		var check = image.split(".");

      	var formdata = new FormData(this);

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
	    	
		    }
		}else{
			if((existing_image == "" || existing_image == undefined)  && image == ""){
				$('#small_image_error').show().html('Please upload valid image');
				small_image_error = true; 

			}else if((existing_image == "" || existing_image == undefined)  && image == ""){
				$('#small_image_error').show().html('Please upload valid image');
				small_image_error = false; 
			}else{
				$('#small_image_error').show().html('');
				small_image_error = false; 
			}
	    }

		if(!small_image_error && !title_error){
			  return true;
			}else{
			  return false;
			}

		});
});
</script>
