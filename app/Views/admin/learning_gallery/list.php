<div class="row">
    <div class="col-xs-12">		  
		  <div class="box">
			<div class="box-header">
			  <h3 class="box-title"></h3>
			</div>

			
			<!-- /.box-header -->
			<a href="<?php echo base_url(); ?>admin/section/add_learning_gallery" type="button" class="btn bg-purple btn-flat margin">Add Gallery image</a>
			<div class="box-body">
			  <table id="example1" class="table table-bordered table-striped">
				<thead>
				<tr>
				  <th>#</th>
				  <th>Title</th>
				  <th>Image</th>
				  <th>Created Date</th>
				  <th>Group Id</th>
				  <th>Status</th>
				  <th>Action</th>
				</tr>
				</thead>
				<tbody>
				<?php  $i=1;
				foreach ($info as $row) { ?>
				<tr>
				  <td><?php echo $i;?></td>
				  <td><?php echo esc($row['title']);?></td>
				  <td><img src="<?php echo base_url().'uploads/learning_gallery/'.esc($row['image_name'])?>" width='100' height='100'></td>
				  <td><?php echo esc($row['created_date']);?></td>
				  <td><input type="text" name="group_id" id="group_id_<?php echo $row['id'];?>" value="<?php echo esc($row['group_id']);?>"><input type="button" id="send_no" class="send_no"  image-id="<?php echo $row['id'];?>" value="Go"></td>
				  <td>
				  	<?php if($row['status'] =='0'){?><a href="<?php echo base_url().'admin/section/change_status_learning_gallery/'.$row['id'].'/1';?>">Enable</a><?php }else{ ?><a href="<?php echo base_url().'admin/section/change_status_learning_gallery/'.$row['id'].'/0';?>">Disable</a><?php } ?>
				  </td>
				  <td><a href="<?php echo base_url().'admin/section/add_learning_gallery/'.$row['id'];?>"><i class="fa fa-edit"></i></a> &nbsp;&nbsp;&nbsp;<a href="#" image-id="<?php echo $row['id'];?>" name="remove_levels" class="remove_levels" data-toggle="modal" data-target="#myModal"><i class="fa fa-trash-o"></i></a></td>
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
var base_url = "<?php echo base_url();?>";

$(document).ready(function(){
	
	$(document).on('click', '.remove_levels', function (e) {
		var image_id = $(this).attr("image-id");
		var project_id = $(this).attr("project-id");

		$(document).on('click', '#delete', function (e) {			
			$(location).attr('href', base_url+'admin/section/delete_learning_gallery_image/'+image_id)
		});
	});
	/***************---------------------------*************/
	$(document).on('click','.send_no',function(e){
		var image_id = $(this).attr('image-id');
		var  group_id = $("#group_id_"+image_id).val();  

		if(group_id!=""){
		$.ajax({
                url: base_url+'admin/section/update_group_id_life_at_coldwell_gallery',
                type: 'POST',
                data: 'group_id='+group_id+'&id='+image_id,
                success: function (response) {
                    console.log(response);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert('Some error occurred');
                }
            });
		}	

	});
	/************--------------------------------****************/
	
});
</script>
