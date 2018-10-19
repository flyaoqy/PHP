<?php 
/**
 * 调用工作流引擎
 */
class Wf_interface extends CI_Controller {

	function __construct(){
		parent::__construct();
	}
	/**
	 * 
	 * 获取当前审批人
	 */
	public function get_approver(){
		$etuid = request("etuid");//实例id 逗号分隔
		$sql = "SELECT cs_nodename,u_name name
				FROM t_wf_currentstep INNER JOIN v_wf_user ON cs_salarysn = u_usercode
				WHERE cs_status<>'Underway' and et_uid =".sqlFilter($etuid,3);
		$query = $this->db->query($sql);
		$result = array();
		foreach ($query->result_array() as $row){
			if(isNull($row['cs_nodename'])){
				$row['cs_nodename'] = "";
			}
			array_push($result, $row);
		}
		echo json_encode($result);
	
	}
	
}