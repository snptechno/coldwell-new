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
                            if (!empty($event_info)) {
                                echo esc($event_info['title']);
                            }
                            ?>" >
                            <span id="title_error" class="text-red" style="display:block;"><?php echo validation_show_error('title');?></span>
					</div>
				</div>


			<div class="form-group">
					<label for="meta_description" class="col-sm-2 control-label">Start Date</label>
					<div class="col-sm-10">
                        <input type="text" class="form-control" id="start_date" name="start_date" placeholder="Start Date" value="<?php
                            if (!empty($event_info)) {
                                echo esc($event_info['start_date']);
                            }
                            ?>" >
                            <span id="title_error" class="text-red" style="display:block;"><?php echo validation_show_error('start_date');?></span>
					</div>
				</div>


			<div class="form-group">
					<label for="meta_description" class="col-sm-2 control-label">End Date</label>
					<div class="col-sm-10">
                        <input type="text" class="form-control" id="end_date" name="end_date" placeholder="End Date" value="<?php
                            if (!empty($event_info)) {
                                echo esc($event_info['end_date']);
                            }
                            ?>" >
                            <span id="title_error" class="text-red" style="display:block;"><?php echo validation_show_error('end_date');?></span>
					</div>
				</div>

				<div class="form-group">
					<label for="meta_description" class="col-sm-2 control-label">Duration</label>
					<div class="col-sm-10">
                        <input type="text" class="form-control" id="duration" name="duration" placeholder="Duration" value="<?php
                            if (!empty($event_info)) {
                                echo esc($event_info['duration']);
                            }
                            ?>" >
                            <span id="title_error" class="text-red" style="display:block;"><?php echo validation_show_error('duration');?></span>
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
  $( function() {
    $( "#start_date" ).datepicker({
      changeMonth: true,
      changeYear: true,
      dateFormat: 'yy-mm-dd'
    });
    $( "#end_date" ).datepicker({
      changeMonth: true,
      changeYear: true,
      dateFormat: 'yy-mm-dd'
    });
  } );
</script>
