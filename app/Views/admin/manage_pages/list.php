<div class="row">
    <div class="col-xs-12">		  
		  <div class="box">
			<div class="box-header">
			  <h3 class="box-title"><?php echo esc($form_title);?></h3>
			</div>
			<!-- /.box-header -->
			<div class="box-body">
			  <table id="example1" class="table table-bordered table-striped">
				<thead>
				<tr>
				  <th>#</th>
				  <th>Name</th>
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
				  <td><?php echo esc($row['name']);?></td>
				  <td><?php if($row['status'] == '0'){?>
						<a data-toggle="tooltip" data-placement="top" title="Click to enable " href="#" data-admin-post="<?php echo base_url() .'admin/section/change_status_page/'.$row['id'].'/1';?>" onclick="adminPost(this.dataset.adminPost); return false;"><i class="fa fa-thumbs-down  fa-2x"></i></a>
					<?php }else{ ?>
						<a data-toggle="tooltip" data-placement="top" title="Click to disable " href="#" data-admin-post="<?php echo base_url() .'admin/section/change_status_page/'.$row['id'].'/0';?>" onclick="adminPost(this.dataset.adminPost); return false;"><i class="fa fa-thumbs-up  fa-2x"></i></a>
					<?php } ?>
				  </td>
				  <td><?php echo esc($row['created_on']);?></td>
				  <td><a href="<?php echo base_url().'admin/section/update_page/'.$row['id']?>"><i class="fa fa-edit"></i></a> &nbsp;&nbsp;&nbsp;<a href=""  page-id="<?php echo $row['id'];?>" name="remove_levels" class="remove_levels" data-toggle="modal" data-target="#myModal"><i class="fa fa-trash-o"></i></a></td>
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
		var page_id = $(this).attr("page-id");
		var base_url = "<?php echo base_url();?>";
		$(document).on('click', '#delete', function (e) {			
			adminPost(base_url+'admin/section/delete_page/'+page_id);
		});
	});
});
</script>
