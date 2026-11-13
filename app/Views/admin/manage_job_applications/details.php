<div class="row">
	<!-- left column -->
	<div class="col-md-12">
	  <!-- general form elements -->
	  <div class="box box-primary">
		<div class="box-header with-border">
		  <h3 class="box-title"></h3>
		<!-- /.box-header -->
		<!-- form start -->
		  <h3>Candidate Details</h3>
		  <ul>
		   <li>Position Name: <?php echo esc($info['position_name'] ?? '');?></li>
		   <li>Name: <?php echo esc(($info['fname'] ?? '').' '.($info['lname'] ?? ''));?></li>
		   <li>Gender: <?php echo esc($info['gender'] ?? '');?></li>
		   <li>Mobile No: <?php echo esc($info['mobile_no'] ?? '');?></li>
		   <li>Telephone: <?php echo esc($info['phone_no'] ?? '');?></li>
		   <li>Email Id: <?php echo esc($info['email_id'] ?? '');?></li>
		   <li>Address: <?php echo esc($info['address'] ?? '');?></li>
		   <li>Download File: <a href="<?php echo base_url().'admin/section/get_candidate_resume/'.($info['id'] ?? '')?>">Download</a></li>
		   <li>Created Date: <?php echo esc($info['created_date'] ?? '');?></li>
		  </ul>
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
