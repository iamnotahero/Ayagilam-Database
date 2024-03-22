<?php
require_once("../../config.php");
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT * from `employee_balance` where id = '{$_GET['id']}' ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=stripslashes($v);
        }
    }
}
?>
<div class="conteiner-fluid">
<form action="" id="employee-form">
    <input type="hidden" name ="id" value="<?php echo isset($id) ? $id : '' ?>">
    <!--    <input type="hidden" name ="balance_type" value="2">STATIC VARIABLE OF BALANCE TYPE!-->
    <?php if(!isset($id)): ?>
    <div class="form-group">
        <label for="person_id" class="control-label">Employee ID</label>
        <select name="person_id" id="person_id" class="custom-select select2" required>
        <option value=""></option>
        <?php
            $qry = $conn->query("SELECT * FROM `employee_data` order by employee_id asc");
            while($row= $qry->fetch_assoc()):
        ?>
        <!--ADD FULL NAME!-->
        <option value="<?php echo $row['id'] ?>" <?php echo isset($person_id) && $person_id == $row['id'] ? 'selected' : '' ?> data-balance="<?php echo $row['balance'] ?>"><?php echo $row['employee_id'] ?> - <?php echo $row['fullname']." [".number_format($row['balance'])."]" ?></option>
        <?php endwhile; ?>
        </select>
    </div>
    <div class="form-group">
				<label for="balance_type" class="control-label">Budget or Expense</label>
                <select name="balance_type" id="balance_type" class="custom-select selevt">
                <option value="1" <?php echo isset($balance_type) && $balance_type == 1 ? 'selected' : '' ?>>Budget</option>
                <option value="2" <?php echo isset($balance_type) && $balance_type == 2 ? 'selected' : '' ?>>Expense</option>
                </select>
	</div>
    <?php else: ?>
        <div class="form-group">
            <label for="person_id" class="control-label">Employee ID</label>
            <input type="hidden" name="person_id" value="<?php echo $person_id ?>">
            <?php
            $qry = $conn->query("SELECT * FROM `employee_data` where id = '{$person_id}'");
            $cat_res = $qry->fetch_assoc();
            $balance = $cat_res['balance'] +$amount;
            ?>
            <p><b><?php echo $cat_res['employee_id'] ?> "<?php echo $cat_res['fullname'] ?>"[<?php echo number_format($balance) ?>]</b></p>

            <input type="hidden" id="balance" value="<?php echo $balance ?>">
            <input type="hidden" id="fullname" value="<?php echo $fullname ?>">
        </div>

    <div class="form-group text-center">
    <td class="text-center">
                                <?php if($balance_type == 1): ?>
                                    <span class="badge badge-success">Budget</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Expense</span>
                                <?php endif; ?>
             </td>
    </div>            	
    <?php endif; ?>
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
		$('#employee-form').submit(function(e){
			e.preventDefault();
            var _this = $(this)
			 $('.err-msg').remove();
             $("[name='amount']").removeClass("border-danger")
			start_loader();
            var cat_type = $("[name='balance_type']").val();
           if(cat_type == 2){
            var cat_id = $("[name='person_id']").val();
            var cat_balance = $('#balance').length > 0 ? $('#balance').val() : $("[name='person_id'] option[value='"+cat_id+"']").attr('data-balance');
            var amount = $("[name='amount']").val();
                amount = amount.replace(/,/g,"");
                console.log(cat_balance,amount)
                console.log(amount > cat_balance)
            if(parseFloat(amount) > parseFloat(cat_balance)){
                var el = $('<div>')
                    el.addClass("alert alert-danger err-msg mt-2").text("Entered Amount is greater than the Employee Budget.")
                    $("[name='amount']").after(el)
                    el.show('slow')
                    $("[name='amount']").addClass("border-danger").focus()
                end_loader();
                return false;
            }
          }
			$.ajax({
				url:_base_url_+"classes/Master.php?f=save_employee_expenses",
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