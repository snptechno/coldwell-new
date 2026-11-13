<div class="row">
    <div class="col-xs-12">		  
		  <div class="box">
			<div class="box-header">
			  <h3 class="box-title"></h3>
			</div>

			<!-- /.box-header -->
			<a href="<?php echo base_url(); ?>admin/section/update_job_opening" type="button" class="btn bg-purple btn-flat margin">Add Job</a>
			<div class="box-body">
			  <table id="example1" class="table table-bordered table-striped">
				<thead>
				<tr>
				  <th>#</th>
				  <th>Type</th>
				  <th>Position</th>
				  <th>Location</th>
				  <th>Qualification</th>
				  <th>Max Experience</th>
				  <th>Min Experience</th>
				  <th>Status</th>
				  <th>Created Date</th>
				  <th>Action</th>
				</tr>
				</thead>
				<tbody>
				<?php  $i=1;
				foreach ($info as $row) { ?>
				<tr>
				  <td><?php echo $i;?></td>
				  <td><?php echo esc($row['type']);?></td>
				  <td><?php echo esc($row['position']);?></td>
				  <td><?php echo esc($row['location']);?></td>
				  <td><?php echo $row['qualification'];?></td>
				  <td><?php echo esc($row['max_experience']);?></td>
				  <td><?php echo esc($row['min_experience']);?></td>
 				  <td>
				  	<?php if($row['status']=='0'){?><a href="<?php echo base_url().'admin/section/change_status_job_opening/'.$row['id'].'/1';?>">Enable</a><?php }else{ ?><a href="<?php echo base_url().'admin/section/change_status_job_opening/'.$row['id'].'/0';?>">Disable</a><?php } ?>
				  </td>
				  <td><?php echo esc($row['created_date']);?></td>
				  <td><a href="<?php echo base_url().'admin/section/update_job_opening/'.$row['id'].'/'.$row['id']?>"><i class="fa fa-edit"></i></a> &nbsp;&nbsp;&nbsp;<a href="#"   job-id="<?php echo $row['id'];?>" name="remove_levels" class="remove_levels" data-toggle="modal" data-target="#myModal"><i class="fa fa-trash-o"></i></a></td>
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
		var job_id = $(this).attr("job-id");
		var base_url = "<?php echo base_url();?>";
		$(document).on('click', '#delete', function (e) {			
			$(location).attr('href', base_url+'admin/section/delete_job_opening/'+job_id)
		});
	});
});
</script>
