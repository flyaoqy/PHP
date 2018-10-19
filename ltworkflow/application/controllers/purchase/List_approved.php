<?php
/**
 * 
 * 已审批列表，查询审批历史
 * @author liutao
 * 2017-06-03
 */
class List_approved extends CI_Controller {
	
	function __construct() {
		parent::__construct();
		check_login();
	}
	/**
	 * 
	 * 列表
	 */
	function loadView() {
		$this->load->view ( 'top/bootstrap_top');
		$data = array ();
		$this->load->view ( 'purchase/list_approved', $data );
		$foot_data = array ();
		$this->load->view ( 'top/foot', $foot_data );
	}
	/**
	 * 
	 * 查询条件
	 */
	public function query() {

		$ssn = isset($_SESSION["ssn"])?$_SESSION["ssn"]:"";
		$where = $this->getWhereSql();

		$sql = "
			select distinct id,a.et_uid,userno,username,createtime,contract_name,contract_type,goods_type,contract_sum
			from t_purchase_contract a 
			inner join t_wf_log b on a.et_uid = b.et_uid
			where wflg_salarysn = '$ssn'
		".$where;
		
		$result = $this->simple_model->get_dt_pageresult ( $sql );
		//把数组以json格式输出
		echo json_encode ( $result );
	
	}
	/**
	 * 
	 * 拼接语句
	 */
	function getWhereSql() {
		$where = '' ;
		$request_array = array(
			'username','contract_name','contract_type','goods_type'
		);
		
		foreach($request_array as $key => $value){
			$$value = request($value);
		}
		
		
				if($username != ''){
					$where .= "and username like '".sqlFilter($username)."%' ";
				}
			
				if($contract_name != ''){
					$where .= "and contract_name like '".sqlFilter($contract_name)."%' ";
				}
			
				if($contract_type != ''){
					$where .= "and contract_type like '".sqlFilter($contract_type)."%' ";
				}
			
				if($goods_type != ''){
					$where .= "and goods_type like '".sqlFilter($goods_type)."%' ";
				}
			

		return $where ;
	}
}