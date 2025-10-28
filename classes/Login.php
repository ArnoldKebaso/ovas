<?php
require_once '../config.php';
class Login extends DBConnection {
	private $settings;
	public function __construct(){
		global $_settings;
		$this->settings = $_settings;

		parent::__construct();
		ini_set('display_error', 1);
	}
	public function __destruct(){
		parent::__destruct();
	}
	public function index(){
		echo "<h1>Access Denied</h1> <a href='".base_url."'>Go Back.</a>";
	}
	
	public function login(){
		extract($_POST);
		
		// Check if email and password are provided
		if(empty($username) || empty($password)){
			return json_encode(array('status'=>'failed', 'msg'=>'Email and password are required'));
		}
		
		// Query using email and verify password_hash
		$stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ? AND is_admin = 1");
		$stmt->bind_param('s', $username);
		$stmt->execute();
		$qry = $stmt->get_result();
		
		if($qry->num_rows > 0){
			$res = $qry->fetch_array();
			
			// Verify password hash
			if(password_verify($password, $res['password_hash'])){
				// Check if user is active
				if($res['status'] != 1){
					return json_encode(array('status'=>'failed', 'msg'=>'Account is disabled'));
				}
				
				// Set session data
				foreach($res as $k => $v){
					if(!is_numeric($k) && $k != 'password_hash'){
						$this->settings->set_userdata($k, $v);
					}
				}
				$this->settings->set_userdata('login_type', 1);
				$this->settings->set_userdata('logged_in', true);
				$this->settings->set_userdata('is_admin', $res['is_admin']);
				
				// Also set direct session variables for auth_check
				$_SESSION['logged_in'] = true;
				$_SESSION['is_admin'] = $res['is_admin'];
				$_SESSION['status'] = $res['status'];
				$_SESSION['id'] = $res['id'];
				$_SESSION['name'] = $res['name'];
				$_SESSION['email'] = $res['email'];
				$_SESSION['phone'] = $res['phone'];
				
				return json_encode(array('status'=>'success'));
			} else {
				return json_encode(array('status'=>'failed', 'msg'=>'Invalid email or password'));
			}
		} else {
			return json_encode(array('status'=>'failed', 'msg'=>'Invalid email or password'));
		}
	}
	
	public function logout(){
		session_start();
		session_destroy();
		return json_encode(array('status'=>'success'));
	}
	
	function client_login(){
		extract($_POST);
		
		if(empty($email) || empty($password)){
			return json_encode(array('status'=>'failed', 'msg'=>'Email and password are required'));
		}
		
		// Query regular users (non-admin)
		$stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ? AND is_admin = 0");
		$stmt->bind_param('s', $email);
		$stmt->execute();
		$qry = $stmt->get_result();
		
		if($this->conn->error){
			$resp['status'] = 'failed';
			$resp['msg'] = "An error occurred while fetching data. Error:". $this->conn->error;
		} else {
			if($qry->num_rows > 0){
				$res = $qry->fetch_array();
				
				// Verify password hash
				if(password_verify($password, $res['password_hash'])){
					if($res['status'] == 1){
						foreach($res as $k => $v){
							if(!is_numeric($k) && $k != 'password_hash'){
								$this->settings->set_userdata($k, $v);
							}
						}
						$this->settings->set_userdata('login_type', 2);
						$this->settings->set_userdata('logged_in', true);
						$resp['status'] = 'success';
					} else {
						$resp['status'] = 'failed';
						$resp['msg'] = "Your Account is not verified yet.";
					}
				} else {
					$resp['status'] = 'failed';
					$resp['msg'] = "Invalid email or password.";
				}
			} else {
				$resp['status'] = 'failed';
				$resp['msg'] = "Invalid email or password.";
			}
		}
		return json_encode($resp);
	}
	
	public function client_logout(){
		session_start();
		session_destroy();
		return json_encode(array('status'=>'success'));
	}
}
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
$auth = new Login();
switch ($action) {
	case 'login':
		echo $auth->login();
		break;
	case 'logout':
		echo $auth->logout();
		break;
	case 'client_login':
		echo $auth->client_login();
		break;
	case 'client_logout':
		echo $auth->client_logout();
		break;
	default:
		echo $auth->index();
		break;
}

