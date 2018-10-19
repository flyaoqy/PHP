<?php 
/**
 * 
 * 采购审批模块，示例单据
 * @author liutao
 * 2017-06-03
 */
class Purchase extends CI_Controller {
	//private $wfuid = "1ba74cea-cbaf-cd6f-cb5d-0924a4e6e3c0";
    private $wfuid = "f48d23f9-e5e2-49f2-3d6c-d855aa40e270";

	function __construct(){
		parent::__construct();
		check_login();
	}

	// 打开单据
	public function open(){
		$ssn = isset($_SESSION["ssn"])?$_SESSION["ssn"]:"";
		$name = isset($_SESSION["name"])?$_SESSION["name"]:"";
		$data = array();
		$data['base'] = array();
		$id = request('id');
		if($id == ''){
			$etuid = uuid();
			$data['base']['id'] = $id;
			$data['base']['et_uid'] = $etuid;
			$data['base']['userno'] = $ssn;
			$data['base']['username'] = $name;
			$data['base']['createtime'] = "";
			$data['wflist']= $this->getWorkflowList();
			$data ['workflow'] = array();
		}else{
			$base = $this->base($id);
			$data['base'] = $base;
			$etuid =  $base['et_uid'];
			$wfuid =  $base['wf_uid'];
			$workflow = $this->workflowInfo($etuid,$wfuid, $ssn);
			$data ['workflow'] = $workflow;
		}
		
		$this->load->view('top/bootstrap_top');
		$this->load->view('purchase/purchase',$data);
		$this->load->view('top/foot');
	}
	
	// 读取单据基本信息
	private function base($id){
		$base = array();
		$sql = "
			select 
				id,et_uid,a.wf_uid,wf_name,userno,username,createtime,contract_name,contract_type,goods_type,contract_sum 
			from 
				t_purchase_contract a 
				left outer join t_wf_workflow b on a.wf_uid = b.wf_uid
			where id = ".sqlFilter($id,3);
		$query = $this->db->query($sql);
		if($query->num_rows()>0){
			$base = $query->row_array();
		
		}
		return $base;
	}
	/**
	 * 
	 * 获取流程列表
	 */
	private function getWorkflowList(){
		$sql = "select wf_uid id,wf_name value from t_wf_workflow order by wf_name";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	// 保存单据
	public function save(){
		$response = array();
		$ssn = isset($_SESSION["ssn"])?$_SESSION["ssn"]:"";
		$name = isset($_SESSION["name"])?$_SESSION["name"]:"";
		$id = request('id');//单据id
		$etuid = request('et_uid');//流程实例id
		
		$base = $this->base($id);//查询单据信息
		if(isNull($base)){//新增
			$sql_array = array();
			$sql_array['et_uid'] = request('et_uid');
			$sql_array['wf_uid'] = request('wf_uid');
			$sql_array['userno'] = $ssn;
			$sql_array['username'] = $name;
			$sql_array['createtime'] = date('Y-m-d H:i:s');
			$sql_array['contract_name'] = request('contract_name');
			$sql_array['contract_type'] = request('contract_type');
			$sql_array['goods_type'] = request('goods_type');
			$sql_array['contract_sum'] = request('contract_sum');
			$insert_sql = $this->simple_model->simple_insert_string ( $sql_array, '', 't_purchase_contract' );
			$this->db->query ( $insert_sql  );
			$response['status'] = 'success';
			$response['message'] = '保存成功';
			
		
		}else{//修改
			$sql_array = array();
			$sql_array['id'] = request('id');
			$sql_array['et_uid'] = request('et_uid');
			$sql_array['contract_name'] = request('contract_name');
			$sql_array['contract_type'] = request('contract_type');
			$sql_array['goods_type'] = request('goods_type');
			$sql_array['contract_sum'] = request('contract_sum');
			// 拼接更新语句
			$update_sql = $this->simple_model->simple_update_string ( $sql_array, '', 't_purchase_contract', 'id' );
			$this->db->query (  $update_sql  );
			
			//调用工作流引擎
			$response = $this->route($etuid,$base['wf_uid']);
		
		}
		
		echo json_encode($response);
	}
	/**
	 * 
	 * 调用工作流
	 * @param  $etuid 实例id
	 * @param  $wfuid 流程uid
	 */
	private function route($etuid,$wfuid) {
		require_once FCPATH . 'ltworkflow/workflow.php';
		$workflow = new com\ltworkflow\workflow();
		
		$ssn = isset($_SESSION["ssn"])?$_SESSION["ssn"]:"";
		$name = isset($_SESSION["name"])?$_SESSION["name"]:"";
		$actionid = request ( '__actionid' );
		$router = request ( "__router" );
		$comment = request ( "__flowcomm" );

		$config = array();
		$config['ssn'] = $ssn;
		$config['name'] = $name;
		$config['etuid'] = $etuid;
		$config['wfuid'] = $wfuid;
		$config['actionid'] = $actionid;
		$config['router'] = $router;
		$config['comment'] = $comment;
		$response = $workflow->doAction($config);

		return $response;
	}
	/**
	 * 流程信息
	 * @param  $etuid
	 * @param  $wfuid 流程uid
	 * @param  $ssn
	 */
	private function workflowInfo($etuid,$wfuid,$ssn){
		$workflowInfo = array();
		//调用工作流引擎，获取当前动作
		require_once FCPATH . 'ltworkflow/workflow.php';
		$workflow = new com\ltworkflow\workflow();
		$workflowInfo = $workflow->getWorkFlow($etuid, $ssn);
		$workflowInfo['actions'] =  $workflow->getAvailableActions($etuid, $ssn,$wfuid,true);
		$workflowInfo['logs'] = $workflow->queryWorkflowLog($etuid);
		return $workflowInfo;
	}
}