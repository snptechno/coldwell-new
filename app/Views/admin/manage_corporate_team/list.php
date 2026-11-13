<div class="row">
    <div class="col-xs-12">		  
		  <div class="box">
			<div class="box-header">
			  <h3 class="box-title"></h3>
			</div>

			
			<!-- /.box-header -->
			<a href="<?php echo base_url(); ?>admin/section/update_corporate_team/<?php 
            if (!empty($fran_id)) {
                echo $fran_id;
            }?>" type="button" class="btn bg-purple btn-flat margin">Add Corporate Team</a>
			<div class="box-body">
			  <table id="example1" class="table table-bordered table-striped">
				<thead>
				<tr>
				  <th>#</th>
				  <th>Name</th>
				  <th>Title</th>
				  <th>Image</th>
				  <th>Created Date</th>
				  <th>Order No</th>
				  <th>Status</th>
				  <th>Action</th>
				</tr>
				</thead>
				<tbody>
				<?php  $i=1;
				foreach ($teams_info as $row) { ?>
				<tr>
				  <td><?php echo $i;?></td>
				  <td><?php echo esc($row['name']);?></td>
				  <td><?php echo esc($row['title']);?></td>
				  <td><img src="<?php echo base_url().'uploads/corporate_team/'.esc($row['image'])?>" width="100" height="100"></td>
				  <td><?php echo esc($row['created_date']);?></td>
				  <td> <input type="text" class="form-control order_no" id="order_no"  member-id="<?php echo $row['id'];?>" name="order_no" value="<?php echo esc($row['order_no']);?>" ></td>
				   <td>
				  	<?php if($row['status'] =='0'){?><a href="<?php echo base_url().'admin/section/change_status_corporate_team/'.$row['id'].'/1';?>">Enable</a><?php }else{ ?><a href="<?php echo base_url().'admin/section/change_status_corporate_team/'.$row['id'].'/0';?>">Disable</a><?php } ?>
				  </td>
				  <td><a href="<?php echo base_url().'admin/section/update_corporate_team/'.$row['id'];?>"><i class="fa fa-edit"></i></a> &nbsp;&nbsp;&nbsp;<a href="#"  project-id="<?php echo $row['id'];?>" image-id="<?php echo $row['id'];?>" name="remove_levels" class="remove_levels" data-toggle="modal" data-target="#myModal"><i class="fa fa-trash-o"></i></a></td>
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
	var base_url = "<?php echo base_url();?>";
	$(document).on('click', '.remove_levels', function (e) {
		var project_id = $(this).attr("project-id");
		var image_id = $(this).attr("image-id");
		
		$(document).on('click', '#delete', function (e) {			
			$(location).attr('href', base_url+'admin/section/delete_corporate_team/'+project_id+'/'+image_id)
		});
	});
	/*-----------change order no---------------*/
	$('.order_no').on('input',function(e){
		var order_no = $(this).val();
		var member_id = $(this).attr("member-id");
		if(order_no!="" && member_id!=""){
			$.ajax({
                url: base_url+'admin/section/update_order_no_corporate_team',
                type: 'POST',
                data: 'order_no='+order_no+'&member_id='+member_id,
                success: function (response) {
                    if(response=="success"){
                    	$(location).attr('href', base_url+'admin/section/manage_corporate_teams')
                    }

                },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert('Some error occurred');
                }
            });
		}	

	});

	/*----------------------------------------*/
});
</script>
