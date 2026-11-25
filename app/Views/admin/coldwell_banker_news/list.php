<div class="row">
    <div class="col-xs-12">		  
		  <div class="box">
			<div class="box-header">
			  <h3 class="box-title"></h3>
			</div>

			
			<!-- /.box-header -->
			<a href="<?php echo base_url(); ?>admin/section/add_coldwell_banker_news" type="button" class="btn bg-purple btn-flat margin">Add media for coldwell banker news</a>

			<div class="box-body">
			  <table id="example1" class="table table-bordered table-striped">
				<thead>
				<tr>
				  <th>#</th>
				  <th>Title</th>
				  <th>Description</th>
				  <th>Image</th>
				  <th>Created Date</th>
				  <th>Action</th>
				</tr>
				</thead>
				<tbody>
				<?php  $i=1;
				foreach ($info as $row) { ?>
				<tr>
				  <td><?php echo $i;?></td>
				  <td><?php echo esc($row['title']);?></td>
				  <td><?php echo $row['description'];?></td>
				  <td><img src="<?php echo base_url().'uploads/news/'.esc($row['image'])?>" width="100" height="100"></td>
				  <td><?php echo esc($row['created_date']);?></td>
				  <td><a href="<?php echo base_url().'admin/section/add_coldwell_banker_news/'.$row['id'];?>"><i class="fa fa-edit"></i></a> &nbsp;&nbsp;&nbsp;<a href="#"  project-id="<?php echo $row['id'];?>" image-id="<?php echo $row['id'];?>" name="remove_levels" class="remove_levels" data-toggle="modal" data-target="#myModal"><i class="fa fa-trash-o"></i></a></td>
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
			adminPost(base_url+'admin/section/delete_coldwell_banker_news/'+project_id+'/'+image_id)
		});
	});
});
</script>
