<?php
require_once("../../config.php");
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT * from `employee_payroll` where id = '{$_GET['id']}' ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=stripslashes($v);
        }
    }
}
?>
<div class="conteiner-fluid">
<form action="" id="payroll-form">
    <input type="hidden" name ="id" value="<?php echo isset($id) ? $id : '' ?>">
    <input type="hidden" name ="balance_type" value="2">
    <?php if(!isset($id)): ?>
    <div class="form-group">
        <label for="fromdata_id" class="control-label">Employee ID And Name</label>
        <select name="fromdata_id" id="fromdata_id" class="custom-select select2" required>
        <option value=""></option>
        <?php
            $qry = $conn->query("SELECT * FROM `employee_data` where `status` = 1 order by id asc");
            while($row= $qry->fetch_assoc()):
        ?>
        <option value="<?php echo $row['id'] ?>" <?php echo isset($fromdata_id) && $fromdata_id == $row['id'] ? 'selected' : '' ?> data-balance="<?php echo $row['balance'] ?>"><?php echo $row['employee_id']." [".($row['fullname'])."]" ?></option>
        <?php endwhile; ?>
        </select>
    </div>
    <?php else: ?>
        <div class="form-group">
            <label for="fromdata_id" class="control-label">Category</label>
            <input type="hidden" name="fromdata_id" value="<?php echo $fromdata_id ?>">
            <?php
            $qry = $conn->query("SELECT * FROM `employee_data` where id = '{$fromdata_id}'");
            $cat_res = $qry->fetch_assoc();
            $balance = $cat_res['balance'] + $amount;
            ?>
            <p><b><?php echo $cat_res['employee_id'] ?> [<?php echo number_format($balance) ?>]</b></p>
            <input type="hidden" id="balance" value="<?php echo $balance ?>">
        </div>
    <?php endif; ?>
    <div class="form-group">
        <label for="numberofdayswork" class="control-label">Days Work</label>
        <input name="numberofdayswork" id="numberofdayswork" class="form-control form text-right number" value="<?php echo isset($numberofdayswork) ? ($numberofdayswork) : 0; ?>">
    </div>
    <div class="form-group">
        <label for="bonus" class="control-label">Bonus</label>
        <input name="bonus" id="bonus" class="form-control form text-right number" value="<?php echo isset($bonus) ? ($bonus) : 0; ?>">
    </div>
    <div class="form-group">
        <label for="overtimepay" class="control-label">Overtime Pay</label>
        <input name="overtimepay" id="overtimepay" class="form-control form text-right number" value="<?php echo isset($overtimepay) ? ($overtimepay) : 0; ?>">
    </div>
    <div class="form-group">
        <label for="amount" class="control-label">Amount</label>
        <input name="amount" id="amount" class="form-control form text-right number" value="<?php echo isset($amount) ? ($amount) : 0; ?>">
    </div>
    <div class="form-group">
        <label for="remarks" class="control-label">Remarks</label>
        <textarea name="remarks" id="" cols="30" rows="2" class="form-control form no-resize summernote"><?php echo isset($remarks) ? $remarks : ''; ?></textarea>
    </div>
</form>
</div>
<script>
  
	$(document).ready(function(){
        $('.select2').select2({placeholder:"Please Select here",width:"relative"})
        $('.number').on('load input change',function(){
            var txt = $(this).val()
                var p = (txt.match(/[.]/g) || []).length;
                    console.log(p)
                if(txt.slice(-1) == '.' && p > 1){
                    $(this).val(txt.slice(0,-1))
                    return false;
                }
                if(txt.slice(-1) == '.'){
                    txt = txt
                }else{
                    txt = txt.split('.')
                    ntxt = ((txt[0]).replace(/\D/g,''));
                    if(!!txt[1])
                    ntxt += "."+txt[1]
                    ntxt = ntxt > 0 ? ntxt : 0;
                    txt = parseFloat(ntxt).toLocaleString('en-US')
                }
                $(this).val(txt)
        })
        $('.number').trigger('change')
		$('#payroll-form').submit(function(e){
			e.preventDefault();
            var _this = $(this)
			 $('.err-msg').remove();
             $("[name='amount']").removeClass("border-danger")
			start_loader();
            var cat_id = $("[name='fromdata_id']").val();
            var cat_balance = $('#balance').length > 0 ? $('#balance').val() : $("[name='category_id'] option[value='"+cat_id+"']").attr('data-balance');
            var amount = $("[name='amount']").val();
                amount = amount.replace(/,/g,"");
                console.log(cat_balance,amount)
                console.log(amount > cat_balance)
            if(parseFloat(amount) > parseFloat(cat_balance)){
                var el = $('<div>')
                    el.addClass("alert alert-danger err-msg mt-2").text("Entered Amount is greater than the selected category balance.")
                    $("[name='amount']").after(el)
                    el.show('slow')
                    $("[name='amount']").addClass("border-danger").focus()
                end_loader();
                return false;
            }
			$.ajax({
				url:_base_url_+"classes/Master.php?f=save_expense",
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
						location.reload()
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

	})
</script>