<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title"><a href="javascript:void(0)" id="file_leave" class="btn btn-lg btn-primary"><i class="fas fa-file-alt"></i>  File a Leave</a></h3>
	</div>
	<div class="card-body">
		<div class="container-fluid">
        <div class="container-fluid">
			<table class="table table-bordered table-stripped">
				<colgroup>
					<col width="5%">
					<col width="30%">
					<col width="30%">
					<col width="30%">
				</colgroup>
				<thead class="text-center">
					<tr>
						<th>EmployeeID</th>
						<th>Date</th>
						<th>Type</th>
						<th>Status</th>
					</tr>
				</thead>
				<input type="hidden" name ="id" value="<?php $id = $_settings->userdata('id'); ?>">
				<tbody>
					<?php $i=1;
						$qry = $conn->query("SELECT * from `leaves` where `fromdata_id` = '$id'"); 
						while($row = $qry->fetch_assoc()):
							foreach($row as $k=> $v){
								$row[$k] = trim(stripslashes($v));
							}
					 ?>
					 <tr>
					 	<td><?php echo $row['fromdata_id']; ?></td>
					 	<td><?php echo $row['date_created']; ?></td>
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
					 	<td class="text-center">
                            <?php if($row['status'] == 1){
                            	echo '<span class="badge badge-danger">Rejected</span>';
                            } else if($row['status']==2){
                            	echo '<span class="badge badge-success">Accepted</span>';
                            } else{
                            	echo '<span class="badge badge-danger">Pending</span>';
                            }
                            ?>
                                
                            
                        </td>
					 </tr>
					 <?php endwhile; ?>
				</tbody>
			</table>
		</div>
		</div>
	</div>

<script>
		$(document).ready(function(){
		$('#file_leave').click(function(){
			uni_modal("<i class='fas fa-file-alt'></i> File a Leave",'leaves/manage_leaves.php')
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
	})
</script>
