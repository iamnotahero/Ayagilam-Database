<?php
require_once('../config.php');
Class Master extends DBConnection {
	private $settings;
	public function __construct(){
		global $_settings;
		$this->settings = $_settings;
		parent::__construct();
	}
	public function __destruct(){
		parent::__destruct();
	}
	function capture_err(){
		if(!$this->conn->error)
			return false;
		else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
			return json_encode($resp);
			exit;
		}
	}
	function save_category(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id','description'))){
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$v}' ";
			}
		}
		if(isset($_POST['description'])){
			if(!empty($data)) $data .=",";
				$data .= " `description`='".addslashes(htmlentities($description))."' ";
		}
		$check = $this->conn->query("SELECT * FROM `categories` where `category` = '{$category}' ".(!empty($id) ? " and id != {$id} " : "")." ")->num_rows;
		if($this->capture_err())
			return $this->capture_err();
		if($check > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Category already exist.";
			return json_encode($resp);
			exit;
		}
		if(empty($id)){
			$sql = "INSERT INTO `categories` set {$data} ";
			$save = $this->conn->query($sql);
		}else{
			$sql = "UPDATE `categories` set {$data} where id = '{$id}' ";
			$save = $this->conn->query($sql);
		}
		if($save){
			$resp['status'] = 'success';
			if(empty($id))
				$this->settings->set_flashdata('success',"New Category successfully saved.");
			else
				$this->settings->set_flashdata('success',"Category successfully updated.");
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		return json_encode($resp);
	}
	function save_employee(){
		//var_dump($_POST);
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id','description'))){
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$v}' ";
			}
		}
		$ext = pathinfo($_FILES['img']['name'], PATHINFO_EXTENSION);
		if(in_array($ext, array("png", "txt", "jpg", ""))){
			if($_FILES['img']['size'] > 625000){ // check file size is above limit	
				$resp['status'] = 'size-limit';
				$resp['msg'] = "File size Above limit 5MB ";
				return json_encode($resp);
				exit;
   			}
			if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != ''){
				$fname = 'uploads/'.strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
				$move = move_uploaded_file($_FILES['img']['tmp_name'],'../'. $fname);
				if($move){
					$data .=" , avatar = '{$fname}' ";
					//removed the tangina mo
				}
			}
		}else{
				$resp['status'] = 'wrong-extension';
				$resp['msg'] = "File not supported.";
				return json_encode($resp);
				exit;
		}
		if(isset($_POST['description'])){
			if(!empty($data)) $data .=",";
				$data .= " `description`='".addslashes(htmlentities($description))."' ";
		}
		$check = $this->conn->query("SELECT * FROM `employee_data` where `employee_id` = '{$employee_id}' ".(!empty($id) ? " and id != {$id} " : "")." ")->num_rows;
		if($this->capture_err())
			return $this->capture_err();
		if($check > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Employee ID already exist.";
			return json_encode($resp);
			exit;
		}
		if(empty($id)){
			$sql = "INSERT INTO `employee_data` set {$data} ";
			$save = $this->conn->query($sql);
		}else{
			$sql = "UPDATE `employee_data` set {$data} where id = '{$id}' ";
			$save = $this->conn->query($sql);
		}
		if($save){
			$resp['status'] = 'success';
			if(empty($id))
				$this->settings->set_flashdata('success',"New Employee successfully saved.");
			else
				$this->settings->set_flashdata('success',"Employee successfully updated.");
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		return json_encode($resp);
	}
	function delete_category(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `categories` where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Category successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	function delete_employee(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `employee_data` where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Employee successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	function update_balance($category_id){
		$budget = $this->conn->query("SELECT SUM(amount) as total FROM `running_balance` where `balance_type` = 1 and `category_id` = '{$category_id}' ")->fetch_assoc()['total'];
		$expense = $this->conn->query("SELECT SUM(amount) as total FROM `running_balance` where `balance_type` = 2 and `category_id` = '{$category_id}' ")->fetch_assoc()['total'];
		$balance = $budget - $expense;
		$update  = $this->conn->query("UPDATE `categories` set `balance` = '{$balance}' where `id` = '{$category_id}' ");
		if($update){
			return true;
		}else{
			return $this->conn;
		}
	}
	function update_pay($fromdata_id){
		//last stop here fixed adding all
		$payroll_amount = $this->conn->query("SELECT SUM(amount) as total FROM `employee_payroll` where `balance_type` = 2 and `fromdata_id` = '{$fromdata_id}' ")->fetch_assoc()['total'];
		$update  = $this->conn->query("UPDATE `employee_data` set `total_income` = '{$payroll_amount}' where `id` = '{$fromdata_id}' ");
		if($update){
			return true;
		}else{
			return $this->conn;
		}
	}
	function save_budget(){
		extract($_POST);
		$_POST['amount'] = str_replace(',','',$_POST['amount']);
		$_POST['remarks'] = addslashes(htmlentities($_POST['remarks']));
		$data = "";
		foreach($_POST as $k =>$v){
			if($k == 'id')
				continue;
			if(!empty($data)) $data .=",";
			$data .= " `{$k}`='{$v}' ";
		}
		if(!empty($data)) $data .=",";
			$data .= " `user_id`='{$this->settings->userdata('id')}' ";
		if(empty($id)){
			$sql = "INSERT INTO `running_balance` set $data";
		}else{
			$sql = "UPDATE `running_balance` set $data WHERE id ='{$id}'";
		}
		$save = $this->conn->query($sql);
		if($save){
			$update_balance =$this->update_balance($_POST['category_id']);
			
			if($update_balance == 1){
				$resp['status'] ='success';
				$this->settings->set_flashdata('success'," Budget successfully saved.");
			}else{
				$resp['status'] = 'failed';
				$resp['error'] = $update_balance;
			}
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn;
		}
		return json_encode($resp);
	}

	function delete_budget(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `running_balance` where id = '{$id}'");
		if($del){
			$update_balance =$this->update_balance($category_id);
			if($update_balance == 1){
				$resp['status'] ='success';
				$this->settings->set_flashdata('success',"Budget successfully deleted.");
			}else{
				$resp['status'] = 'failed';
				$resp['error'] = $update_balance;
			}
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
	function save_expense(){
		extract($_POST);
		$_POST['amount'] = str_replace(',','',$_POST['amount']);
		$_POST['remarks'] = addslashes(htmlentities($_POST['remarks']));
		$data = "";
		foreach($_POST as $k =>$v){
			if($k == 'id')
				continue;
			if(!empty($data)) $data .=",";
			$data .= " `{$k}`='{$v}' ";
		}
		if(!empty($data)) $data .=",";
			$data .= " `user_id`='{$this->settings->userdata('id')}' ";
		if(empty($id)){
			$sql = "INSERT INTO `running_balance` set $data";
		}else{
			$sql = "UPDATE `running_balance` set $data WHERE id ='{$id}'";
		}
		$save = $this->conn->query($sql);
		if($save){
			$update_balance =$this->update_balance($_POST['category_id']);
			
			if($update_balance == 1){
				$resp['status'] ='success';
				$this->settings->set_flashdata('success'," Expense successfully saved.");
			}else{
				$resp['status'] = 'failed';
				$resp['error'] = $update_balance;
			}
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn;
		}
		return json_encode($resp);
	}
	function save_payroll_expenses(){
		//var_dump($_POST);
		extract($_POST);
		$_POST['amount'] = str_replace(',','',$_POST['amount']);
		$_POST['remarks'] = addslashes(htmlentities($_POST['remarks']));
		$data = "";
		foreach($_POST as $k =>$v){
			if($k == 'id')
				continue;
			if(!empty($data)) $data .=",";
			$data .= " `{$k}`='{$v}' ";
		}
		//check if month exist or not COMPARE DATE IN PHP IS POSSIBLE AND CONVERT JSON TO ARRAY
		$test = json_decode($salaryoftheday,TRUE);
		/*foreach($test as $x => $x_value) {
			echo "Key=" . $x . ", Value=" . $x_value;
			echo "<br>";
		  }*/
		$check = $this->conn->query("SELECT id, month_start, month_end FROM `employee_payroll` where `fromdata_id` = '{$fromdata_id}' ".(!empty($id) ? " and id != {$id} " : "")." ");
		$numrow = array();
		if (mysqli_num_rows($check) > 0) {
			// output data of each row
			while($row = mysqli_fetch_assoc($check)) {
				foreach($test as $x => $x_value) {
					/*echo "Key=" . date($x) . ", Value=" . $x_value;
					echo "<br>";
					echo "month_start: " . date('m/d/Y', strtotime($row["month_start"])). " - month end: " . $row["month_end"]. " <br>"; */
					if ((date('m/d/Y', strtotime($x)) >= date('m/d/Y', strtotime($row["month_start"]))) && (date('m/d/Y', strtotime($x)) <= date('m/d/Y', strtotime($row["month_end"])))){
						array_push($numrow, "DATE: ". $x. " OVERLAPS WITH ". "DATE: ". $row["month_start"]);
						//var_dump($numrow);
					}
				}
			  
			}
		  }
		if($this->capture_err())
			return $this->capture_err();
		if (empty($fromdata_id)){
			$resp['status'] = 'empty-failure';
			$resp['msg'] = 'No Employee Selected <br> Please check again.';
			return json_encode($resp);
			exit;
		}
		if(count($numrow) > 0){
			$resp['status'] = 'overlap';
			$resp['msg'] = 'Previous Entry Overlap: '.'<br>'.implode($numrow);
			return json_encode($resp);
			exit;
		}
		if(!empty($data)) $data .=",";
			$data .= " `user_id`='{$this->settings->userdata('id')}' ";
		if(empty($id)){
			$sql = "INSERT INTO `employee_payroll` set $data";
		}else{
			$sql = "UPDATE `employee_payroll` set $data WHERE id ='{$id}'";
		}
		$save = $this->conn->query($sql);
		if($save){
			$update_pay =$this->update_pay($_POST['fromdata_id']);
			
			if($update_pay == 1){
				$resp['status'] ='success';
				$this->settings->set_flashdata('success'," Payroll successfully saved.");
			}else{
				$resp['status'] = 'failed';
				$resp['error'] = $update_pay;
			}
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn;
		}
		return json_encode($resp);
	}
	function save_employee_expenses(){
		extract($_POST);
		$_POST['amount'] = str_replace(',','',$_POST['amount']);
		$_POST['remarks'] = addslashes(htmlentities($_POST['remarks']));
		$data = "";
		foreach($_POST as $k =>$v){
			if($k == 'id')
				continue;
			if(!empty($data)) $data .=",";
			$data .= " `{$k}`='{$v}' ";
		}
		if(!empty($data)) $data .=",";
			$data .= " `user_id`='{$this->settings->userdata('id')}' ";
		if(empty($id)){
			$sql = "INSERT INTO `employee_balance` set $data";
		}else{
			$sql = "UPDATE `employee_balance` set $data WHERE id ='{$id}'";
		}
		$save = $this->conn->query($sql);
		if($save){
			$update_balance =$this->update_balance($_POST['person_id']);
			
			if($update_balance == 1){
				$resp['status'] ='success';
				$this->settings->set_flashdata('success'," Employee Data successfully saved.");
			}else{
				$resp['status'] = 'failed';
				$resp['error'] = $update_balance;
			}
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn;
		}
		return json_encode($resp);
	}
	function delete_expense(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `running_balance` where id = '{$id}'");
		if($del){
			$update_balance =$this->update_balance($category_id);
			if($update_balance == 1){
				$resp['status'] ='success';
				$this->settings->set_flashdata('success',"Expense successfully deleted.");
			}else{
				$resp['status'] = 'failed';
				$resp['error'] = $update_balance;
			}
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
	function delete_payroll_expenses(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `employee_payroll` where id = '{$id}'");
		if($del){
			$update_balance =$this->update_balance($fromdata_id);
			if($update_balance == 1){
				$resp['status'] ='success';
				$this->settings->set_flashdata('success',"Payroll successfully deleted.");
			}else{
				$resp['status'] = 'failed';
				$resp['error'] = $update_balance;
			}
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
}
$Master = new Master();
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
$sysset = new SystemSettings();
switch ($action) {
	case 'save_category':
		echo $Master->save_category();
	break;
	case 'delete_category':
		echo $Master->delete_category();
	break;
	case 'delete_employee':
		echo $Master->delete_employee();
	break;
	case 'save_budget':
		echo $Master->save_budget();
	break;
	case 'save_employee':
		echo $Master->save_employee();
	break;
	case 'save_employee_expenses':
		echo $Master->save_employee_expenses();
	break;
	case 'delete_budget':
		echo $Master->delete_budget();
	break;
	case 'delete_payroll_expenses':
		echo $Master->delete_payroll_expenses();
	break;
	case 'save_expense':
		echo $Master->save_expense();
	break;
	case 'save_payroll_expenses':
		echo $Master->save_payroll_expenses();
	break;
	case 'delete_expense':
		echo $Master->delete_expense();
	break;
	default:
		// echo $sysset->index();
		break;
}