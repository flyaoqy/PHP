<?php

class Login extends CI_Controller {
	function __construct() {
		parent::__construct();
	}
	/**
	 * 
	 * 登录页
	 */
	public function index(){
		
		
		$this->load->view('top/bootstrap_top');
		$this->load->view( 'login');
		$this->load->view('top/foot');
	}
	/**
	 * 
	 * 切换用户
	 */
	public function switch_user(){
		$ssn = request('ssn');//员工号
		$sql = "select u_usercode,u_name from v_wf_user where u_usercode = ".sqlFilter($ssn,3);
		$query = $this->db->query($sql);
		if($query->num_rows()>0){
			$row = $query->row_array();
			$_SESSION['ssn'] = $row['u_usercode'];
			$_SESSION['name'] = $row['u_name'];
			redirect(site_url('purchase/list_purchase/loadview'));
			
		}else{
			redirect(site_url('login'));
		}
	
	}
}
