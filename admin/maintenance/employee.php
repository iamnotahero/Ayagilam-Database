<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title">List of Employee Data</h3>
		<div class="card-tools">
		<button type="button" class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
				                  		Company Type
				                    <span class="sr-only">Toggle Dropdown</span>
				                  </button>
				                  <div class="dropdown-menu" role="menu">
				                    <a class="dropdown-item" href="?page=maintenance/manage_empssloyee&id=<?php echo $row['id'] ?>"><span class="fa fa-edit text-primary"></span> Schengger</a>
				                    <div class="dropdown-divider"></div>
				                    <a class="dropdown-item delete_data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"><span class="fa fa-trash text-danger"></span> LBC</a>
									<div class="dropdown-divider"></div>
				                    <a class="dropdown-item delete_data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"><span class="fa fa-trash text-danger"></span> LBC</a>
									<div class="dropdown-divider"></div>
				                    <a class="dropdown-item delete_data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"><span class="fa fa-trash text-danger"></span> LBC</a>
				                  </div>			
			<a href="?page=maintenance/manage_employee" class="btn btn-flat btn-primary"><span class="fas fa-plus"></span>  Create New</a>
		</div>
	</div>
	<div class="card-body">
		<div class="container-fluid">
        <div class="container-fluid">
			<table class="table small table-bordered table-stripped">
				<colgroup>
					<col width="1%">
					<col width="8%">
					<col width="8%">
					<col width="10%">
					<col width="1%">
					<col width="5%">
					<col width="5%">
					<col width="5%">
					<col width="1%">
					<col width="1%">
					<col width="1%">
					<col width="10%">
					<col width="10%">
				</colgroup>
				<thead>
					<tr>
						<th>#</th>
						<th>Date Created</th>
						<th>Employee ID</th>
						<th>Full Name</th>
						<th>Position</th>
						<th>Date Start</th>
						<th>Driving Lisc No.</th>
						<th>Expiration</th>
						<th>Company Assignments</th>
						<th>C19 Vaccine</th>
						<th>Medical</th>
						<th>NBI</th>
						<th>Police Clearance</th>
						<th>Training Date</th>
						<th>Validity</th>
						<th>Remark</th>
						<th>Status</th>
						<th>Photo</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$i = 1;
						$qry = $conn->query("SELECT * from `employee_data` order by employee_id asc ");
						while($row = $qry->fetch_assoc()):
                            $row['description'] = strip_tags(stripslashes(html_entity_decode($row['description'])));
					?>
						<tr>
							<td class="text-center"><?php echo $i++; ?></td>
							<td><?php echo date("Y-m-d H:i",strtotime($row['date_created'])) ?></td>
							<td><?php echo $row['employee_id'] ?></td>
							<td>tamad ako mag video edit</td>
							<td class="text-center">
                                <?php if($row['position'] == 0): ?>
                                    <span>Truck Driver</span>
								<?php elseif($row['position'] == 1): ?>
									<span>Coordinator</span>
								<?php elseif($row['position'] == 2): ?>
									<span>Helper</span>
								<?php elseif($row['position'] == 3): ?>
									<span>Driver/Coor</span>
								<?php elseif($row['position'] == 4): ?>
									<span>Team Leader</span>
                                <?php else: ?>
									<!--CHANGE THIS!-->
                                    <span class="badge badge-danger">NULL</span>
                                <?php endif; ?>
                            </td>
							<td><?php echo $row['date_start'] ?></td>
							<td><?php if(empty($row['license'])): ?>
								<span class="badge badge-danger">Empty</span>
								<?php else: echo $row['license'] ?>
								<?php endif; ?>
							</td>
							<td><?php if(empty($row['expiration'])): ?>
								<span class="badge badge-danger">Empty</span>
								<?php else: echo $row['expiration'] ?>
								<?php endif; ?>
							</td>	
							<td><?php if(empty($row['company_assign'])): ?>
								<span class="badge badge-danger">Empty</span>
								<?php else: 
								$company_array = array("1"=>"James Hardie","2"=>"Huawei","3"=>"LBC","4"=>"Alaska","5"=>"ZTE"); //Change this when adding a company in manage_employee.php options
								$str_arr = explode(",",$row['company_assign']);
								$company_show = array();
								foreach ($str_arr as &$tae):  ?>
								<script>console.log(<?php echo json_encode($tae);?>)</script>
								<?php array_push($company_show,$company_array[$tae]); ?>
								<?php endforeach; ?>
								<span><?php echo implode(",",$company_show); ?></span>
								<?php endif; ?>
							</td>
							<td class="text-center">
                                <?php if($row['c19_vaccine'] == 1): ?>
                                    <span class="badge badge-success">Fully Vaccineted</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Not Specified</span>
                                <?php endif; ?>
                            </td>									
							<td class="text-center">
                                <?php if($row['medical'] == 1): ?>
                                    <span class="badge badge-success">Yes</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">No</span>
                                <?php endif; ?>
                            </td>
							<td class="text-center">
                                <?php if($row['nbi'] == 1): ?>
                                    <span class="badge badge-success">Yes</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">No</span>
                                <?php endif; ?>
                            </td>
							<td class="text-center">
                                <?php if($row['policeclearance'] == 1): ?>
                                    <span class="badge badge-success">Yes</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">No</span>
                                <?php endif; ?>
                            </td>
							<td><?php echo $row['trainingdate'] ?></td>
							<td><?php echo $row['validity'] ?></td>
							<td class ="myDIV"><p class="truncate-1 m-0" ><?php echo $row['description'] ?></p></td>
							<td class="text-center">
                                <?php if($row['status'] == 1): ?>
                                    <span class="badge badge-success">Active</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Inactive</span>
                                <?php endif; ?>
                            </td>
							<td><?php  if($row['avatar'] == "N/A"): ?>
								<span><img src="/AyagilamDatabase/uploads/default.png" class="img-circle elevation-2 temp-blur-img" alt="" id="cimg"></span>
								<?php else: ?>
								<span><img src="<?php echo validate_image($row['avatar']) ?>" class="img-circle elevation-2 temp-blur-img" alt="User Image" id="cimg"></span>
								<?php endif; ?>
							</td>
							<td align="center">
								 <button type="button" class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
				                  		Action
				                    <span class="sr-only">Toggle Dropdown</span>
				                  </button>
				                  <div class="dropdown-menu" role="menu">
				                    <a class="dropdown-item" href="?page=maintenance/manage_employee&id=<?php echo $row['id'] ?>"><span class="fa fa-edit text-primary"></span> Edit</a>
				                    <div class="dropdown-divider"></div>
				                    <a class="dropdown-item delete_data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"><span class="fa fa-trash text-danger"></span> Delete</a>
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
<style>
	img#cimg{
		filter: blur(8px);
    	-webkit-filter: blur(8px);
		height: 10vh;
		width: 10vh;
		object-fit: cover;
		border-radius: 100% 100%;
		
	}
</style>
<script>
	$(document).ready(function(){
		$('.delete_data').click(function(){
			_conf("Are you sure to delete this employee permanently? The action will remove also the budget and expense under this category","delete_employee",[$(this).attr('data-id')])
		})
		$('.table').dataTable();
	})
	function delete_employee($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_employee",
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