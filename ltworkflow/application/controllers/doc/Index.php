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
		$this->load->view('top/bootstrap_top');
		$this->load->view('doc/list_doc');
		$this->load->view('top/foot');
	}
	public function test(){
		echo "##########";
	}
}
