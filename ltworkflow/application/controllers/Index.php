<?php

class Index extends CI_Controller {
	function __construct() {
		parent::__construct();
	}
	/**
	 * 
	 * 主页
	 */
	public function index(){
		
		$sub_title = request("sub_title");//导航栏标签
		if($sub_title == ""){//默认首页
			$sub_title = "doc";
		}
		$data = array();
		$data['sub_title'] = $sub_title;
		$this->load->view( 'top',$data);
		if($sub_title == "doc"){
			$this->load->view( 'left_doc');
			$this->load->view( 'index_frame');
		}else if($sub_title == "example"){
			$this->load->view( 'left_example');
			$this->load->view( 'index_frame');
		}else if($sub_title == "manage"){
			$this->load->view( 'left_manage');
			$this->load->view( 'index_frame');
		}
		$this->load->view( 'foot');
	}
}
