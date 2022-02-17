<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title">Payroll Management</h3>
		<div class="card-tools">
			<a href="javascript:void(0)" id="manage_payroll" class="btn btn-flat btn-sm btn-primary"><span class="fas fa-plus"></span>  Add New</a>
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
					<col width="12%">
					<col width="7%">
					<col width="10%">
					<col width="10%">
					<col width="15%">
					<col width="15%">
					<col width="15%">
				</colgroup>
				<thead>
					<tr>
						<th>#</th>
						<th>Date Created</th>
						<th>Employee ID</th>
						<th>Full Name</th>
						<th>Days of Work</th>
						<th>Bonus</th>
						<th>Overtime Pay</th>
						<th>Gross Salary</th>
						<th>Cash Advance</th>
						<th>Late Hours</th>
						<th>Total Deduction</th>
						<th>Net Pay</th>
						<th>Amount</th>
						<th>Remarks</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$i = 1;
						$qry = $conn->query("SELECT r.*,c.employee_id,c.fullname from `employee_payroll` r inner join `employee_data` c on r.fromdata_id = c.id where c.status= 1 and r.balance_type = 2 order by unix_timestamp(r.date_created) desc");
						while($row = $qry->fetch_assoc()):
							foreach($row as $k=> $v){
								$row[$k] = trim(stripslashes($v));
							}
                            $row['remarks'] = strip_tags(stripslashes(html_entity_decode($row['remarks'])));
					?>
						<tr>
							<td class="text-center"><?php echo $i++; ?></td>
							<td><?php echo date("Y-m-d H:i",strtotime($row['date_created'])) ?></td>
							<td><?php echo $row['employee_id'] ?></td>
							<td><?php echo $row['fullname'] ?></td>
							<td><?php echo $row['numberofdayswork'] ?></td>
							<td><?php echo $row['bonus'] ?></td>
							<td><?php echo $row['overtimepay'] ?></td>
							<td><?php echo $row['grosssalary'] ?></td>
							<td><?php echo $row['cashadvance'] ?></td>
							<td><?php echo $row['latehours'] ?></td>
							<td><?php echo $row['totaldeduction'] ?></td>
							<td><?php echo number_format($row['netpay']) ?></td>
							<td ><p class="m-0 text-right"><?php echo number_format($row['amount']) ?></p></td>
							<td ><p class="m-0 truncate"><?php echo ($row['remarks']) ?></p></td>
							<td align="center">
								 <button type="button" class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
				                  		Action
				                    <span class="sr-only">Toggle Dropdown</span>
				                  </button>
				                  <div class="dropdown-menu" role="menu">
				                    <a class="dropdown-item manage_payroll" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"><span class="fa fa-edit text-primary"></span> Edit</a>
				                    <div class="dropdown-divider"></div>
				                    <a class="dropdown-item delete_data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>" data-payroll_id="<?php echo $row['fromdata_id'] ?>"><span class="fa fa-trash text-danger"></span> Delete</a>
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
		$('#manage_payroll').click(function(){
			uni_modal("<i class='fa fa-plus'></i> Add New Payroll Data",'payroll/manage_payroll.php')
		})
		$('.manage_payroll').click(function(){
			uni_modal("<i class='fa fa-edit'></i> Update Payroll Data",'payroll/manage_payroll.php?id='+$(this).attr('data-id'))
		})
		$('.delete_data').click(function(){
			_conf("Are you sure to delete this expense permanently?","delete_payroll",[$(this).attr('data-id'),$(this).attr('data-payroll_id')])
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
		//The Fix for document.ready now working properly with modals
		$('#uni_modal').on('hidden.bs.modal', function () {
 location.reload();
})
		$('.table').dataTable({
			columnDefs: [
				{ orderable: false, targets: 5 }
			],
			order: [[0, 'asc']]
		});
	})
	function delete_expense($id,$fromdata_id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_payroll",
			method:"POST",
			data:{id: $id,fromdata_id: $fromdata_id},
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