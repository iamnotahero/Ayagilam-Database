<?php
require_once("../../config.php");
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT * from `truck_data` where id = '{$_GET['id']}' ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=stripslashes($v);
        }
    }
}
?>
<div class="container-fluid">
<form action="" id="truck-form">
    <input type="hidden" id="test1" name ="id" value="<?php echo isset($id) ? $id : '' ?>">
    <!--<input type="hidden" name ="balance_type" value="2"> -->
   <!--  <?php if(!isset($id)): ?>
    <div class="form-group">
        <label for="fromdata_id" class="control-label">Truck Plate Number#</label>
        <select name="fromdata_id" id="fromdata_id" class="custom-select select2" required>
        <option value=""></option>
        <?php
            $qry = $conn->query("SELECT * FROM `truck_data` where `status` = 1 order by id asc");
            while($row= $qry->fetch_assoc()):
        ?>
        <option value="<?php echo $row['id'] ?>" <?php echo isset($fromdata_id) && $fromdata_id == $row['id'] ? 'selected' : '' ?> data-balance="<?php echo $row['balance'] ?>"><?php echo $row['employee_id']." [".($row['fullname'])."]" ?></option>
        <?php endwhile; ?>
        </select>
    </div>
    <?php else: ?>
        <div class="form-group">
            <label for="fromdata_id" class="control-label">Truck Plate Number#</label>
            <input type="hidden" name="fromdata_id" value="<?php echo $fromdata_id ?>">
            <?php
            $qry = $conn->query("SELECT * FROM `truck_data` where id = '{$id}'");
            $cat_res = $qry->fetch_assoc();
           // $balance = $cat_res['balance']
            ?>
            <p><b><?php echo $cat_res['plate_number'] ?> [<?php echo $cat_res['driver_name'] ?>]</b></p>
            <input type="hidden" id="balance" value="<?php echo $balance ?>">
        </div>
    <?php endif; ?>
    -->
    <div class="form-group d-flex justify-content-center">
				<!--PUT CONDITION IF WHILE NO PICTURE *FIXED* I THINK COMEBACK TO THIS LATER IF NOT--> 	
				<?php if (!isset($avatar) or $avatar == "N/A"): ?>
								<span><img src="/AyagilamDatabase/uploads/default_truck.jpg" alt=" " id="ccimg" class="img-fluid img-thumbnail "></span>
								<?php else: ?>	
					<img src="<?php echo validate_image(isset($avatar) ? $avatar :'') ?>" alt="	" id="ccimg" class="img-fluid img-thumbnail ">
					<?php endif; ?>	
	</div>
    <div class="form-group">
				<div class="custom-file">
		            <input type="file" class="custom-file-input rounded-circle" id="customFile" name="img" accept=".png, .jpg" onchange="displayImg(this,$(this))">
		            <label class="custom-file-label" for="customFile">Choose Truck Picture</label>
		        </div>
	</div>
    <div class="form-group">
				<label for="plate_number" class="control-label">Plate Number</label>
                <textarea name="plate_number" id="" cols="30" rows="2" class="form-control form no-resize"><?php echo isset($plate_number) ? $plate_number : ''; ?></textarea>
	</div>
    <div class="form-group">
				<label for="type" class="control-label">Type</label>
				<select name="type" id="type" class="custom-select select2" required>
				<option value="0" <?php echo isset($type) && $type == 0 ? 'selected' : '' ?>>Truck Driver</option>
				<option value="1" <?php echo isset($type) && $type == 1 ? 'selected' : '' ?>>Coordinator</option>
				<option value="2" <?php echo isset($type) && $type == 2 ? 'selected' : '' ?>>Helper</option>
				<option value="3" <?php echo isset($type) && $type == 3 ? 'selected' : '' ?>>Driver/Coor</option>
				<option value="4" <?php echo isset($type) && $type == 4 ? 'selected' : '' ?>>Team Leader</option>
				</select>
   			</div>
    <div class="form-group">
        <label for="driver_name" class="control-label">Driver's Name</label>
        <select name="driver_name" id="driver_name" class="custom-select select2" required>
        <option value=""></option>
        <?php
            $qry = $conn->query("SELECT * FROM `employee_data` where `status` = 1 order by id asc");
            while($row= $qry->fetch_assoc()):
        ?>
        <option value="<?php echo $row['fullname'] ?>" <?php echo isset($driver_name) && $driver_name == $row['fullname'] ? 'selected' : '' ?> data-balance="<?php echo $row['balance'] ?>"><?php echo $row['employee_id']." [".($row['fullname'])."]" ?></option>
        <?php endwhile; ?>
        </select>
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
<script>
    function displayImg(input,_this) {
	    if (input.files && input.files[0]) {
	        var reader = new FileReader();
	        reader.onload = function (e) {
	        	$('#ccimg').attr('src', e.target.result); // changed to #ccimg there's an overlap of variables.
	        }	

	        reader.readAsDataURL(input.files[0]);
	    }
	}
	$(document).ready(function(){
		$('#truck-form').submit(function(e){
			e.preventDefault();
            var _this = $(this)
			 $('.err-msg').remove();
			start_loader();	
			$.ajax({
				url:_base_url_+"classes/Master.php?f=save_truck",
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
						location.href = "./?page=truck/index";
					}else if(resp.status == 'failed' && !!resp.msg){
                        var el = $('<div>')
                            el.addClass("alert alert-danger err-msg").text(resp.msg)
                            _this.prepend(el)
                            el.show('slow')
                            $("html, body").animate({ scrollTop: _this.closest('.card').offset().top }, "fast");
                            end_loader()
					}else if(resp.status == 'wrong-extension' && !!resp.msg){
						alert_toast(resp.msg,'error');
						end_loader();
                        console.log(resp)
                    }else if(resp.status == 'size-limit' && !!resp.msg){
						alert_toast(resp.msg,'error');
						end_loader();
                        console.log(resp)
                    }else{
						alert_toast("An error occured",'error');
						end_loader();
                        console.log(resp)
					}
				}
			})
		})
    })
</script>
