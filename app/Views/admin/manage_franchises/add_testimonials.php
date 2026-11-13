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
		  <?php if(!empty($franchise_info) && $franchise_info['id']!=""){?>
		  <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
               	<li class="<?php if (!empty($franchise_info) && $form_title=="Franchise | Add/Update") {  echo 'active'; }?>">
		            <a href="<?php echo base_url(); ?>admin/section/add_franchise/<?php 
		            if (!empty($franchise_info)) { echo $franchise_info['id'];}?>"  aria-expanded="true">Update Franchise
		            </a>
	        	</li>
	        	<li class="<?php if ($form_title=="Franchises Owners") {  echo 'active'; }?>">
					<a href="<?php echo base_url(); ?>admin/section/franchise_owners_list/<?php 
					if (!empty($franchise_info)) { echo $franchise_info['id'];} ?>"  aria-expanded="false">Manage Franchises Owners </a>
				</li>
	        	<li class="<?php if ($form_title=="Real Estate Specialist") {  echo 'active'; }?>">
					<a href="<?php echo base_url(); ?>admin/section/specialist_list/<?php 
					if (!empty($franchise_info)) { echo $franchise_info['id'];} ?>"  aria-expanded="false">Manage Franchises Real Estate Specialist
					</a>
				</li>
				<li class="<?php if ($form_title=="Franchiese Testimonials") {  echo 'active'; }?>">
					<a href="<?php echo base_url(); ?>admin/section/manage_franchiese_testimonials/<?php 
					if (!empty($franchise_info)) { echo $franchise_info['id'];} ?>"  aria-expanded="false">Manage Franchises Testimonials
					</a>
				</li>
            </ul>
		</div>
		<?php } ?>
		<!-- /.box-header -->
		<!-- form start -->
		<form class="form-horizontal" enctype="multipart/form-data" autocomplete="off" name="franchise_form" id="franchise_form"  action="" method="post">
			<?= csrf_field() ?>
			<div class="form-group">
					<label for="meta_description" class="col-sm-2 control-label">Name</label>
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
					<label for="meta_description" class="col-sm-2 control-label">Designation</label>
					<div class="col-sm-10">
                        <input type="text" class="form-control" id="designation" name="designation" placeholder="Designation" value="<?php
                            if (!empty($franchise_info)) {
                                echo esc($franchise_info['designation']);
                            }
                            ?>" >
                            <span id="designation_error" class="text-red" style="display:block;"><?php echo validation_show_error('designation');?></span>
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


			<!-- /.box-body -->
			<div class="box-footer">
				<button type="submit" class="btn btn-info pull-right">Update</button>
			</div>
			<!-- /.box-footer -->
		</form>
	  </div>
    </div>
</div>
