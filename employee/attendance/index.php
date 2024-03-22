<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title">Attendance History:</h3>
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
				<thead>
					<tr>
						<th>#</th>
						<th>Date</th>
						<th>In</th>
						<th>Out</th>
					</tr>
				</thead>
				<input type="hidden" name ="id" value="<?php $id = $_settings->userdata('id'); ?>">
				<tbody>
					<?php $i=1;
						$qry = $conn->query("SELECT * from `employee_time_logs` where `employee_id` = '$id'  order by time_in asc "); //the status query... i might remove in the futer
						while($row = $qry->fetch_assoc()):
							foreach($row as $k=> $v){
								$row[$k] = trim(stripslashes($v));
							}
					 ?>
					 <tr>
					 	<td><?php echo $i++ ?></td>
					 	<td><?php echo $row['Tdate']; ?></td>
					 	<td><?php echo $row['time_in']; ?></td>
					 	<td><?php echo $row['time_out']; ?></td>
					 </tr>
					 <?php endwhile; ?>
				</tbody>
			</table>
		</div>
		</div>
	</div>
</div>