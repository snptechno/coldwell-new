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
				<label for="meta_description" class="col-sm-2 control-label">Title</label>
				<div class="col-sm-10">
                    <input type="text" class="form-control" id="title" name="title" placeholder="Title" value="<?php
                        if (!empty($selling_home_info)) {
                            echo esc($selling_home_info['title']);
                        }
                        ?>" >
                        <span id="title_error" class="text-red" style="display:block;"><?php echo validation_show_error('title');?></span>
				</div>
			</div>

			<div class="form-group">
				<label for="content" class="col-sm-2 control-label">Description</label>
				<div class="col-sm-10">
					<textarea class="textarea" placeholder="Description" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;" name="description" id="description"><?php
                        if (!empty($selling_home_info)) {
                            echo esc(trim($selling_home_info['description']));
                        }
                        ?></textarea>
					<span id="description_error" class="text-red" style="display:block;"><?php echo validation_show_error('description');?></span>
				</div>
			</div>
			
			
			<div class="form-group">
				<label for="meta_description" class="col-sm-2 control-label">Video Url</label>
				<div class="col-sm-10">
                    <input type="text" class="form-control" id="url" name="url" placeholder="Video url" value="<?php
                        if (!empty($selling_home_info)) {
                            echo esc($selling_home_info['video_url']);
                        }
                        ?>" >
                        <span id="url_error" class="text-red" style="display:block;"><?php echo validation_show_error('url');?></span>
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

<script type="text/javascript" src="<?php echo base_url(); ?>assets/admin/plugins/datepicker/bootstrap-datepicker.js"></script>

<script>
$(document).ready(function(){

	$(document).on("submit","#franchise_form",function(){

		var BASE_URL = '<?php echo base_url();?>';
		  
			var dRegExp = /^[A-z0-9 ]+$/;
			var title_error = false;
			var description_error = false;
			var url_error = false;

			var title = $('#title').val();
			var description = $('#description').val();
			var url = $('#url').val();

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

		     if (url == "") {
		        $('#url_error').show().html('Please provide video url');
		        url_error = true;
		    }else{
		        $('#url_error').show().html('');
		        url_error = false;
		    }

			if(!title_error && !description_error && !url_error){
			  return true;
			}else{
			  return false;
			}

		});
});
</script>
