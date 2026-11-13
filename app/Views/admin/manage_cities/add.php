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
		<form class="form-horizontal" autocomplete="off" enctype="multipart/form-data" name="city_form" id="city_form"  action="" method="post">
			<?= csrf_field() ?>
			<div class="form-group">
				<label for="meta_description" class="col-sm-2 control-label">Name</label>
				<div class="col-sm-10">
                    <input type="text" class="form-control" id="name" name="name" placeholder="Name" value="<?php
                        if (!empty($city_info)) {
                            echo esc($city_info['city_name']);
                        }
                        ?>" >
                        <span id="title_error" class="text-red" style="display:block;"><?php echo validation_show_error('name');?></span>
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


<script>
$(document).ready(function(){

	$(document).on("submit","#city_form",function(){

			var BASE_URL = '<?php echo base_url();?>';
			  
			var dRegExp = /^[A-z ]+$/;
			var title_error = false;

			var name = $('#name').val();

	      	if (name == "") {
		        $('#title_error').show().html('Please provide name');
		        title_error = true;
		    }else{
		        $('#title_error').show().html('');
		        title_error = false;
		    }


			if(!title_error){
			  return true;
			}else{
			  return false;
			}

	});
	
});
</script>
