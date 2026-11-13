<div class="row">
    <div class="col-xs-12">		  
		  <div class="box">
			<div class="box-header">
			  <h3 class="box-title"></h3>
			</div>

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
						if (!empty($fran_id)) { echo $fran_id;} ?>"  aria-expanded="false">Manage Franchises Owners
						</a>
					</li>
		        	<li class="<?php if ($form_title=="Real Estate Specialist") {  echo 'active'; }?>">
						<a href="<?php echo base_url(); ?>admin/section/specialist_list/<?php 
						if (!empty($fran_id)) { echo $fran_id;} ?>"  aria-expanded="false">Manage Franchises Real Estate Specialist
						</a>
					</li>
					<li class="<?php if ($form_title=="Franchiese Testimonials") {  echo 'active'; }?>">
						<a href="<?php echo base_url(); ?>admin/section/manage_franchiese_testimonials/<?php 
						if (!empty($fran_id)) { echo $fran_id;} ?>"  aria-expanded="false">Manage Franchises Testimonials
						</a>
					</li>
				</ul>
			</div>
			<?php } ?>
			<!-- /.box-header -->
			<a href="<?php echo base_url(); ?>admin/section/update_franchise_owner/<?php 
            if (!empty($fran_id)) {
                echo $fran_id;
            }?>" type="button" class="btn bg-purple btn-flat margin">Add Owner</a>
			<div class="box-body">
			  <table id="example1" class="table table-bordered table-striped">
				<thead>
				<tr>
				  <th>#</th>
				  <th>Franchise Name</th>
				  <th>Owner Name</th>
				  <th>Image</th>
				  <th>Created Date</th>
				  <th>Action</th>
				</tr>
				</thead>
				<tbody>
				<?php  $i=1;
				foreach ($franchise_info as $row) { ?>
				<tr>
				  <td><?php echo $i;?></td>
				  <td><?php echo esc($row['franchise']);?></td>
				  <td><?php echo esc($row['name']);?></td>
				  <td><?php if($row['image']!=""){ ?><img src="<?php echo base_url().'uploads/franchise_owners/'.esc($row['image'])?>" width="100" height="100"><?php } ?></td>
				  <td><?php echo esc($row['created_date']);?></td>
				  <td><a href="<?php echo base_url().'admin/section/update_franchise_owner/'.$row['franchise_id'].'/'.$row['id']?>"><i class="fa fa-edit"></i></a> &nbsp;&nbsp;&nbsp;<a href="#"  project-id="<?php echo $fran_id;?>" image-id="<?php echo $row['id'];?>" name="remove_levels" class="remove_levels" data-toggle="modal" data-target="#myModal"><i class="fa fa-trash-o"></i></a></td>
				</tr>
				<?php $i++;} ?>
				</tfoot>
			  </table>
			</div>
			<!-- /.box-body -->
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
			$(location).attr('href', base_url+'admin/section/delete_franchise_owner/'+project_id+'/'+image_id)
		});
	});
});
</script>
