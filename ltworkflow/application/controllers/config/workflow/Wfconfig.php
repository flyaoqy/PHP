<?php
class Wfconfig extends CI_Controller {
	function __construct() {
		parent::__construct();
	}
	/**
	 * 
	 * 新建窗口
	 */
	function createview(){
		$this->load->view ( 'config/workflow/detail', $data);
	}
	/**
	 * 
	 * 新建动作
	 */
	function create(){
		$response = array();
		$wf_name = request("wf_name");
		if(isNull($wf_name)){
			$response['status'] = "error";
			$response['msg'] = "请填写流程名称！";
			echo json_encode($response);
			return;
		}
		$newid = uuid();
		$sql = "INSERT INTO t_wf_workflow
        				( wf_uid,wf_name,wf_version,wf_createtime)
        		values ('$newid',".sqlFilter($wf_name,3).",'1.0',now())";
		$this->db->query($sql);
		
		$response['status'] = "success";
		echo json_encode($response);
		
	}
	/**
	 * 
	 * 打开
	 */
	function detail() {
		$data = array();
		$wfuid = request ( "wfuid" );
		$sql = "select  wf_uid, wf_name,  wf_filename 
		from t_wf_workflow where wf_uid='" . $wfuid . "'";
		$query = $this->db->query($sql);
		$data['row'] = $query->row_array();
		$this->load->view ( 'config/workflow/detail', $data);
	}
	/**
	 * 
	 * 历史打开
	 */
	function history_detail() {
		$data = array();
		$id = request ( "id" );
		$sql = "select wf_uid, wf_name,  wf_filename 
		from t_wf_workflow_xmllog where id=" . sqlFilter($id,2);
		$query = $this->db->query($sql);
		$data['row'] = $query->row_array();
		$this->load->view ( 'config/workflow/detail', $data);
	}
	/**
	 * 
	 * 历史列表
	 */
	function history_list() {
		$wfuid = request ( "wfuid" );
		$sql = "SELECT  id,wf_name,wf_version,logtime FROM t_wf_workflow_xmllog 
				WHERE wf_uid = ".sqlFilter($wfuid,1)."
				ORDER BY logtime DESC " ;
		$query = $this->db->query($sql);
		$result = $this->simple_model->returnqueryArray($query);
		echo json_encode($result);
	}
	
	/**
	 * 
	 * 编辑
	 */
	function edit() {
		$wfuid = request ( "wfuid" );
		$sql = "select wf_uid, wf_name, wf_filename 
		from t_wf_workflow where wf_uid='" . $wfuid . "'";
		$query = $this->db->query($sql);
		$data['row'] = $query->row_array();
		$this->load->view ( 'config/workflow/edit', $data);
	}
	/**
	 * 
	 * 保存
	 */
	function save() {
		$data = array();
		$wfuid = request ( "wfuid" );
		$filename = request ( "filename" ); 
		$this->db->trans_start();
		//更新流程配置
		$sql = "update t_wf_workflow set wf_filename = ".sqlFilter($filename,3)." 
			where wf_uid=" . sqlFilter($wfuid,1);
		$this->db->query($sql);
		
		//查询最近的历史版本
		$sql = "SELECT wf_filename,wf_version 
				FROM t_wf_workflow_xmllog 
				WHERE wf_uid =".sqlFilter($wfuid,1)." 
				ORDER BY logtime DESC
				limit 1  ";
		$query = $this->db->query($sql);
		if($query->num_rows() > 0){//
			$row = $query->row_array();
			$preFilename = trim($row['wf_filename']);
			$preVersion = $row['wf_version'];
			if($filename != $preFilename){//如果XML内容不相同，添加历史版本
				$newVersion = floor($preVersion + 1) ;//大版本号加1
				$sql = "INSERT INTO t_wf_workflow_xmllog
        				( wf_uid ,wf_name ,wf_filename ,wf_layout ,wf_version ,logtime)
						SELECT wf_uid ,wf_name ,wf_filename ,wf_layout,$newVersion,now() 
						FROM t_wf_workflow WHERE wf_uid = " . sqlFilter($wfuid,1);
				$this->db->query($sql);
			}
		}else{
			//第一版
			$sql = "INSERT INTO t_wf_workflow_xmllog
        				( wf_uid ,wf_name ,wf_filename ,wf_layout ,wf_version ,logtime)
						SELECT wf_uid ,wf_name ,wf_filename ,wf_layout,1,now() 
						FROM t_wf_workflow WHERE wf_uid = " . sqlFilter($wfuid,1);
				$this->db->query($sql);
		}
		
		$this->db->trans_complete();
		
		$this->detail();
	}
	/**
	 * 
	 * 打开列表
	 */
	function openworkflow(){
		$sql = 'select wf_uid, wf_name from t_wf_workflow order by wf_name ';
		$query = $this->db->query($sql);
		$data['result'] = $this->simple_model->returnqueryArray($query);
		$this->load->view('top/bootstrap_top');
		$this->load->view ( 'config/workflow/openworkflow', $data);
		$this->load->view('top/foot');
	}
	
}