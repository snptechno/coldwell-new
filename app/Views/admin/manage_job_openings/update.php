<div class="row">
	<!-- left column -->
	<div class="col-md-12">
	  <!-- general form elements -->
	  <div class="box box-primary">
		<div class="box-header with-border">
		  <h3 class="box-title"></h3>
		<!-- /.box-header -->
		<!-- form start -->
		<form class="form-horizontal" autocomplete="off" enctype="multipart/form-data" name="job_opening" id="job_opening"  action="<?php echo base_url(); ?>admin/section/save_job/<?php
                            if (!empty($job_info)) {
                                echo $job_info['id'];
                            }
                            ?>" method="post">
			<?= csrf_field() ?>
			<div class="box-body">
				<?php if(session()->getFlashdata('class')!=""){ ?>
				<div class="alert <?php echo esc(session()->getFlashdata('class'));?> alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
				<?php echo esc(session()->getFlashdata('message'));?>
				</div>
				<?php } ?>


				<div class="form-group">
				<label for="meta_description" class="col-sm-2 control-label">Job opening Type</label>
				<div class="col-sm-10">
					<select id="type" name="type" class="form-control">
						<option value="">Select Type</option>						
						<option value="COLDWELL BANKER INDIA OPENINGS" <?php echo (!empty($job_info) && $job_info['type'] == 'COLDWELL BANKER INDIA OPENINGS') ? 'selected=selected' : ''; ?>>COLDWELL BANKER INDIA OPENINGS</option>
						<option value="FRANCHISEES OPENINGS" <?php echo (!empty($job_info) && $job_info['type'] == 'FRANCHISEES OPENINGS') ? 'selected=selected' : ''; ?>>FRANCHISEES OPENINGS</option>						
					</select>
	                <span id="type_error" class="text-red" style="display:block;"><?php echo validation_show_error('type');?></span>
				</div>
			</div>

				<div class="form-group">
					<label for="Floor Plan Name" class="col-sm-2 control-label">Position Name</label>

					<div class="col-sm-10">
						<input type="text" class="form-control" id="position_name"  name="position_name" placeholder="Position Name" value="<?php
                            if (!empty($job_info)) {
                                echo esc($job_info['position']);
                            }
                            ?>" >
                            <span id="position_name_error" class="text-red"></span>
					</div>
				</div>

				<div class="form-group">
					<label for="Floor Plan Name" class="col-sm-2 control-label">Location</label>

					<div class="col-sm-10">
						<input type="text" class="form-control" id="location" name="location" placeholder="Location" value="<?php
                            if (!empty($job_info)) {
                                echo esc($job_info['location']);
                            }
                            ?>" >
                            <span id="location_error" class="text-red"></span>
					</div>
				</div>

				<div class="form-group">
					<label for="Floor Plan Name" class="col-sm-2 control-label">Educational Qualification</label>

					<div class="col-sm-10">
		                    <textarea class="textarea" placeholder="Educational Qualification" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;" name="qualification" id="qualification"><?php
		                            if (!empty($job_info)) {
		                                echo esc($job_info['qualification']);
		                            }
		                            ?></textarea>
		                    <span id="qualification_error" class="text-red"></span>
					</div>
				</div>

				<div class="form-group">
					<label for="Floor Plan Name" class="col-sm-2 control-label">Max Experience</label>

					<div class="col-sm-10">
						<input type="text" class="form-control" id="experience" name="experience" placeholder="Max Experience" value="<?php
                            if (!empty($job_info)) {
                                echo esc($job_info['max_experience']);
                            }
                            ?>" >
                            <span id="experience_error" class="text-red"></span>
					</div>
				</div>

				<div class="form-group">
					<label for="Floor Plan Name" class="col-sm-2 control-label">Min Experience</label>

					<div class="col-sm-10">
						<input type="text" class="form-control" id="min_experience" name="min_experience" placeholder="Min Experience" value="<?php
                            if (!empty($job_info)) {
                                echo esc($job_info['min_experience']);
                            }
                            ?>" >
                            <span id="min_experience_error" class="text-red"></span>
					</div>
				</div>

				<div class="form-group">
					<label for="Project Meta Title" class="col-sm-2 control-label">Description</label>

					<div class="col-sm-10">
					<textarea class="textarea" placeholder="Description" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;" name="description" id="description"><?php
                            if (!empty($job_info)) {
                                echo esc($job_info['description']);
                            }
                            ?></textarea>
                    <span id="description_error" class="text-red"></span>
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
			adminPost(base_url+'admin/section/delete_project_banner/'+project_id+'/'+image_id)
		});
	});

	$(document).on("submit","#job_opening",function(){
		  
		  var dRegExp = /^[A-z0-9 \/,.]+$/;
		  var type_error = false;
		  var position_name_error = false;
		  var location_error = false;
		  var qualification_error = false;
		  var experience_error = false;
		  var min_experience_error = false;
		  var description_error = false;

		  var type = $('#type').val();
		  var position_name = $('#position_name').val();
		  var location = $('#location').val();
		  var qualification = $('#qualification').val();
		  var experience = $('#experience').val();
		  var min_experience = $('#min_experience').val();		  
		  var description = $('#description').val();

		  	if (type == "") {
		        $('#type_error').show().html('Please select opening type');
		        type_error = true;
		    }else{
		        $('#type_error').show().html('');
		        type_error = false;
		    }

		    if (position_name == "") {
		        $('#position_name_error').show().html('Please provide position name');
		        position_name_error = true;
		    } else if (position_name.length == 0 || position_name.length < 2 ) {
		        $('#position_name_error').show().html('Project position name should be atleast 1 character in length');
		        position_name_error = true;
		    }else{
		        $('#position_name_error').show().html('');
		        position_name_error = false;
		    }

		    if (location == "") {
		        $('#location_error').show().html('Please provide location name');
		        location_error = true;
		    }else{
		        $('#location_error').show().html('');
		        location_error = false;
		    }

		    if (qualification == "") {
		        $('#qualification_error').show().html('Please provide qualification');
		        qualification_error = true;
		    }else{
		        $('#qualification_error').show().html('');
		        qualification_error = false;
		    }

		    if (description == "") {
		        $('#description_error').show().html('Please provide job description');
		        description_error = true;
		    }else{
		        $('#description_error').show().html('');
		        description_error = false;
		    }


			if(!position_name_error && !location_error && !qualification_error &&  !description_error ){
			  return true;
			}else{
			  return false;
			}

		});
});
</script>
