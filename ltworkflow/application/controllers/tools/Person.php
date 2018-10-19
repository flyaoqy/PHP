<?php
/**
 * 
 * 查询人员相关信息
 * @author liutao
 *
 */
class Person extends CI_Controller {

	public function __construct(){
        parent::__construct();
    }
	/**
	 * 获取人员列表
	 *
	 */

	function get_all_person(){
		$sql ="
			select u_usercode salarysn,u_name name,u_email email from v_wf_user order by u_name 
		";
		$query = $this->db->query($sql);
		$result = $this->simple_model->returnqueryArray($query);
		echo json_encode($result);
	}
	/**
	 * 获取人员列表
	 *
	 */
	function getPerson() {
		$field = request ( "q" );
		//如果查询条件为空直接返回
		if (isNull ( $field )) {
			return;
		}
		$mailFlag = true; // 是否为邮箱
		$numFlag = true; // 是否为电话
		$chineseFlag = true; // 是否为汉字
		$charFlag = true; // 是否为字母（默认）
		
		
		$select = "select distinct ";
		$select .= " u_usercode salarysn,u_name name,u_email email ";
		$from = " from v_wf_user ";
		$where = " where 1 = 1  ";
		$orderby = " order by name ";
		
		if (preg_match ( '/^[a-zA-Z]+$/', $field )) { //  按字母查询
			$where .= " and u_email like '" . sqlFilter($field) . "%' ";
		} else if (preg_match ( '/^[0-9a-zA-Z]+$/', $field )) { // 按电话或工号查询
			$where .= " and (u_usercode like '" . sqlFilter($field) . "%' or u_telephone like '" . sqlFilter($field) . "%' )";
		} else { // 按汉字查询
			$where .= " and u_name like '" . sqlFilter($field) . "%' ";
		}
		$sql = $select . $from . $where . $orderby;
		$result = $this->db->query ( $sql );
		$result_list = $this->simple_model->returnqueryArray($result);
		echo json_encode ( $result_list );

	}
	/**
	 * 
	 * 检查初始化时的人员信息并返回以姓名(工号)为显示、工号为值的json串
	 */
	function checkPerson(){
		$names = request('names');
		$arr = explode(',',$names);
		$string = "";
		foreach($arr as $name){
			if($name != ""){
				$string .= ",".$name;
			}
		}
		if($string != ""){
			$string = substr($string,1,strlen($string)-1);
			$string = str_replace(',',"','",$string);
			$string = "'".$string."'";
			$sql = "select CONCAT(u_name,'(',u_usercode,')') as usershow,u_usercode as value from v_wf_user 
			where  u_usercode in (".$string.")";
			$query = $this->db->query($sql);
			$result = $this->simple_model->returnqueryArray($query);
		}else{
			$result = array();
		}
		
		echo json_encode($result);
	}
}