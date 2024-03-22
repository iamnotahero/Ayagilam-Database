<?php require_once("../config.php");
if($_settings->chk_flashdata('success')): ?>
<script>
    alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>

<style>
  .info-tooltip,.info-tooltip:focus,.info-tooltip:hover{
    background:unset;
    border:unset;
    padding:unset;
  
</style>
<h1>Good Day! Welcome to Ayagilam Trucking Services!</h1>
<style>
.job-feed {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            padding: 20px;
        }

        .job-card {
            width: 300px;
            border: 1px solid #ddd;
            margin: 10px;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background: white;
        }
        .job{
            color: white;
            padding: 1em;
            text-align: center;
        }
        .job-card h2{
            font-size: 20px;
            font-weight: bold;
        }
        .applyBtn{
            display: block;
            margin-top: 15px;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
</style>
<hr>
<div class="row">
  <div class="col-lg-12">
    <h4>Attendance</h4>
    <hr>
    <div class="card card-outline card-primary">
  <div class="card-header">
    <div class="card-tools">
    </div>
  </div>
    <div class="card-body">
    <form action="" id="clockin">
      <input type="hidden" name ="id" value="<?php echo $_settings->userdata('id') ?>">
        <button class="btn btn-primary btn-lg btn-block" style="background-color: #38761d;margin-top:20px;" form="clockin">Clock in</button>
    </form>
    <form action="" id="clockout">
        <input type="hidden" name ="id" value="<?php echo $_settings->userdata('id') ?>">
        <button class="btn btn-primary btn-lg btn-block" style="background-color: #990000; margin-top:20px;" form="clockout">Clock out</button>
    </form>
  </div>
</div>
<hr>
<div class="row">
  <div class="col-lg-12">
    <h4>Available Jobs</h4>
    <hr>
  </div>
</div>
<div class="card card-outline card-primary">
  <div class="card-header">
  </div>
  <?php $fullname = $_settings->userdata('firstname').' '.$_settings->userdata('lastname');
        $qry = $conn->query("SELECT * FROM jobs where jobdoer = '$fullname' and status!=2");
        while($row = $qry->fetch_assoc()):
            foreach($row as $k=> $v){
            $row[$k] = trim(stripslashes($v));
        }  
  ?>
    <div class="card-body">
        <div class ="job-feed">
            <div class="job-card">
                <h2> Job Title: <?php   echo $row['jobname']; ?></h2>
                <input type="hidden" name ="jobReq" value="<?php echo $row['jobrequester'] ?>">
                <p> Company: <?php  echo $row['jobrequester']; ?></p>
                <?php   if($row['status']==0){ ?>
                <form action="" id="Accept">
                    <input type="hidden" name ="jobID" value="<?php echo $row['id'] ?>">
                    <button class ="applyBtn" form ="Accept">Accept</button>
                </form>
                <?php   } ?>
                <?php   if($row['status']==1){ ?>
                <form action="" id="Delivered">
                        <input type="hidden" name="jobID" value="<?php echo $row['id'] ?>">
                        <button class="applyBtn" form="Delivered">Delivered</button>
                </form>
            <?php   } ?>
            </div>
        </div>
    <?php endwhile; ?>
<script>
    $(document).ready(function(){
        $('#clockin').submit(function(e){
            e.preventDefault();
            var _this = $(this)
             $('.err-msg').remove();
            start_loader(); 
            $.ajax({
                url:_base_url_+"classes/Master.php?f=clock_in",
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
                    if(typeof resp =='object' &&resp.status == 'success'){
                        location.reload();
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
        $('#clockout').submit(function(e){
            e.preventDefault();
            var _this = $(this)
             $('.err-msg').remove();
            start_loader(); 
            $.ajax({
                url:_base_url_+"classes/Master.php?f=clock_out",
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
                    if(typeof resp =='object' &&resp.status == 'success'){
                        location.reload();
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
        $('#Accept').submit(function(e){
            e.preventDefault();
            var _this = $(this)
             $('.err-msg').remove();
            start_loader(); 
            $.ajax({
                url:_base_url_+"classes/Master.php?f=update_status",
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
                    if(typeof resp =='object' &&resp.status == 'success'){
                        location.reload();
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
        $('#Delivered').submit(function(e){
            e.preventDefault();
            var _this = $(this)
             $('.err-msg').remove();
            start_loader(); 
            $.ajax({
                url:_base_url_+"classes/Master.php?f=delivered",
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
                    if(typeof resp =='object' &&resp.status == 'success'){
                        location.reload();
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