<?php
class Wfapprove extends CI_Controller {
	private $wf_xml;
	function __construct() {
		parent::__construct();
		$this->wf_xml = new DOMDocument ( );
	}
	/**
	 * 
	 * 获取审批条件
	 */
	function condition_get(){
		$wfuid = request ( "wfuid" );
		$from = request ( "from" );
		$to = request ( "to" );
		$data['expression'] = "";
		$data['from_name'] = $from;
		$data['to_name'] = $to;
		//审批条件
		$sql = "SELECT expression FROM t_wf_steps_condition 
			WHERE wf_uid= ".sqlFilter($wfuid,3)." AND step_id = ".sqlFilter($from,3)." AND next_step_id = ".sqlFilter($to,3);
		$query = $this->db->query($sql);
		if($query->num_rows() > 0){//修改
			$data['expression'] = $query->row()->expression;
			
		}
		//审批节点名
		$sql = "select  wf_uid, wf_name, wf_filename 
		from t_wf_workflow where wf_uid=".sqlFilter($wfuid,1);
		$query = $this->db->query($sql);
		if($query->num_rows() > 0){
			$row = $query->row_array();
			$data['base']  = $row;
			$dxml = $row['wf_filename'];
			if(!isNull($dxml)){
				$this->wf_xml->loadXML ($dxml);
				$data['from_name'] = $this->getElementById($from)->getAttribute ( "name" );
				$data['to_name'] = $this->getElementById($to)->getAttribute ( "name" );
			}
			
			
		}
		echo json_encode($data);
	}
	/**
	 * 
	 * 保存审批条件
	 */
	function condition_save(){
		$wfuid = request ( "wfuid" );
		$from = request ( "from" );
		$to = request ( "to" );
		$expression = request ( "expression" );
		$expression = $this->unescape($expression);
		if (!$this->expression_check($expression)) { 
			$response['status'] = "error";
			$response['msg'] = "表达式必须为 (#频道#==@频道@ && #金额#>50000) || #地域#!='北京'  的形式";
			echo json_encode($response);
			return;
		}
		$sql = "SELECT 1 FROM t_wf_steps_condition 
			WHERE wf_uid= ".sqlFilter($wfuid,3)." AND step_id = ".sqlFilter($from,3)." AND next_step_id = ".sqlFilter($to,3);
		$query = $this->db->query($sql);
		if($query->num_rows() > 0){//修改
			$sql = "UPDATE t_wf_steps_condition SET expression = ".sqlFilter($expression,3)."
				WHERE wf_uid= ".sqlFilter($wfuid,3)." AND step_id = ".sqlFilter($from,3)." AND next_step_id = ".sqlFilter($to,3);
			$this->db->query($sql);
		}else{//新增
			$sql = "INSERT INTO t_wf_steps_condition 
				(wf_uid,type,step_id,next_step_id,expression,sort)
				 VALUES(".sqlFilter($wfuid,3).",
					'condition',
					".sqlFilter($from,3).",
					".sqlFilter($to,3).",
					".sqlFilter($expression,3).",
					0
					)";
			$this->db->query($sql);
		}
		$response['status'] = "success";
		echo json_encode($response);
	}
	/**
	 * 
	 * 审批人列表
	 */
	function role_list(){
		$wfuid = request('wfuid');
		$stepid = request('stepid');
		$data = array ();
		$data['wfuid'] = $wfuid;
		$data['stepid'] = $stepid;
		$this->load->view ( 'top/bootstrap_top');
		$this->load->view ( 'config/workflow/wfrole', $data );
		$this->load->view ( 'top/foot');
	}
	/**
	 * 
	 * 审批人列表
	 */
	function role_query(){
		$wfuid = request('wfuid');
		$stepid = request('stepid');
		//查询所有判断条件下的人员分组
		$sql = "SELECT id,expression,group_uid,sort FROM t_wf_steps_condition
				WHERE  type='role' AND  wf_uid = ".sqlFilter($wfuid,3)." AND step_id = ".sqlFilter($stepid,2)."
				order by sort ";
		$query = $this->db->query($sql);
		$result = array();
		foreach ($query->result_array() as $condition){
			//查询每组人员列表
			$sql = "SELECT usercode,CONCAT(username,'(',usercode,')') as usershow  
					FROM t_wf_steps_user WHERE group_uid = '".$condition['group_uid']."'";
			$role_query = $this->db->query($sql);
			$snList = array();
			$showList = array();
			foreach ($role_query->result_array() as $role){
				array_push($snList, $role['usercode']);
				array_push($showList, $role['usershow']);
			}
			$condition['snList']  =  implode(",",$snList);
			$condition['showList']  = implode(",",$showList);
			array_push($result, $condition);
		}
		echo json_encode($result);
	}
	/**
	 * 
	 * 一条审批人、审批条件信息
	 */
	function role_info(){
		$id = request('id');
		//查询所有判断条件下的人员分组
		$sql = "SELECT id,expression,group_uid,sort FROM t_wf_steps_condition
				WHERE  id = ".sqlFilter($id,2);
		$query = $this->db->query($sql);
		$condition = array();
		if($query->num_rows()>0){
			$condition = $query->row_array();
			//查询每组人员列表
			$sql = "SELECT usercode,CONCAT(username,'(',usercode,')') as usershow  
					FROM t_wf_steps_user WHERE group_uid = '".$condition['group_uid']."'";
			$role_query = $this->db->query($sql);
			$snList = array();
			$showList = array();
			foreach ($role_query->result_array() as $role){
				array_push($snList, $role['usercode']);
				array_push($showList, $role['usershow']);
			}
			$condition['snList']  =  implode(",",$snList);
			$condition['showList']  = implode(",",$showList);
		}
		echo json_encode($condition);
	}
	/**
	 * 
	 * 保存审批人、审批条件
	 */
	function role_del(){
		$id = request('id');
		$this->db->trans_start();
		//删除原有人员列表
		$sql = "DELETE FROM  t_wf_steps_user WHERE group_uid in
				(SELECT group_uid FROM t_wf_steps_condition WHERE  id = ".sqlFilter($id,2).")";
		$this->db->query($sql);
		//删除判断条件
		$sql = "DELETE FROM t_wf_steps_condition WHERE id = ".sqlFilter($id,2);
		$this->db->query($sql);
		
		$this->db->trans_complete ();
		// 返回信息
		$response = array();
		$response['status'] = 'success';

		echo json_encode($response);

		
	}
	/**
	 * 
	 * 删除审批人、审批条件
	 */
	function role_save(){
		$id = request('id');
		$group_uid = request('group_uid');
		$wfuid = request('wfuid');
		$stepid = request('stepid');
		$sort = request('sort');
		$user_list_str = request('user_list');
		$user_list_show_str = request('user_list_show');
		$expression = request('expression');
		$expression = $this->unescape($expression);
		if (!$this->expression_check($expression)) { 
			$response['status'] = "error";
			$response['msg'] = "表达式必须为 (#频道#==@频道@ && #金额#>50000) || #地域#!='北京'  的形式";
			echo json_encode($response);
			return;
		}
		$this->db->trans_start ();
		if($id!=""){//修改
			$sql = "UPDATE t_wf_steps_condition SET expression = ".sqlFilter($expression,3).",sort=".sqlFilter($sort,2)."
					WHERE id = ".sqlFilter($id,2);
			$this->db->query($sql);
		}else{//新增
			$group_uid = uuid();
			$sql = "INSERT INTO t_wf_steps_condition (wf_uid,type,step_id,expression,group_uid,sort)
					VALUES (".sqlFilter($wfuid,3).",'role',".sqlFilter($stepid,3).",".sqlFilter($expression,3).",
					".sqlFilter($group_uid,3).",".sqlFilter($sort,2).")";
			$this->db->query($sql);
		}
		//删除原有人员列表
		$sql = "DELETE FROM t_wf_steps_user WHERE group_uid = ".sqlFilter($group_uid,3);
		$this->db->query($sql);
		//添加人员列表
		$user_list = explode(",", $user_list_str);
		$user_list_show = explode(",", $user_list_show_str);
		foreach ($user_list as $user){
			$sql = "select u_name name from v_wf_user where u_usercode = ".sqlFilter($user,3) ;
			$query = $this->db->query($sql);
			$row = $query->row_array();
			$username = $row['name'];
			$sql = "INSERT INTO t_wf_steps_user (group_uid,usercode,username) 
					VALUES (".sqlFilter($group_uid,3).",".sqlFilter($user,3).",".sqlFilter($username,3).")";
			$this->db->query($sql);
		}
		
		$this->db->trans_complete ();
		// 返回信息
		$response = array();
		$response['status'] = 'success';

		echo json_encode($response);

		
	}
	
	/**
	 * 
	 * 根据id取节点对象
	 * @param unknown_type $wf_id
	 */
	private function getElementById($wf_id) {
		foreach ( $this->wf_xml->getElementsByTagName ( 'initial-actions' ) as $node ) {
			//判断是否id
			if ($node->getAttribute ( "id" ) == $wf_id) {
				return $node;
			}
		}
		foreach ( $this->wf_xml->getElementsByTagName ( 'step' ) as $node ) {
			//判断是否id
			if ($node->getAttribute ( "id" ) == $wf_id) {
				return $node;
			}
		}
		foreach ( $this->wf_xml->getElementsByTagName ( 'split' ) as $node ) {
			//判断是否id
			if ($node->getAttribute ( "id" ) == $wf_id) {
				return $node;
			}
		}
		foreach ( $this->wf_xml->getElementsByTagName ( 'join' ) as $node ) {
			//判断是否id
			if ($node->getAttribute ( "id" ) == $wf_id) {
				return $node;
			}
		}
		foreach ( $this->wf_xml->getElementsByTagName ( 'node' ) as $node ) {
			//判断是否id
			if ($node->getAttribute ( "id" ) == $wf_id) {
				return $node;
			}
		}
		return null;
	}
	
	function test(){
		require_once 'ltworkflow/workflow.php';
		$userDB = new UserDB();
		$varList = array();
		//查询配置信息
		$sql = "SELECT expression_key,expression_value FROM t_wf_steps_condition_var 
				WHERE wf_uid = 1 
				order by expression_key ";
		$result = DBCommon::query ( $sql );
		while ( $row_var = DBCommon::fetch_array ( $result ) ) {
			$row_var['expression_key'] = wf_iconvutf($row_var['expression_key']);
			$row_var['expression_value'] = wf_iconvutf($row_var['expression_value']);
			array_push($varList, $row_var);	
		}
		//替换表达式中的配置变量
		__addGlobalVar("频道","新浪体育");
		__addGlobalVar("金额",10000);
		__addGlobalVar("地域","上海");
		
		$expression = "(#频道#==@频道@ && #金额#>50000) || (#地域#!=@地域@ && #频道#==@频道@) ";
		
		echo $expression."<br>";
		$expressions = $userDB->convertExpression($expression, $varList);
		$expressions = __replaceGlobalVar ( $expressions );
		echo "<br> last:".$expressions."<br>";
		if (eval ( "return " .  $expressions . ";" )) {
			echo "true";
		}else{
			echo "false";
		}
		
	}
	/**
	 * 
	 * 表达式校验
	 */
	private function expression_check($expression){
		//可填写 1 或 0 表示验证成功或失败
		if($expression == "1" || $expression == "0" ){
			return true;
		}
		$expression = str_replace("#", "'", $expression);//替换系统变量
		$expression = str_replace("@", "'", $expression);//替换用户变量
		$result = @eval (  $expression  . ";" );//执行表达式
		if ($result === false) {//解析错误返回false
			return false;
		}else{
			return true;
		}
	
	}
	/**
	 * 
	 * js escape 解码
	 * @param unknown_type $str
	 */
	private function unescape($str){
		$ret = '';
		$len = strlen($str);
		for ($i = 0; $i < $len; $i++){
			if ($str[$i] == '%' && $str[$i+1] == 'u'){
				$val = hexdec(substr($str, $i+2, 4));
				if ($val < 0x7f) $ret .= chr($val);
				else if($val < 0x800) $ret .= chr(0xc0|($val>>6)).chr(0x80|($val&0x3f));
				else $ret .= chr(0xe0|($val>>12)).chr(0x80|(($val>>6)&0x3f)).chr(0x80|($val&0x3f));
				$i += 5;
			}
			else if ($str[$i] == '%'){
				$ret .= urldecode(substr($str, $i, 3));
				$i += 2;
			}
			else $ret .= $str[$i];
		}
		return $ret;
	}
	
}