<?php 
/**
 * 模板套用_crm无流程单据模板
 * 表名t_wf_steps_condition_var
 * 模块名variate
 */
class variate extends CI_Controller {


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
		}else{
			$data['base'] = $this->base($id);
		}
		$this->load->view('top/bootstrap_top');
		$this->load->view('config/workflow/variate',$data);
		$this->load->view('top/foot');
	}
	
	// 读取单据基本信息
	private function base($id){
		$sql = "
			select 
				id,wf_uid,expression_key,expression_value 
			from 
				t_wf_steps_condition_var 
			where id = ".sqlFilter($id,2);
		$query = $this->db->query($sql);
		$result = $this->simple_model->returnqueryArray($query);
		return $result[0];
	}
	
	// 表单验证函数
	public function validate(){
	
		// 加载验证文件
		require $_SERVER['DOCUMENT_ROOT'].'/wf_engine/manage/application/controllers/config/workflow/Variate_validate.php';
		
		// 初始化验证文件类
		$validate_model = new variate_validate();
		
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
		//添加特殊验证
		$expression_key = request("expression_key");
		if (!preg_match('/^(@|#).+(@|#)$/', $expression_key)) { 
			$response = array(
				'status'	=> 'error',
				'type'		=> 'validate',
				'msg'		=> '变量的格式必需为 @用户变量@ 或  #系统变量#' 
			);
			echo json_encode($response);
			exit();
		}
		
	}
	
	// 保存单据
	public function save(){
	
		// 表单验证
		$this->validate();
	
		$sql_array = array();
		$request_array = array(
			'id','wf_uid','expression_key','expression_value'
		);
		foreach($request_array as $value){
			$$value = request($value);
		}
		
		foreach($request_array as $each_request){
			$sql_array[$each_request] = $$each_request;
		}

		// 拼接更新语句
		$update_sql = $this->simple_model->simple_update_string ( $sql_array, '', 't_wf_steps_condition_var', 'id' );
		$this->db->query (  $update_sql  );
		
		// 如果更新失败，拼接新建插入语句
		if ($this->db->affected_rows () == 0) {
			array_splice($sql_array,0,1);
			$insert_sql = $this->simple_model->simple_insert_string ( $sql_array, '', 't_wf_steps_condition_var' );
			$this->db->query ( $insert_sql  );
		}
		// 返回信息
		$response = array();
		$response['status'] = 'success';

		echo json_encode($response);
	}
}