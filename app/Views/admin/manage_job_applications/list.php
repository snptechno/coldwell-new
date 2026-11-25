<div class="row">
    <div class="col-xs-12">		  
		  <div class="box">
			<div class="box-header">
			  <h3 class="box-title"></h3>
			</div>

			<div class="box-body">
			  <table id="example1" class="table table-bordered table-striped">
				<thead>
				<tr>
				  <th>#</th>
				  <th>Type</th>
				  <th>Position Name</th>
				  <th>First Name</th>
				  <th>Mobile No</th>
				  <th>Email Id</th>
				  <th>Created Date</th>
				  <th>Action</th>
				</tr>
				</thead>
				<tbody>
				<?php  $i=1;
				foreach ($info as $row) { ?>
				<tr>
				  <td><?php echo $i;?></td>
				  <td><?php echo esc($row['position_type']);?></td>
				  <td><?php echo esc($row['position'] ?? '');?></td>
				  <td><?php echo esc($row['fname'].' '.$row['lname']);?></td>
				  <td><?php echo esc($row['mobile_no']);?></td>
				  <td><?php echo esc($row['email_id']);?></td>
				  <td><?php echo esc($row['created_date']);?></td>
 				  <td>
				  	<a href="<?php echo base_url().'admin/section/check_details_candidate/'.$row['id'];?>">View</a>
				  </td>
				  
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
			adminPost(base_url+'admin/section/delete_job_opening/'+job_id)
		});
	});
});
</script>
