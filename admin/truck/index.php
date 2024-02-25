<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title">Trucks:</h3>
		<div class="card-tools">
			<a href="javascript:void(0)" id="manage_truck" class="btn btn-flat btn-sm btn-primary"><span class="fas fa-plus"></span>  Add New</a>
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
						<th>Type</th>
						<th>Plate #</th>
						<th>Driver</th>
						<th>Photo</th>
						<th>Status</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
				<?php 
					$i = 1;
						$qry = $conn->query("SELECT * from `truck_data`  order by plate_number asc "); //the status query... i might remove in the futer
						while($row = $qry->fetch_assoc()):
							foreach($row as $k=> $v){
								$row[$k] = trim(stripslashes($v));
							}
                         //echo implode(" ",$row);
					?>
						<tr>
							<td class="text-center"><?php echo $i++; ?></td>
							<td><?php if(empty($row['type'])): ?>
								<span class="badge badge-danger">Empty</span>
								<?php else: echo $row['type'] ?>
								<?php endif; ?>
							</td>
							<td><?php echo $row['plate_number'] ?></td>
							<td><?php if(empty($row['driver_name'])): ?>
								<span class="badge badge-danger">Empty</span>
								<?php else: echo $row['driver_name'] ?>
								<?php endif; ?>
							</td>	
							<td class="text-center"><?php  if($row['avatar'] == "N/A"): ?>
								<span><img src="/AyagilamDatabase/uploads/default_truck.jpg" class="img-circle elevation-2 " alt="" id="cimg"></span>
								<?php else: ?>
								<span><img src="<?php echo validate_image($row['avatar']) ?>" class="img-circle elevation-2 " alt="User Image" id="cimg"></span>
								<?php endif; ?>
							</td>
							<td class="text-center">
                                <?php if($row['status'] == 1): ?>
                                    <span class="badge badge-success">Active</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Inactive</span>
                                <?php endif; ?>
                            </td>
							<td align="center">
								 <button type="button" class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
				                  		Action
				                    <span class="sr-only">Toggle Dropdown</span>
				                  </button>
				                  <div class="dropdown-menu" role="menu">
				                    <a class="dropdown-item manage_truck" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"><span class="fa fa-edit text-primary"></span> Edit</a>
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

    	
		height: 15vh;
		width: 15vh;
		object-fit: contain;
		border-radius: 100% 100%;
		
	}
	img#ccimg{

    	
		height: 15vh;
		width: 15vh;
		object-fit: contain;
		border-radius: 100% 100%;

	}
</style>
<script>
	$(document).ready(function(){
		$('#manage_truck').click(function(){
			uni_modal("<i class='fa fa-plus'></i> Add New Truck Data",'truck/manage_truck.php')
		})
		$('.manage_truck').click(function(){
			uni_modal("<i class='fa fa-edit'></i> Update Truck Data",'truck/manage_truck.php?id='+$(this).attr('data-id'))
		})
		$('.delete_data').click(function(){
			_conf("Are you sure to delete this truck data permanently?","delete_truck_data",[$(this).attr('data-id')])
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
	function delete_truck_data($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_truck_data",
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