<div class="row">
    <div class="col-xs-12">		  
		  <div class="box">
			<div class="box-header">
			  <h3 class="box-title"><?php echo esc($form_title);?></h3>
			</div>
			<?php if(session()->getFlashdata('error')){?>
			<div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-ban"></i> Alert!</h4>
                <?php echo esc(session()->getFlashdata('error')); ?>
             </div>
            <?php  } ?> 
			<form action="<?php echo base_url().'admin/section/export_to_excell/';?>" method="post">
				<?= csrf_field() ?>
				<div class="form-group">
				<label>Date range:</label>
				<div class="input-group col-xs-5">
					<div class="input-group-addon">
					<i class="fa fa-calendar"></i>
					</div>
					<input type="text" class="form-control pull-right" id="reservation" name="reservation">
				</div>
				<!-- /.input group -->
				</div>
				<div class="col-xs-2">
					<button type="submit" class="btn btn-block btn-primary" id="export_to_excell">Export Date Wise</button>
				</div>
			</form>
			<br><br>
			<div class="col-xs-2">
				<a href="<?php echo base_url().'admin/section/export_to_excell/all'?>" class="btn btn-block btn-primary" id="export_all">Export All</a>
			</div>
			<br><br><br>
			<!-- /.box-header -->
			<div class="box-body">
			  <table id="example1" class="table table-bordered table-striped">
				<thead>
				<tr>
				  <th>#</th>
				  <th>Name</th>
				  <th>Email ID</th>
				  <th>Phone No.</th>
				  <th>Message</th>
				  <th>Interested In</th>
				  <th>Action</th>
				</tr>
				</thead>
				<tbody>
				<?php  $i=1;
				foreach ($info as $row) { ?>
				<tr>
				  <td><?php echo $i;?></td>
				  <td><?php echo esc($row['name']);?></td>
				  <td><?php echo esc($row['email']);?></td>
				  <td><?php echo esc($row['phone']);?></td>				
				  <td><?php echo esc($row['message']);?></td>	
				  <td><?php echo esc($row['interested_in']);?></td>	
				  <td><a href=""  contact-id="<?php echo $row['id'];?>" name="remove_levels" class="remove_levels" data-toggle="modal" data-target="#myModal"><i class="fa fa-trash-o"></i></a></td>				  
				</tr>
				<?php $i++;} ?>
				</tbody>
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
		var contact_id = $(this).attr("contact-id");
		var base_url = "<?php echo base_url();?>";
		$(document).on('click', '#delete', function (e) {			
			adminPost(base_url+'admin/section/delete_contact/'+contact_id);
		});
	});
});
</script>
