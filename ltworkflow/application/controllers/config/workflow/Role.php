<?php 
/**
 * 模板套用_crm无流程单据模板
 * 表名t_wf_steps_condition
 * 模块名role
 */
class Role extends CI_Controller {


	function __construct(){
		parent::__construct();
	}

	// 打开单据
	public function open(){
		$data = array();
		$data['base'] = array();
		$id = request('id');
		$wf_uid = request('wf_uid');
		if($id == ''){
			$data['base']['id'] = $id;
			$data['base']['wf_uid'] = $wf_uid;
			$data['base']['group_uid'] = uuid();
		}else{
			$data['base'] = $this->base($id);
		}
		$this->load->view('top/bootstrap_top');
		$this->load->view('config/workflow/role',$data);
		$this->load->view('top/foot');
	}
	
	// 读取单据基本信息
	private function base($id){
		$sql = "
			select 
				id,wf_uid,expression,group_uid 
			from 
				t_wf_steps_condition 
			where id = ".sqlFilter($id,3);
		$query = $this->db->query($sql);
		$result = $this->simple_model->returnqueryArray($query);
		
		return $result[0];
	}
	
	// 表单验证函数
	public function validate(){
	
		// 加载验证文件
		require $_SERVER['DOCUMENT_ROOT'].'/wf_engine/manage/application/controllers/config/workflow/Role_validate.php';
		
		// 初始化验证文件类
		$validate_model = new role_validate();
		
		// 获取验证配置信息
		$validators = $validate_model->get_validate();
		
		// 返回给js-ajax请求
		if(request('from') == 'js'){
			echo json_encode($validators['common']);
			exit();
		}
		
		// 表单验证
		$result = validate_helper::validate($validators,$this);
		
		// 验证不通过，返回验证失败信息
		if(!$result['valid']){
			$response = array(
				'status'	=> 'error',
				'type'		=> 'validate',
				'msg'		=> nl2br($result['msg'])
			);
			echo json_encode($response);
			exit();
		}
	}
	
	// 保存单据
	public function save(){
	
		// 表单验证
		$this->validate();
		$id = request("id");//是否全局角色
		$sql_array = array();
		$request_array = array(
			'wf_uid','type','expression','group_uid'
		);
		foreach($request_array as $value){
			$$value = request($value);
		}
		$isGlobal = request("isGlobal");//是否全局角色
		if($isGlobal == "1"){
			$wf_uid = "";
		}
		foreach($request_array as $each_request){
			$sql_array[$each_request] = $$each_request;
		}
		
		$this->db->trans_start();
		if($id != ""){
			//拼接更新语句
			$sql_array['id'] = $id;
			$update_sql = $this->simple_model->simple_update_string ( $sql_array, '', 't_wf_steps_condition', 'id' );
			$this->db->query (  $update_sql  );
			
		}else{//新增
			$insert_sql = $this->simple_model->simple_insert_string ( $sql_array, '', 't_wf_steps_condition' );
			$this->db->query ( $insert_sql  );
		}

		//人员删除
		$sql = "delete from t_wf_steps_user WHERE group_uid = ".sqlFilter($group_uid,3);
		$this->db->query ( $sql  );
		//人员添加
		$user_list = request("user_list");
		$snList = explode(",", $user_list);
		foreach ($snList as $sn){
			//查出用户名
			$sql = "SELECT u_name FROM v_wf_user WHERE u_usercode = ".sqlFilter($sn,3);
			$query = $this->db->query($sql);
			$name = "";
			if($query->num_rows()>0){
				$row = $query->first_row();
				$name = $row->u_name;
			}
			$sql = "INSERT INTO t_wf_steps_user
		        ( group_uid, usercode, username )
				VALUES  ( ".sqlFilter($group_uid,3).", -- group_uid - varchar(50)
				          ".sqlFilter($sn,3).", -- usercode - varchar(50)
				          '$name'  -- username - varchar(50)
				          )";
			$this->db->query($sql);
		}
		
		
		
		$this->db->trans_complete();

		// 返回信息
		$response = array();
		$response['status'] = 'success';

		echo json_encode($response);
	}
	/**
	 * 
	 * 审批人列表
	 */
	function role_query(){
		$group_uid = request('group_uid');
		$sql = "SELECT usercode,CONCAT(username,'(',usercode,')') as usershow  
				FROM t_wf_steps_user WHERE group_uid = ".sqlFilter($group_uid,3);
		$role_query = $this->db->query($sql);
		$role = array();
		$snList = array();
		$showList = array();
		foreach ($role_query->result_array() as $role){
			array_push($snList, $role['usercode']);
			array_push($showList, $role['usershow']);
		}
		$role['snList']  =  implode(",",$snList);
		$role['showList']  = implode(",",$showList);

		echo json_encode($role);
	}
}