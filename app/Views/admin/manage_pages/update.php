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
		<form class="form-horizontal" autocomplete="off" enctype="multipart/form-data" name="pages" id="pages"  action="<?php echo base_url(); ?>admin/section/save_page/<?php 
            if (!empty($page_info)) {
                echo $page_info['id'];
            }
            ?>" method="post">
			<?= csrf_field() ?>
			<div class="box-body">
			
				<div class="form-group">
					<label for="name" class="col-sm-2 control-label">Name</label>
					<div class="col-sm-10">
                        <span>
							<strong>
							<?php
								if (!empty($page_info)) {
									echo esc($page_info['name']);
								}
                            ?>
							</strong>
						</span>
					</div>
				</div>
				
				<div class="form-group">
					<label for="content" class="col-sm-2 control-label">Content</label>
					<div class="col-sm-10">
						<textarea class="textarea" placeholder="Page content" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;" name="content" id="content"><?php
								if (!empty($page_info)) {
									echo esc($page_info['content']);
								}
								?></textarea>
					</div>
				</div>
				
				<div class="form-group">
					<label for="title" class="col-sm-2 control-label">Title</label>
					<div class="col-sm-10">
                        <input type="text" class="form-control" id="title" name="title" placeholder="Page title" value="<?php
                            if (!empty($page_info)) {
                                echo esc($page_info['title']);
                            }
                            ?>" >
					</div>
				</div>
				
				<div class="form-group">
					<label for="meta_description" class="col-sm-2 control-label">Meta Description</label>
					<div class="col-sm-10">
                        <input type="text" class="form-control" id="meta_description" name="meta_description" placeholder="Meta description" value="<?php
                            if (!empty($page_info)) {
                                echo esc($page_info['meta_description']);
                            }
                            ?>" >
					</div>
				</div>
				
				<div class="form-group">
					<label for="meta_keywords" class="col-sm-2 control-label">Meta Keywords</label>
					<div class="col-sm-10">
                        <input type="text" class="form-control" id="meta_keywords" name="meta_keywords" placeholder="Meta keywords" value="<?php
                            if (!empty($page_info)) {
                                echo esc($page_info['meta_keywords']);
                            }
                            ?>" >
					</div>
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
