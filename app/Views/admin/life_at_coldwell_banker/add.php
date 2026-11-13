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
		        	<li class="<?php if ($form_title=="Life at coldwell banker images | List") {  echo 'active'; }?>">
						<a href="<?php echo base_url(); ?>admin/section/life_at_coldwell_banker_images"  aria-expanded="false">Images
						</a>
					</li>
	            </ul>
			</div>
		
		<!-- /.box-header -->
		<!-- form start -->
		<form class="form-horizontal" autocomplete="off" enctype="multipart/form-data" name="franchise_form" id="franchise_form"  action="" method="post">
			<?= csrf_field() ?>
			<div class="form-group">
					<label for="meta_description" class="col-sm-2 control-label">Title</label>
					<div class="col-sm-10">
                        <input type="text" class="form-control" id="title" name="title" placeholder="Title" value="<?php
                            if (!empty($info)) {
                                echo esc($info['title']);
                            }
                            ?>" >
                            <span id="title_error" class="text-red" style="display:block;"><?php echo validation_show_error('title');?></span>
					</div>
				</div>

			<div class="form-group">
					<label for="content" class="col-sm-2 control-label">Description</label>
					<div class="col-sm-10">
						<textarea class="textarea" placeholder="Description" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;" name="description" id="description"><?php
                            if (!empty($info)) {
                                echo esc(trim($info['description']));
                            }
                            ?></textarea>
						<span id="description_error" class="text-red" style="display:block;"><?php echo validation_show_error('description');?></span>
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

	$.fn.datepicker.defaults.format = "dd-mm-yyyy";
	$('#start_date').datepicker();
	$('#end_date').datepicker();


	$(document).on("submit","#franchise_form",function(){

		var BASE_URL = '<?php echo base_url();?>';
		  
		var dRegExp = /^[A-z0-9 ]+$/;
		
		var title_error = false;
		var description_error = false;


		var title = $('#title').val();
		var description = $('#description').val();
		
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

			if(!title_error && !description_error){
			  return true;
			}else{
			  return false;
			}

		});
});
</script>
