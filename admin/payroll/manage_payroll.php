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
    <input type="hidden" id="test1" name ="id" value="<?php echo isset($id) ? $id : '' ?>">
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
            $balance = $cat_res['balance']
            ?>
            <p><b><?php echo $cat_res['employee_id'] ?> [<?php echo number_format($balance) ?>]</b></p>
            <input type="hidden" id="balance" value="<?php echo $balance ?>">
        </div>
    <?php endif; ?>
    <div class="form-group">
				<label for="month_start" class="control-label">Date Start</label>
				<input id="start" type="date" class="form-control form-control-sm date-start" name="month_start" value="<?php echo isset($month_start) ? $month_start : date('Y-m-d', strtotime(' - 6 days'));  ?>">
				<script>             
				</script>
                </select>
	</div>
    <div class="form-group">
				<label for="month_end" class="control-label">Date End</label>
				<input id="end" type="date" class="form-control form-control-sm" name="month_end" value="<?php echo isset($month_end) ? $month_end : date('Y-m-d', time());  ?>">
				<script>
				</script>
                </select>
	</div>
    <div class="form-group">
        <!-- WILL SOON REMOVE  input-id transfer the php code to span-day later-->
            <label for="" class="control-label">Total Days: <span id = "span-totalday" name="span-totalday"></span></label>
            <label for="" class="control-label">Found Weekends: <span id = "span-weekend" name="span-weekend"></span></label>
            <label for="numberofdayswork" class="control-label">Total Work Days: <span id = "numberofdayswork" name="numberofdayswork">0</span></label>
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
var salaries;
var total_work;
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
    var id = document.getElementById('test1');
        var y = x.getElementsByTagName('input');
        myStorage.setItem('id',id.value);
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
        let divObject = {};
        for (var i = 0; i < y.length; i += 1) {
            let removestring = y[i].value.replaceAll(',','');
            let cur_date = y[i].id;
            console.log(cur_date);
            divObject[cur_date] = removestring;
        }
        salaries = JSON.stringify(divObject)
        //document.getElementById("salaryoftheday").value = salaries;
        //console.log(company)
}
function CalculateAllDivs(){
    var x = document.getElementById('day_list');
    var amount_all = document.getElementById('amount');
        var y = x.getElementsByTagName('input');
        let storeval = 0;
        for (var i = 0; i < y.length; i += 1) {
            if (y[i].value == 0){
                var removestring = 0;
            }else{
            removestring = y[i].value.replaceAll(',','');
            }
            var value = parseFloat(removestring);
            storeval += value;

            //console.log("THE VAL", storeval);  
            //divObject[cur_date] = removestring;
        }
        amount_all.value = storeval.toLocaleString('en-US');
        //salaries = JSON.stringify(divObject)
        //document.getElementById("salaryoftheday").value = salaries;
        //console.log(company)
}
function GetTotalWorkDays(){
    var x = document.getElementById('day_list');
    var total_work_span = document.getElementById('numberofdayswork');
        var y = x.getElementsByTagName('input');
        let storeval = 0;
        for (var i = 0; i < y.length; i += 1) {
            if (parseFloat(y[i].value.replaceAll(',','')) > 0){
                storeval += 1;
            }
        }
        total_work = parseFloat(storeval);
        total_work_span.innerText = parseFloat(storeval);
}
function additem(tab_name,date,amount){
            if (tab_name){ //add if chosen is duplicateed in the future
                var div = document.getElementById("day_list");
                var divc = document.createElement("div");
                var input = document.createElement("input");
                input.setAttribute('id',date);
                input.setAttribute('class',"form-control form text-right number");
                input.setAttribute('value',amount);
                divc.setAttribute('class',"callout callout-info");
                divc.appendChild (document.createTextNode(tab_name));
                div.appendChild(divc);
                divc.appendChild(input);
            }
} 

function getDayName(dateStr, locale){
    var date = new Date(dateStr);
    return date.toLocaleDateString(locale, {month: 'long',
                                            day:'numeric',
                                            weekday: 'long' });        
}

function getFullDate(dateStr, locale){
    var date = new Date(dateStr);
    return date.toLocaleDateString(locale, {day:'numeric',
                                            month: 'numeric',
                                            year: 'numeric' });        
}
function loadfromdatabase(){
    var id = document.getElementById('test1');
    if (myStorage.getItem('id') != id.value){
        //console.log("TRIGGERED DELETE")
        myStorage.clear();
    }
    var start = document.getElementById("start");
    var end = document.getElementById("end");
    var salary = <?php echo isset($salaryoftheday) ? (json_encode($salaryoftheday)) : 0; ?>;
    let weekends = 0;
    //console.log(salary);    
    const jsonParse = JSON.parse(salary);
    //console.log(jsonParse);
    // myArray = salary.split(",");
    //console.log("mYarray = "+myArray);
    var work_days = new Date(start.value);

    for (var i = 0; i < daysBetween(start.value,end.value)+1; i += 1) { 
        //Working iteration in console log need to add a offset at the end of date
        if (work_days.getDay() == 6 || work_days.getDay() == 0){
            //console.log(getFullDate(work_days, "en-US")+" is a Weekend");
            weekends +=1;
        }
        if (jsonParse[getFullDate(work_days, "en-US")]){
                //console.log("FOUND ITEM"+" " +jsonParse[getFullDate(work_days, "en-US")] );
            //var salary = myStorage.getItem(getFullDate(work_days, "en-US"));
            additem(getDayName(work_days, "en-US"),getFullDate(work_days, "en-US"),jsonParse[getFullDate(work_days, "en-US")]);
            }else{
                //console.log("NOT FOUND ITEM");
            additem(getDayName(work_days, "en-US"),getFullDate(work_days, "en-US"),0);
            }
            
            work_days.setDate(work_days.getDate() + 1);

    }
    document.getElementById("span-totalday").innerText = daysBetween(start.value,end.value)+1;
    document.getElementById("span-weekend").innerText = weekends;
    GetTotalWorkDays();
    savedatatolocalstorage();
}

function loadfromlocalstorage(){
    savedatatolocalstorage();
    deletealldivs();
    var start = document.getElementById("start");
    var end = document.getElementById("end");
    let weekends = 0;
    //console.log("mYarray = "+myArray);
var work_days = new Date(start.value);
//add compare date remove negative date.
for (var i = 0; i < daysBetween(start.value,end.value)+1; i += 1) { 
    //Working iteration in console log need to add a offset at the end of date
    if (work_days.getDay() == 6 || work_days.getDay() == 0){
        //console.log(getFullDate(work_days, "en-US")+" is a Weekend");
        weekends +=1;
    }       
            //console.log(getDayName(work_days, "en-US"));
            if (myStorage.getItem(getFullDate(work_days, "en-US"))){
                //console.log("FOUND ITEM"+ myStorage.getItem(getFullDate(work_days, "en-US")));
            additem(getDayName(work_days, "en-US"),getFullDate(work_days, "en-US"),myStorage.getItem(getFullDate(work_days, "en-US")));
            }else{
                //console.log("NOT FOUND ITEM");
            additem(getDayName(work_days, "en-US"),getFullDate(work_days, "en-US"),0);
            }
            work_days.setDate(work_days.getDate() + 1);

        }
        document.getElementById("span-totalday").innerText = daysBetween(start.value,end.value)+1;
        document.getElementById("span-weekend").innerText = weekends;
        CalculateAllDivs();
        GetTotalWorkDays();
        $('.number').on('load input change',function(){
            CalculateAllDivs();
            GetTotalWorkDays();
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
			document.getElementById("start").onchange=loadfromlocalstorage;     
            document.getElementById("end").onchange=loadfromlocalstorage; 
            //make a php function that will iterate the stored array on the database with the date on the additem
            //STORE IN A MAP ARRAY OR OBJECT AND IMPLEMENT A DELETE FUNCTION

	$(document).ready(function(){
        $('.select2').select2({placeholder:"Please Select here",width:"relative"})
        $('.number').on('load input change',function(){
            CalculateAllDivs();
            GetTotalWorkDays();
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
            CalculateAllDivs();
            GetTotalWorkDays();
			e.preventDefault();
            var _this = $(this)
            var data = new FormData($(this)[0]);
            data.append('numberofdayswork', total_work);
            data.append('salaryoftheday', salaries);
			$.ajax({
				url:_base_url_+"classes/Master.php?f=save_payroll_expenses",
				data: data,
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                dataType: 'json',
				error:err=>{
                    loadfromlocalstorage();
					console.log(err)
					alert_toast("An error occured",'error');
					end_loader();
				},
				success:function(resp){
					if(typeof resp =='object' && resp.status == 'success'){
                        myStorage.clear()
						location.reload()
					}else if(resp.status == 'failed' && !!resp.msg){
                        var el = $('<div>')
                            el.addClass("alert alert-danger err-msg").text(resp.msg)
                            _this.prepend(el)
                            el.show('slow')
                            $("html, body").animate({ scrollTop: _this.closest('.card').offset().top }, "fast");
                            end_loader()
                    }else if(resp.status == 'empty-failure' && !!resp.msg){
    					alert_toast(resp.msg,'error');
						end_loader();
                        console.log(resp)
                    }else if(resp.status == 'overlap' && !!resp.msg){
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