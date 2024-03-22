<?php
require_once("../../config.php");
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT * from `jobs` where id = '{$_GET['id']}' ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=stripslashes($v);
        }
    }
}
?>
<div class="container-fluid">
<form action="" id="job-form">
    <input type="hidden" id="test1" name ="id" value="<?php echo isset($id) ? $id : '' ?>">
    <!--<input type="hidden" name ="balance_type" value="2"> -->
   <!--  <?php if(!isset($id)): ?>
    <div class="form-group">
        <label for="fromdata_id" class="control-label">Job Name</label>
        <select name="fromdata_id" id="fromdata_id" class="custom-select select2" required>
        <option value=""></option>
        <?php
            $qry = $conn->query("SELECT * FROM `jobs` where `status` = 1 order by id asc");
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
            $qry = $conn->query("SELECT * FROM `jobs` where id = '{$id}'");
            $cat_res = $qry->fetch_assoc();
           // $balance = $cat_res['balance']
            ?>
            <p><b><?php echo $cat_res['plate_number'] ?> [<?php echo $cat_res['driver_name'] ?>]</b></p>
            <input type="hidden" id="balance" value="<?php echo $balance ?>">
        </div>
    <?php endif; ?>
    -->
    <div class="form-group">
				<label for="jobname" class="control-label">Job Name</label>
                <textarea name="jobname" id="" cols="30" rows="1" class="form-control form no-resize"><?php echo isset($jobname) ? $jobname : ''; ?></textarea>
	</div>
    <div class="form-group">
		<label for="jobrequester" class="control-label">Job Requester</label>
		<select name="jobrequester" id="jobrequester" class="custom-select select2" required>
        <option value=""></option>
        <?php
            $qry = $conn->query("SELECT * FROM `categories` where `status` = 1 order by id asc");
            while($row= $qry->fetch_assoc()):
        ?>
        <option value="<?php echo $row['category'] ?>" <?php echo isset($category) && $category == $row['category'] ? 'selected' : '' ?>data-balance="<?php echo $row['balance'] ?>"><?php echo $row['category']?></option>
        <?php endwhile; ?>
        </select>
   	</div>
    <div class="form-group">
        <label for="jobdoer" class="control-label">Driver's Name</label>
        <select name="jobdoer" id="jobdoer" class="custom-select select2" required>
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
                <option value="0" <?php echo isset($status) && $status == 0 ? 'selected' : '' ?>>Pending</option>
                </select>
	</div>
    
</form>
</div>
<script>
	$(document).ready(function(){
		$('#job-form').submit(function(e){
			e.preventDefault();
            var _this = $(this)
			 $('.err-msg').remove();
			start_loader();	
			$.ajax({
				url:_base_url_+"classes/Master.php?f=save_job",
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
						location.href = "./?page=addjob/index";
					}else if(resp.status == 'failed' && !!resp.msg){
                        var el = $('<div>')
                            el.addClass("alert alert-danger err-msg").text(resp.msg)
                            _this.prepend(el)
                            el.show('slow')
                            $("html, body").animate({ scrollTop: _this.closest('.card').offset()}, "fast");
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
