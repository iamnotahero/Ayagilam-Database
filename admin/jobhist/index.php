<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title">Job History:</h3>
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
					</tr>
				</thead>
				<tbody>
				<?php 
					$i = 1;
						$qry = $conn->query("SELECT * from `jobs` WHERE status = 2 order by id asc "); //the status query... i might remove in the futer
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
                                <?php if($row['status'] == 2): ?>
                                    <span class="badge badge-success">Completed</span>
                                <?php endif;?>
                            </td>		
						</tr>
					<?php endwhile; ?>
				</tbody>
			</table>
		</div>
		</div>
	</div>
</div>