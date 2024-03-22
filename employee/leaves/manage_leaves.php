<?php require_once("../../config.php");
if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?> 
<div class="container-fluid">
<form action="" id="leaves-form">
    <input type="hidden" id="fromdata_id" name ="fromdata_id" value="<?php echo $_settings->userdata('id') ?>">
    <div class="form-group">
        <label for="days" class="control-label">Days of Absense</label>
        <input name="days" id="days" class="form-control form text-right number" value="1">
    </div>
    <div class="form-group">
        <label for="type" class="control-label">Type of Leave</label>
			<select name="type" selected="0" id="type" class="custom-select select2" required>
                <option value="" disabled selected>Select Type Here</option>
                <option value="1">Sick Leave</option>
                <option value="2">Vacation Leave</option>
                <option value="3">Leave of Absence</option>
                <option value="4">Paternity Leave</option>
                <option value="5">Bereavement Leave</option>
			</select>
    </div>
    <div class="form-group">
		<label for="reason" class="control-label">Reason</label>
        <textarea name="reason" id="" cols="30" rows="2" class="form-control form no-resize summernote"></textarea>
	</div>
</form>
</div>
<script>
	$(document).ready(function(){
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
		$('#leaves-form').submit(function(e){
			e.preventDefault();
            var _this = $(this)
			 $('.err-msg').remove();
			start_loader();	
			$.ajax({
				url:_base_url_+"classes/Master.php?f=save_leaves",
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
						location.href = "./?page=leaves/index";
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
