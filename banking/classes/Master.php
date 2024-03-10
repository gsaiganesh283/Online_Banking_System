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
			if(isset($sql))
			$resp['sql'] = $sql;
			return json_encode($resp);
			exit;
		}
	}
	
	function save_account(){
		extract($_POST);
		$data ="";
		foreach($_POST as $k => $v){
			if(!in_array($k,array('id','password'))){
				if(!empty($data)) $data.= ", ";
				$data.= " {$k} = '{$v}' ";
			}
		}
		if(isset($password))
			$data.= " `password` = md5('{$password}') ";
		if(empty($id)){
			$sql = "INSERT INTO `accounts` set {$data}";
		}else{
			$sql = "UPDATE `accounts` set {$data} where id = {$id}";
		}
		$save =  $this->conn->query($sql);
		$this->capture_err();
		if($save){
			if(empty($id)){
				$id = $this->conn->insert_id;
				$this->conn->query("INSERT INTO `transactions` set account_id ={$id},remarks='Beginning balance',`type` = 1, `amount` = '{$balance}' ");
				$this->capture_err();
			}
			$resp['status']='success';
			$this->settings->set_flashdata('success',' Account successfully saved.');
		}
		return json_encode($resp);
	}
	function check_account(){
		extract($_POST);
		$chk = $this->conn->query("SELECT * FROM `accounts` where account_number = '{$account_number}' ".($id > 0 ? " and id != '{$id}' " : ''));
		$this->capture_err();
		if($chk->num_rows > 0){
			$resp['status'] = 'taken';
		}else{
			$resp['status'] = 'available';
		}
		return json_encode($resp);
	}
	function get_account(){
		extract($_POST);
		$qry = $this->conn->query("SELECT id,balance,concat(lastname,', ',firstname,' ',middlename) as name FROM `accounts` where account_number = '{$account_number}' ");
		$this->capture_err();
		if($qry->num_rows > 0){
			$resp['status'] = 'success';
			$resp['data'] = $qry->fetch_assoc();
		}else{
			$resp['status'] = 'not_exist';
		}
		return json_encode($resp);
	}
	function deposit(){
		extract($_POST);
		$current = floatval(str_replace(',','',$current));
		$new_balance = floatval($current) + floatval($balance);
		$update = $this->conn->query("UPDATE `accounts` set `balance` = '{$new_balance}' where id = {$account_id} ");
		$this->capture_err();
		if($update){
			$this->conn->query("INSERT INTO `transactions` set account_id ={$account_id},remarks='Deposits',`type` = 1, `amount` = '{$balance}' ");
			$this->capture_err();
			$resp['status']='success';
			if($this->settings->userdata('login_type') == 1){
				$this->settings->set_flashdata('success', $name.'\'s deposit successfully.');
			}else{
				$this->settings->set_flashdata('success', 'Deposit successfully saved.');
				$this->settings->set_userdata('balance', $new_balance);
			}
		}else{
			$resp['status'] = 'failed';
		}
		return json_encode($resp);
	}
	function withdraw(){
		extract($_POST);
		$current = floatval(str_replace(',','',$current));
		$new_balance = floatval($current) - floatval($balance);
		$update = $this->conn->query("UPDATE `accounts` set `balance` = '{$new_balance}' where id = {$account_id} ");
		$this->capture_err();
		if($update){
			$this->conn->query("INSERT INTO `transactions` set account_id ={$account_id},remarks='Withdraw',`type` = 1, `amount` = '{$balance}' ");
			$this->capture_err();
			$resp['status']='success';
			if($this->settings->userdata('login_type') == 1){
			$this->settings->set_flashdata('success', $name.'\'s withdraw form successfully saved.');
			}else{
			$this->settings->set_flashdata('success', 'Withdraw form successfully saved.');
			$this->settings->set_userdata('balance', $new_balance);
			}
		}else{
			$resp['status'] = 'failed';
		}
		return json_encode($resp);
	}
	
	function transfer(){
		extract($_POST);
		$current = floatval(str_replace(',','',$current));
		$new_balance = floatval($current) - floatval($balance);
		$update = $this->conn->query("UPDATE `accounts` set `balance` = '{$new_balance}' where id = {$account_id} ");
		$this->capture_err();
		$update2 = $this->conn->query("UPDATE `accounts` set `balance` = `balance`+'{$balance}' where id = {$transfer_id} ");
		$this->capture_err();
		if($update && $update2){
			$this->conn->query("INSERT INTO `transactions` set account_id ={$account_id},remarks='Transferred to {$transfer_number}',`type` = 3, `amount` = '{$balance}' ");
			$this->capture_err();
			$this->conn->query("INSERT INTO `transactions` set account_id ={$transfer_id},remarks='Transferred from {$account_number}',`type` = 3, `amount` = '{$balance}' ");
			$this->capture_err();
			$resp['status']='success';
			$this->settings->set_flashdata('success', 'Transfer successfully processed.');
			if($this->settings->userdata('login_type') == 1)
				$this->settings->set_userdata('balance', $new_balance);
		}else{
			$resp['status'] = 'failed';
		}
		return json_encode($resp);
	}

	function save_announcement(){
		extract($_POST);
		$data = " title = '{$title}' ";
		$data .= ", announcement = '".(addslashes(htmlentities($announcement)))."' ";
		if(empty($id)){
			$sql = "INSERT INTO `announcements` set {$data} ";
		}else{
			$sql = "UPDATE `announcements` set {$data} where id = {$id} ";
		}
		$save = $this->conn->query($sql);
		$this->capture_err();
		if($save){
			$resp['status'] ='success';
			$this->settings->set_flashdata('success', 'Announcement successfully saved.');
		}else{
			$resp['status'] = 'failed';
			$resp['sql'] = $sql;
		}
		return json_encode($resp);
	}
	function delete_account(){
		extract($_POST);
		$delete = $this->conn->query("DELETE FROM `accounts` where id = {$id}");
		$this->capture_err();
		$delete1 = $this->conn->query("DELETE FROM `transactions` where account_id = {$id}");
		$this->capture_err();
		if($delete && $delete1){
			$resp['status'] ='success';
			$this->settings->set_flashdata('success', 'Account successfully deleted.');
		}else{
			$resp['status'] = 'failed';
		}
		return json_encode($resp);
	}
	function delete_announcement(){
		extract($_POST);
		$delete = $this->conn->query("DELETE FROM `announcements` where id = {$id}");
		$this->capture_err();
		if($delete){
			$resp['status'] ='success';
			$this->settings->set_flashdata('success', 'Announcement successfully deleted.');
		}else{
			$resp['status'] = 'failed';
		}
		return json_encode($resp);
	}
}

$Master = new Master();
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
$sysset = new SystemSettings();
switch ($action) {
	case 'save_account':
		echo $Master->save_account();
	break;
	case 'get_account':
		echo $Master->get_account();
	break;
	case 'check_account':
		echo $Master->check_account();
	break;
	case 'deposit':
		echo $Master->deposit();
	break;
	case 'withdraw':
		echo $Master->withdraw();
	break;
	case 'transfer':
		echo $Master->transfer();
	break;
	case 'save_announcement':
		echo $Master->save_announcement();
	break;
	case 'delete_account':
		echo $Master->delete_account();
	break;
	case 'delete_announcement':
		echo $Master->delete_announcement();
	break;
	default:
		// echo $sysset->index();
		break;
}