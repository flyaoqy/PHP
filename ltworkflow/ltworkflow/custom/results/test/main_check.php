<?php
namespace com\ltworkflow;
/*
 * 主流程节点检测
 */

class main_check extends WorkflowBase {

	function __construct(){

	}
	
	function validate() {
		//获取自定义变量和业务ID
		$etuid = $this->local_param ['etuid'];
		$type = $this->local_param ['type'];

		if ($type == "parallel") { 
			$sql = "select contract_type from t_purchase_contract where et_uid = '$etuid' ";
			$query = DBCommon::query ( $sql );
			if ($row = DBCommon::fetch_array ( $query )) {
				//如果是实物合同走并行测试流程
				if($row['contract_type']=='实物'){
					return true;
				}
			}
		}
		return FALSE;
	
	}
}