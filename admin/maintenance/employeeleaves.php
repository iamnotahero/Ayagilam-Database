<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title">List of Employees:</h3>
	</div>
	<div class="card-body">
		<div class="container-fluid">
        <div class="container-fluid">
		<table class="table table-bordered table-stripped">
				<colgroup>
					<col width="5%">
					<col width="20%">
					<col width="12%">
					<col width="15%">
					<col width="15%">
					<col width="15%">	
				</colgroup>
				<thead class = "text-center">
					<tr>
						<th>#</th>
						<th>Name of Employee</th>
						<th>Days will be absent</th>
						<th>Type of leave</th>
						<th>Reason</th>
						<th>Status</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody class="text-center">
					<tr>
						<?php 
					$i = 1;
						$qry = $conn->query("SELECT * from `leaves` where `status` = 0 order by id asc "); //the status query... i might remove in the futer
						while($row = $qry->fetch_assoc()):
							foreach($row as $k=> $v){
								$row[$k] = trim(stripslashes($v));
								$row['reason'] = strip_tags(stripslashes(html_entity_decode($row['reason'])));
							}
							   ?>
						<?php
			            $qry1 = $conn->query("SELECT * from `employee_data` where `employee_id` = '{$row['fromdata_id']}'"); 
						if($qry1){
							while($row1 = $qry1->fetch_assoc()):
								foreach($row1 as $k=> $v){
									$row1[$k] = trim(stripslashes($v));
								}
							
						
           				?>
						<td><?php echo $i++; ?></td>
						<td><?php echo isset($row1['fullname']) ? $row1['fullname'] : 'UNKNOWN USER ID '.$row['fromdata_id'] ?></td>
						<td><?php echo $row['days'] ?></td>
						<td class="text-center">
                            <?php if($row['type'] == 1){
                            		echo "Sick Leave";
                            	}
                            	else if($row['type'] == 2){
                            		echo "Vacation Leave";
                            	}
                            	else if($row['type'] == 3){
                            		echo "Leave of Absence";
                            	}
                            	else if($row['type'] == 4){
                            		echo "Paternity Leave";
                            	}
                            	else{
                            		echo "Bereavement Leave";
                            	}
                            	?>
                        </td>
                        <td><?php echo $row['reason'] ?></td>
						<td class="text-center">
                            <?php if($row['status'] == 1){
                            	echo '<span class="badge badge-danger">Rejected</span>';
                            } else if($row['status'] == 2){
                            	echo '<span class="badge badge-success">Accepted</span>';
                            } else{
                            	echo '<span class="badge badge-danger">Pending</span>';
                            }
                            ?>
                                
                            
                        </td>
                        <td align="center">
								 <button type="button" class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
				                  		Action
				                    <span class="sr-only">Toggle Dropdown</span>
				                  </button>
				                  <div class="dropdown-menu" role="menu">
				                    <a class="dropdown-item accept" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"></span><?php echo $row['status'] != 0 ? 'Change to Accepted' : 'Accept' ?></a>
				                    <div class="dropdown-divider"></div>
				                    <a class="dropdown-item reject" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"></span><?php echo $row['status'] != 0 ? 'Change to Rejected' : 'Reject' ?></a>
				                  </div>
							</td>
					
					</tr>
					<?php endwhile; }?>
					
						<?php endwhile; ?>
				</tbody>
			</table>
		</div>
		</div>
	</div>
</div>
<script>
	$(document).ready(function(){
		$('.accept').click(function(){
			_conf("Are you sure to Accept this?","accept",[$(this).attr('data-id')])
		})
		$('.reject').click(function(){
			_conf("Are you sure to Reject this?","reject",[$(this).attr('data-id')])
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
	function reject($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=reject",
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
	function accept($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=accept",
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