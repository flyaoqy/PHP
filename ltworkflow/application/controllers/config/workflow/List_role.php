<?php

class List_role extends CI_Controller {
	
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
		$this->load->view ( 'config/workflow/list_role', $data );
		$foot_data = array ();
		$this->load->view ( 'top/foot', $foot_data );
	}
	/**
	 * 
	 * 查询条件
	 */
	public function query() {

		$where = " where type = 'custom_role' ".$this->getWhereSql();

		$sql = "
			select 
				id as dt_primary_id,id,wf_name,expression,
				'' AS userlist
			from 
				t_wf_steps_condition a LEFT OUTER JOIN t_wf_workflow  b ON a.wf_uid = b.wf_uid
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
			'expression','wf_uid'
		);
		
		foreach($request_array as $key => $value){
			$$value = request($value);
		}
		
		
		if($expression != ''){
			$where .= "and expression like '".sqlFilter($expression)."%' ";
		}
		
		$where .= "and (a.wf_uid ='' or a.wf_uid = '".sqlFilter($wf_uid)."') ";
		
			

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
		//删除子表
		$sql = " delete from t_wf_steps_user where group_uid in (
			select group_uid from t_wf_steps_condition where id in ($id_clause)
			)
		";
		$this->db->query($sql);
		//删除主表
		$sql = "
			delete from t_wf_steps_condition where id in ($id_clause)
		";
		$this->db->query($sql);
		$response = array();
		$response['status'] = 'success';
		echo json_encode($response);
	}
	
}