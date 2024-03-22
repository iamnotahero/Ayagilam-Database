<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title">Add Jobs:</h3>
		<div class="card-tools">
			<a href="javascript:void(0)" id="Addjobs" class="btn btn-flat btn-sm btn-primary"><span class="fas fa-plus"></span>  Add New</a>
		</div>
	</div>
	<div class="card-body">
		<div class="container-fluid">
        <div class="container-fluid">
		<table class="table table-bordered table-stripped">
				<colgroup>
					<col width="5%">
					<col width="15%">
					<col width="20%">
					<col width="15%">
				</colgroup>
				<thead>
					<tr>
						<th>#</th>
						<th>Job Name</th>
						<th>Job Requester</th>
						<th>Driver</th>
						<th>Status</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
				<?php 
					$i = 1;
						$qry = $conn->query("SELECT * from `jobs` WHERE NOT `status` = 2   order by id asc "); //the status query... i might remove in the futer
						while($row = $qry->fetch_assoc()):
							foreach($row as $k=> $v){
								$row[$k] = trim(stripslashes($v));
							}
                         //echo implode(" ",$row);
					?>
					<tr>
							<td class="text-center"><?php echo $i++; ?></td>
							<td><?php if(empty($row['jobname'])): ?>
								<span class="badge badge-danger">Empty</span>
								<?php else: echo $row['jobname'] ?>
								<?php endif; ?>
							</td>
							<td><?php echo $row['jobrequester'] ?></td>
							<td><?php if(empty($row['jobdoer'])): ?>
								<span class="badge badge-danger">Empty</span>
								<?php else: echo $row['jobdoer'] ?>
								<?php endif; ?>
							</td>	
							<td class="text-center">
                                <?php if($row['status'] == 1): ?>
                                    <span class="badge badge-success">Active</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Pending</span>
                                <?php endif; ?>
                            </td>
							<td align="center">
								 <button type="button" class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
				                  		Action
				                    <span class="sr-only">Toggle Dropdown</span>
				                  </button>
				                  <div class="dropdown-menu" role="menu">
				                    <a class="dropdown-item manage_jobs" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"><span class="fa fa-edit text-primary"></span> Edit</a>
				                    <div class="dropdown-divider"></div>
				                    <a class="dropdown-item delete_job" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"><span class="fa fa-trash text-danger"></span> Delete</a>
				                  </div>
							</td>
						</tr>
					<?php endwhile; ?>
				</tbody>
			</table>
		</div>
		</div>
	</div>
</div>
<script>
	$(document).ready(function(){
		$('#Addjobs').click(function(){
			uni_modal("<i class='fa fa-plus'></i> Add New Job",'addjob/Addjobs.php')
		})
		$('.manage_jobs').click(function(){
			uni_modal("<i class='fa fa-edit'></i> Update Job Data",'addjob/Addjobs.php?id='+$(this).attr('data-id'))
		})
		$('.delete_job').click(function(){
			_conf("Are you sure to delete this Job data permanently?","delete_job",[$(this).attr('data-id')])
		})
		$('#uni_modal').on('show.bs.modal',function(){
			$('.summernote').summernote({
		        height: 200,
		        toolbar: [
		            [ 'font', [ 'bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear'] ],
		            [ 'fontsize', [ 'fontsize' ] ],
		            [ 'para', [ 'ol', 'ul' ] ],
		            [ 'view', [ 'undo', 'redo'] ]
		        ]
		    })
		})
		$('.table').dataTable({
			columnDefs: [
				{ orderable: false, targets: 5 }
			],
			order: [[0, 'asc']]
		});
	})
	function delete_job($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_job",
			method:"POST",
			data:{id: $id},
			dataType:"json",
			error:err=>{
				console.log(err)
				alert_toast("An error occured.",'error');
				end_loader();
			},
			success:function(resp){
				if(typeof resp== 'object' && resp.status == 'success'){
					location.reload();
				}else{
					alert_toast("An error occured.",'error');
					end_loader();
				}
			}
		})
	}
</script>