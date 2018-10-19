<?php

class List_variate extends CI_Controller {
	
	function __construct() {
		parent::__construct();
	}
	/**
	 * 
	 * 列表
	 */
	function loadView() {
		$wf_uid = request('wf_uid');
		$this->load->view ( 'top/bootstrap_top');
		$data = array ();
		$data['wf_uid'] = $wf_uid;
		$this->load->view ( 'config/workflow/list_variate', $data );
		$foot_data = array ();
		$this->load->view ( 'top/foot', $foot_data );
	}
	/**
	 * 
	 * 查询条件
	 */
	public function query() {

		$where = " where 1 = 1 ".$this->getWhereSql();

		$sql = "
			select 
				id as dt_primary_id,id,wf_uid,expression_key,expression_value
			from 
				t_wf_steps_condition_var
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
			'wf_uid','expression_key','expression_value'
		);
		
		foreach($request_array as $key => $value){
			$$value = request($value);
		}
				if($wf_uid != ''){
					$where .= "and wf_uid = '".sqlFilter($wf_uid)."' ";
				}
				if($expression_key != ''){
					$where .= "and expression_key like '%".sqlFilter($expression_key)."%' ";
				}
			
				if($expression_value != ''){
					$where .= "and expression_value like '%".sqlFilter($expression_value)."%' ";
				}
		

		return $where ;
	}
	/**
	 * 
	 * 删除
	 */
	public function delete(){
		$ids = request('ids');
		$id_arr = explode(',',$ids);
		$id_clause = '';
		foreach($id_arr as $id){
			$id_clause .= ",".sqlFilter($id,3);
		}
		$id_clause = substr($id_clause,1);
		$sql = "
			delete from t_wf_steps_condition_var where id in ($id_clause)
		";
		$this->db->query($sql);
		$response = array();
		$response['status'] = 'success';
		echo json_encode($response);
	}
	
}