<?php
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT * from `employee_data` where id = '{$_GET['id']}' ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=$v;
        }
    }
}
?>
<div class="card card-outline card-info">
	<div class="card-header">
		<h3 class="card-title"><?php echo isset($id) ? "Update ": "Create New " ?> Employee Data</h3>
	</div>
	<div class="card-body">
		<form action="" id="employee-form">
			<input type="hidden" name ="id" value="<?php echo isset($id) ? $id : '' ?>">
			<input type="hidden" id ="company_assign" name = "company_assign" value="<?php echo isset ($company_assign) ? $company_assign : '' ;?>">	
			<div class="form-group d-flex justify-content-center">
				<!--PUT CONDITION IF WHILE NO PICTURE *FIXED* I THINK COMEBACK TO THIS LATER IF NOT--> 	
				<?php if (!isset($avatar) or $avatar == "N/A"): ?>
								<span><img src="/expense_budget/uploads/default.png" alt=" " id="cimg" class="img-fluid img-thumbnail"></span>
								<?php else: ?>	
					<img src="<?php echo validate_image(isset($avatar) ? $avatar :'') ?>" alt="	" id="cimg" class="img-fluid img-thumbnail">
					<?php endif; ?>	
				</div>
			<div class="form-group">
				<div class="custom-file">
		            <input type="file" class="custom-file-input rounded-circle" id="customFile" name="img" onchange="displayImg(this,$(this))">
		            <label class="custom-file-label" for="customFile">Choose Employee Picture</label>
		        </div>
			</div>
			<div class="form-group">
				<label for="employee_id" class="control-label">Employee ID</label>
                <textarea name="employee_id" id="" cols="30" rows="2" class="form-control form no-resize"><?php echo isset($employee_id) ? $employee_id : ''; ?></textarea>
			</div>
			<div class="form-group">
				<label for="fullname" class="control-label">Full Name</label>
                <textarea name="fullname" id="" cols="30" rows="2" class="form-control form no-resize"><?php echo isset($fullname) ? $fullname : ''; ?></textarea>
			</div>
			<div class="form-group">
				<label for="position" class="control-label">Position</label>
				<select name="position" id="position" class="custom-select select2" required>
				<option value="0" <?php echo isset($position) && $position == 0 ? 'selected' : '' ?>>Truck Driver</option>
				<option value="1" <?php echo isset($position) && $position == 1 ? 'selected' : '' ?>>Coordinator</option>
				<option value="2" <?php echo isset($position) && $position == 2 ? 'selected' : '' ?>>Helper</option>
				<option value="3" <?php echo isset($position) && $position == 3 ? 'selected' : '' ?>>Driver/Coor</option>
				<option value="4" <?php echo isset($position) && $position == 4 ? 'selected' : '' ?>>Team Leader</option>
				</select>
   			</div>
			<div class="form-group">
				<label for="date_start" class="control-label">Date Start</label>
				<input type="date" class="form-control form-control-sm" name="date_start" value="<?php echo isset($date_start) ? $date_start : date('Y-m-d', time());  ?>">
				<script>
				</script>
                </select>
			</div>
			<div class="form-group">
				<label for="license" class="control-label">Driving Lisc No.</label>
                <textarea name="license" id="" cols="30" rows="2" class="form-control form no-resize"><?php echo isset($license) ? $license : ''; ?></textarea>
			</div>
			<div class="form-group">
				<label for="expiration" class="control-label">Expiration</label>
				<input type="date" class="form-control form-control-sm" name="expiration" value="<?php echo isset($expiration) ? $expiration : date('Y-m-d', time());  ?>">
				<script>
				</script>
                </select>
			</div>
			<div class="form-group">
			<label for="company_chosen" class="control-label">Company Assignments</label>		
			<div class="form-group" id="company_chosen">
			</div>
			<div class="form-group">
				<label for="company_list" class="control-label">Add Company</label>
				<select name="company_list" selected="0" id="company_list" class="custom-select select2" required>
				<option value="0"></option>
				<option value="1">James Hardie</option>
				<option value="2">Huawei</option>
				<option value="3">LBC</option>
				<option value="4">Alaska</option>
				<option value="5">ZTE</option>
				</select>
				<script>
					var e = document.getElementById("company_list");
					function additem(){
						var strUser = e.options[e.selectedIndex].text;
						if (strUser){ //add if chosen is duplicateed in the future
						var div = document.getElementById("company_chosen");
						var divc = document.createElement("div");
						var span = document.createElement("span");
						span.setAttribute('id',strUser);
						span.setAttribute('onclick',"removeitem(this)");
						span.setAttribute('class',"close delete-company");
						span.appendChild (document.createTextNode("x"));
						divc.setAttribute('id',e.options[e.selectedIndex].value);
						divc.setAttribute('class',"callout callout-info");
						divc.appendChild (document.createTextNode(strUser));
						div.appendChild(divc);
						divc.appendChild(span);
					////// get elements in inside the div 
						updatearraytodb()
					}
					}
					function checkhaschild(){
						var div = document.getElementById("company_chosen");
						if (div.firstChild) {
						console.log("EMPTY YAN")
					}
					}
					function updatearraytodb(){
						var x = document.getElementById('company_chosen');
					var y = x.getElementsByTagName('div');
					var divArray = [];
					for (var i = 0; i < y.length; i += 1) {
						divArray.push(y[i].id);
					}
					var company = divArray.toString();	
					//console.log(company)
					//console.log(divArray)
					document.getElementById("company_assign").value = company;
				}
				function removeitem(div){
						div.parentNode.remove();
						checkhaschild()
						updatearraytodb()
					}
					function showitem(index){
						var div = document.getElementById("company_chosen");
						var divc = document.createElement("div");
						var span = document.createElement("span");
						span.setAttribute('id',index);
						span.setAttribute('onclick',"removeitem(this)");
						span.setAttribute('class',"close delete-company");
						span.appendChild (document.createTextNode("x"));
						divc.setAttribute('id',index);
						divc.setAttribute('class',"callout callout-info");
						divc.appendChild (document.createTextNode(e.options[index].text));
						div.appendChild(divc);
						divc.appendChild(span);
						checkhaschild();
					}
					e.onchange=additem;	
					//on start show item
					<?php 
					if (!empty($company_assign)):
					$str_arr = explode(",",$company_assign);
					foreach ($str_arr as &$tae): ?> 
					console.log("CHOSEN: "+e.options[<?php echo json_encode($tae);?>].text);
					showitem(e.options[<?php echo json_encode($tae);?>].value); 
					<?php endforeach;
					else: ?>
					checkhaschild();
					<?php endif?>


					//continue here

				</script>
   			</div>
			<div class="form-group">
				<label for="c19_vaccine" class="control-label">C19 Vaccine</label>
                <select name="c19_vaccine" id="c19_vaccine" class="custom-select selevt">
				<option value="0" <?php echo isset($c19_vaccine) && $c19_vaccine == 0 ? 'selected' : '' ?>>N/A</option>
                <option value="1" <?php echo isset($c19_vaccine) && $c19_vaccine == 1 ? 'selected' : '' ?>>Fully Vaccinated</option>
                </select>
			</div>
			<div class="form-group">
				<label for="medical" class="control-label">Medical</label>
                <select name="medical" id="medical" class="custom-select selevt">
				<option value="0" <?php echo isset($medical) && $medical == 0 ? 'selected' : '' ?>>No</option>
                <option value="1" <?php echo isset($medical) && $medical == 1 ? 'selected' : '' ?>>Yes</option>
                </select>
			</div>
			<div class="form-group">
				<label for="nbi" class="control-label">NBI</label>
                <select name="nbi" id="nbi" class="custom-select selevt">
				<option value="0" <?php echo isset($nbi) && $nbi == 0 ? 'selected' : '' ?>>No</option>
                <option value="1" <?php echo isset($nbi) && $nbi == 1 ? 'selected' : '' ?>>Yes</option>
                </select>
			</div>
			<div class="form-group">
				<label for="policeclearance" class="control-label">Police Clearance</label>
                <select name="policeclearance" id="policeclearance" class="custom-select selevt">
				<option value="0" <?php echo isset($policeclearance) && $policeclearance == 0 ? 'selected' : '' ?>>No</option>
                <option value="1" <?php echo isset($policeclearance) && $policeclearance == 1 ? 'selected' : '' ?>>Yes</option>
                </select>
			</div>
			<div class="form-group">
				<label for="trainingdate" class="control-label">Training Date</label>
				<input type="date" class="form-control form-control-sm" name="trainingdate" value="<?php echo isset($trainingdate) ? $trainingdate : date('Y-m-d', time());  ?>">
				<script>
				</script>
                </select>
			</div>
			<div class="form-group">
				<label for="validity" class="control-label">Validity</label>
				<input type="date" class="form-control form-control-sm" name="validity" value="<?php echo isset($validity) ? $validity : date('Y-m-d', time());  ?>">
				<script>
				</script>
                </select>
			</div>
            <div class="form-group">
				<label for="description" class="control-label">Description</label>
                <textarea name="description" id="" cols="30" rows="2" class="form-control form no-resize summernote"><?php echo isset($description) ? $description : ''; ?></textarea>
			</div>
            <div class="form-group">
				<label for="status" class="control-label">Status</label>
                <select name="status" id="status" class="custom-select selevt">
                <option value="1" <?php echo isset($status) && $status == 1 ? 'selected' : '' ?>>Active</option>
                <option value="0" <?php echo isset($status) && $status == 0 ? 'selected' : '' ?>>Inactive</option>
                </select>
			</div>
			
		</form>
	</div>
	<div class="card-footer">
		<button class="btn btn-flat btn-primary" form="employee-form">Save</button>
		<a class="btn btn-flat btn-default" href="?page=maintenance/employee">Cancel</a>
	</div>
</div>
<style>
	img#cimg{
		height: 15vh;
		width: 15vh;
		object-fit: cover;
		border-radius: 100% 100%;
	}
</style>
<script>
	function displayImg(input,_this) {
	    if (input.files && input.files[0]) {
	        var reader = new FileReader();
	        reader.onload = function (e) {
	        	$('#cimg').attr('src', e.target.result);
	        }	

	        reader.readAsDataURL(input.files[0]);
	    }
	}
	$(document).ready(function(){
		$('#employee-form').submit(function(e){
			$("#company_list").remove();
			e.preventDefault();
            var _this = $(this)
			 $('.err-msg').remove();
			start_loader();	
			$.ajax({
				url:_base_url_+"classes/Master.php?f=save_employee",
				data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                dataType: 'json',
				error:err=>{
					console.log(err)
					alert_toast("An error occured",'error');
					end_loader();
				},
				success:function(resp){
					if(typeof resp =='object' && resp.status == 'success'){
						location.href = "./?page=maintenance/employee";
					}else if(resp.status == 'failed' && !!resp.msg){
                        var el = $('<div>')
                            el.addClass("alert alert-danger err-msg").text(resp.msg)
                            _this.prepend(el)
                            el.show('slow')
                            $("html, body").animate({ scrollTop: _this.closest('.card').offset().top }, "fast");
                            end_loader()
                    }else{
						alert_toast("An error occured",'error');
						end_loader();
                        console.log(resp)
					}
				}
			})
		})

        $('.summernote').summernote({
		        height: 200,
		        toolbar: [
		            [ 'style', [ 'style' ] ],
		            [ 'font', [ 'bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear'] ],
		            [ 'fontname', [ 'fontname' ] ],
		            [ 'fontsize', [ 'fontsize' ] ],
		            [ 'color', [ 'color' ] ],
		            [ 'para', [ 'ol', 'ul', 'paragraph', 'height' ] ],
		            [ 'table', [ 'table' ] ],
		            [ 'view', [ 'undo', 'redo', 'fullscreen', 'codeview', 'help' ] ]
		        ]
		    })
	})
</script>