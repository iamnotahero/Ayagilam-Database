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
<div class="container-fluid">
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
            <label for="fromdata_id" class="control-label">Employee ID</label>
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
				<label for="month_start" class="control-label">Date Start</label>
				<input id="start" type="date" class="form-control form-control-sm date-start" name="month_start" value="<?php echo isset($month_start) ? $month_start : date('Y-m-d', time());  ?>">
				<script>             
				</script>
                </select>
	</div>
    <div class="form-group">
				<label for="month_end" class="control-label">Date End</label>
				<input id="end" type="date" class="form-control form-control-sm" name="month_end" value="<?php echo isset($month_end) ? $month_end : date('Y-m-d', strtotime(' + 6 days'));  ?>">
				<script>
				</script>
                </select>
	</div>
    <div class="form-group">
        <!-- WILL SOON REMOVE  input-id transfer the php code to span-day later-->
            <label for="numberofdayswork" class="control-label">Total Days: <span id = "span-totalday" name="span-totalday"></span></label>
            <label for="numberofdayswork" class="control-label">Found Weekends: <span id = "span-weekend" name="span-weekend"></span></label>
            <label for="numberofdayswork" class="control-label">Total Work Days: <span id = "span-day" name="span-day"><?php echo isset($numberofdayswork) ? ($numberofdayswork) : ""; ?></span></label>
            <input id= "salaryoftheday" type="hidden" name="salaryoftheday" value="<?php echo isset($salaryoftheday) ? ($salaryoftheday) : "0"; ?>"> 
            <script>
            </script>
    </div>
    <div class="form-group">
        <label for="day_list" class="control-label">Days: </label>
            <div class="form-group" id="day_list">
		    </div>
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
        <label for="overtimepay" class="control-label">Gross Salary</label>
        <input name="overtimepay" id="overtimepay" class="form-control form text-right number" value="<?php echo isset($grosssalary) ? ($grosssalary) : 0; ?>">
    </div>
    <div class="form-group">
        <label for="overtimepay" class="control-label">Cash Advance</label>
        <input name="overtimepay" id="overtimepay" class="form-control form text-right number" value="<?php echo isset($cashadvance) ? ($cashadvance) : 0; ?>">
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
myStorage = window.sessionStorage;
function treatAsUTC(date) {
    var result = new Date(date);
    result.setMinutes(result.getMinutes() - result.getTimezoneOffset());
    return result;
}
function daysBetween(startDate,endDate) {
    var millisecondsPerDay = 24 * 60 * 60 * 1000;
    return (treatAsUTC(endDate) - treatAsUTC(startDate)) / millisecondsPerDay;
}
function deletealldivs(){
    var x = document.getElementById('day_list');
    while (x.firstChild) {
        x.firstChild.remove()
    }
}
function savedatatolocalstorage(){
    var x = document.getElementById('day_list');
        var y = x.getElementsByTagName('input');
        for (var i = 0; i < y.length; i += 1) {
            var date = y[i].id;
            var salary = y[i].value;
            //console.log(salary);
            if (parseFloat(salary) !== 0){
            //console.log("IN"+date+" SALARY IS NOT ZERO")
            myStorage.setItem(date, salary);
            }else{
                //console.log("THE SALARY IS ZERO");
                }
        }
}
function StoreDaySalaryToArray(){
    var x = document.getElementById('day_list');
        var y = x.getElementsByTagName('input');
        var divArray = [];
        for (var i = 0; i < y.length; i += 1) {
            var removestring = y[i].value.replaceAll(',','');
            divArray.push(removestring);
        }
        var salaries = divArray.toString();	
        document.getElementById("salaryoftheday").value = salaries;
        //console.log(company)
        //console.log(divArray)
}
function additem(strUser,date,amount,isholiday){
            if (strUser){ //add if chosen is duplicateed in the future
            var div = document.getElementById("day_list");
            var divc = document.createElement("div");
            var input = document.createElement("input");
            input.setAttribute('name',strUser);
            input.setAttribute('id',date);
            input.setAttribute('class',"form-control form text-right number");
            input.setAttribute('value',amount);
            divc.setAttribute('id',strUser+" "+isholiday);
            divc.setAttribute('class',"callout callout-info");
            divc.appendChild (document.createTextNode(strUser));
            div.appendChild(divc);
            divc.appendChild(input);
            }
} 

function getDayName(dateStr, locale){
    var date = new Date(dateStr);
    return date.toLocaleDateString(locale, {day:'numeric',
                                            weekday: 'long' });        
}

function getFullDate(dateStr, locale){
    var date = new Date(dateStr);
    return date.toLocaleDateString(locale, {day:'numeric',
                                            month: 'numeric',
                                            year: 'numeric' });        
}

function loadfromdatabase(){
    var start = document.getElementById("start");
    var end = document.getElementById("end");
    var salary = document.getElementById("salaryoftheday").value;
    let weekends = 0;
    const myArray = salary.split(",");
    console.log("mYarray = "+myArray);
var work_days = new Date(start.value);
for (var i = 0; i < daysBetween(start.value,end.value)+1; i += 1) { 
    //Working iteration in console log need to add a offset at the end of date
    if (work_days.getDay() == 6 || work_days.getDay() == 0){
        //console.log(getFullDate(work_days, "en-US")+" is a Weekend");
        weekends +=1;
    }
             additem(getDayName(work_days, "en-US"),getFullDate(work_days, "en-US"),myArray[i]);
            work_days.setDate(work_days.getDate() + 1);
            //console.log(getDayName(work_days, "en-US"));        
        }
        document.getElementById("span-totalday").innerText = daysBetween(start.value,end.value)+1;
        document.getElementById("span-weekend").innerText = weekends;
        document.getElementById("span-day").innerText = (daysBetween(start.value,end.value)+1)-weekends;
        savedatatolocalstorage();
}
function loadfromlocalstorage(){
    savedatatolocalstorage();
    deletealldivs();
    var start = document.getElementById("start");
    var end = document.getElementById("end");
    var salary = document.getElementById("salaryoftheday").value;
    let weekends = 0;
    const myArray = salary.split(",");
    //console.log("mYarray = "+myArray);
var work_days = new Date(start.value);
for (var i = 0; i < daysBetween(start.value,end.value)+1; i += 1) { 
    //Working iteration in console log need to add a offset at the end of date
    if (work_days.getDay() == 6 || work_days.getDay() == 0){
        //console.log(getFullDate(work_days, "en-US")+" is a Weekend");
        weekends +=1;
    }       
            //console.log(getDayName(work_days, "en-US"));
            if (myStorage.getItem(getFullDate(work_days, "en-US"))){
                console.log("FOUND ITEM");
            var salary = myStorage.getItem(getFullDate(work_days, "en-US"));
            }else{
                console.log("NOT FOUND ITEM");
            var salary = 0;
            }
            console.log("This is the salaray"+salary);
            additem(getDayName(work_days, "en-US"),getFullDate(work_days, "en-US"),salary);
            work_days.setDate(work_days.getDate() + 1);

        }
        document.getElementById("span-totalday").innerText = daysBetween(start.value,end.value)+1;
        document.getElementById("span-weekend").innerText = weekends;
        document.getElementById("span-day").innerText = (daysBetween(start.value,end.value)+1)-weekends;
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
}
<?php if (!empty($salaryoftheday)): ?>                   
            loadfromdatabase();
            <?php else: ?>
			loadfromlocalstorage();
			<?php endif?>
            var divs = document.getElementById("salaryoftheday").value;
            if (divs === "") {
            console.log("string is empty");
            }else{
            console.log("string is not empty");
            }
			document.getElementById("start").onchange=loadfromlocalstorage;     
            document.getElementById("end").onchange=loadfromlocalstorage; 
            //make a php function that will iterate the stored array on the database with the date on the additem
            //STORE IN A MAP ARRAY OR OBJECT AND IMPLEMENT A DELETE FUNCTION

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
            StoreDaySalaryToArray();
            loadfromlocalstorage();
            deletealldivs();
			e.preventDefault();
            var _this = $(this)
			 $('.err-msg').remove();
             $("[name='amount']").removeClass("border-danger")
			start_loader();
            var cat_id = $("[name='fromdata_id']").val();
            var cat_balance = $('#balance').length > 0 ? $('#balance').val() : $("[name='fromdata_id'] option[value='"+cat_id+"']").attr('data-balance');
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
				url:_base_url_+"classes/Master.php?f=save_payroll_expenses",
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