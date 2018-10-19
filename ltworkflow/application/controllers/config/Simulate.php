<?php

class Simulate extends CI_Controller {
	private $nodeList = array();
	private $workflow;
	function __construct() {
		parent::__construct();
		$this->nodeList = array();
	}
	/**
	 * 
	 * 打开页面
	 */
	function loadview(){
		$this->load->view('top/bootstrap_top');
		$this->load->view('config/simulate_view');
		$this->load->view('top/foot');
	}
	/**
	 * 
	 * 单据流程
	 */
	public function draw_flow(){
		$etuid = request("etuid");
		$data['etuid'] = $etuid;
		$this->load->view( 'config/draw_flow_html4',$data);
	}
	/**
	 * 
	 * 单据流程
	 */
	public function draw_flow_data(){
		$etuid = request("etuid");
		require_once ROOTPATH . 'ltworkflow/workflow.php';
		$this->workflow = new com\ltworkflow\workflow();
		
		$historyList = $this->draw_history($etuid);
		$data['historyList'] = $historyList;
		
		$simulateUtil = com\ltworkflow\SimulateUtil::getInstance();
		$simulateUtil->init($etuid);
		$forwardList = $this->draw_forward($etuid,$simulateUtil);
		$data['forwardList'] = $forwardList;
		echo json_encode($data);
	}
	/**
	 * 
	 * 审批过的节点
	 * @param unknown_type $etuid
	 */
	private function draw_history($etuid){
		$logArray = array();
		$sql = "select wflg_salarysn as ssn,u_name name,wf_status status,wflg_comment comment,wflg_finishdate finishdate,wf_actionid,cs_id,wf_uid 
				from t_wf_log inner join v_wf_user on wflg_salarysn = u_usercode 
				where wflg_type = 'handle' and  et_uid = '$etuid'
				order by wflg_finishdate";
		
		$query = $this->db->query($sql);
		foreach ($query->result_array() as $row){
			$row['node_name']  = "未知";
			//处理节点名称
			if(!isNull($row['cs_id']) ){
				$stepId = isNull($row['cs_id'])?"1":$row['cs_id'];
                $wf_xml = com\ltworkflow\XmlUtil::getConfiguration ( $row['wf_uid'] );
                $node = com\ltworkflow\XmlUtil::getElementById($stepId, $wf_xml);
                $initialId = com\ltworkflow\XmlUtil::getInitialId($wf_xml);
                if($initialId == $stepId){
                    $nodeName = "草稿";
                }else{
                    if(isset($node)){
                        $nodeName = $node->getAttribute ( "name" );
                    }
                }

				$row['node_name'] = $nodeName;
			}
			
			//处理审批状态
			switch ($row ['status']) {
				case WF_STATUS_SUBMIT :
					$row ['status'] = '提交';
					break;
				case WF_STATUS_DEALING :
					$row ['status'] = '同意';
					break;
				case WF_STATUS_FINISHED :
					$row ['status'] = '同意';
					break;
				case WF_STATUS_REJECT :
					$row ['status'] = '驳回';
					break;
				case WF_STATUS_RECYCLE :
					$row ['status'] = '收回';
					break;
				case WF_STATUS_ROUTING :
					if ($row ['wf_actionid'] == "-500") {
						$row ['status'] = '会签';
					} else if ($row ['wf_actionid'] == "-550") {
						$row ['status'] = '循环会签';
					} else if ($row ['wf_actionid'] == "-600") {
						$row ['status'] = '会签确认';
					} else if ($row ['wf_actionid'] == "-650") {
						$row ['status'] = '循环会签确认';
					} else if ($row ['wf_actionid'] == "-200") {
						$row ['status'] = '会签驳回';
					} else if ($row ['wf_actionid'] == "-250") {
						$row ['status'] = '循环会签驳回';
					} else {
						$row ['status'] = '会签';
					}
					$row ['node_name'] = '会签';
					break;
				case 'Recycle' :
					$row ['status'] = '收回';
					break;
			}
			array_push($logArray, $row);
		}
		return $logArray;
	}
	/**
	 * 
	 * 将要审批的节点
	 * @param $eruid 实例id
	 * @param $simulateUtil 模拟工具类
	 */
	private function draw_forward($etuid,$simulateUtil){
		$commandContext = com\ltworkflow\CommandContext::getInstance();
		$configContext = com\ltworkflow\ConfigContext::getInstance();
		static $num = 0;
		if($num>50){//防止死循环
		 	return $this->nodeList;
		}else{
			$num++;
		}
		$result = $simulateUtil->get_t_wf_currentstep();
		if( count($result)> 0 ){
			$rowNum = 0;
			$responseBox = array();
			$node = array ();
			$node['curUserList'] = array();
			$node['curUserListStr'] = "";
			foreach( $result as $row ) {
				if($rowNum == 0){//默认第一个处理人处理该单据
					$nextNodeId = $row['cs_id'];
					$nextWfUid = $row['wf_uid'];
					$nextSsn = $row['cs_salarysn'];
					//获取下一节点名
					if($nextNodeId=="1"){
						$nextNodeName = "草稿";
					}else{
						$nextNodeName = $row['cs_nodename'];
					}
					//从页面获取action操作
					$actionId = DEFAULT_ACTION;
		
					$session['ssn'] = $nextSsn;//修改当前处理人
					$session['name'] = $nextSsn;//
					$session['accredit'] = $nextSsn;//
					
					$commandContext->clear();
					$configContext->clear();
					$commandContext->setSysVar('debug', DEBUG_PREDICTION);
					$config['etuid'] = $etuid;
					$config['wfuid'] = $nextWfUid;
					$config['actionid'] = $actionId;
					$config['ssn'] = $nextSsn;
					$responseBox = $this->workflow->doAction($config);
					$node['wfUid'] = $nextWfUid;
					$node['node_id'] = $nextNodeId;
					$node['node_name'] = $nextNodeName;
					$node['curUser'] = $row['name'];
					$node['actionId'] = $actionId;
					$node['responseBox'] = $responseBox;
					$node['status'] = $commandContext->getFlowStatus();
					
				}
				array_push($node['curUserList'],array('ssn'=>$row['cs_salarysn'],'name'=>$row['name']) );
				$node['curUserListStr'] .= $row['name'].",";
				$rowNum ++;
			}
			array_push($this->nodeList,$node);
			if($responseBox['status']=='success'){
				$this->draw_forward($etuid,$simulateUtil);
			}
		}
		return $this->nodeList;;
	}
	/**
	 * 
	 * 将要审批的节点
	 * @param unknown_type $eruid
	 */
	private function draw_forward_old($etuid){
		$commandContext = CommandContext::getInstance();
		$configContext = ConfigContext::getInstance();
		static $num = 0;
		if($num>50){//防止死循环
		 	return $this->nodeList;
		}else{
			$num++;
		}
		$sql = "select cs_salarysn as ssn,u_name name,cs_id, wf_uid 
				from t_wf_currentstep 
				left outer  join v_wf_user on cs_salarysn = u_usercode
				where et_uid = '$etuid' and cs_status<>'Underway' 
				order by cs_salarysn";
		//echo $sql;
		$result = DBCommon::wf_query($sql);
		if( mssql_num_rows($result) > 0 ){
			$rowNum = 0;
			$responseBox = array();
			$node = array ();
			$node['curUserList'] = array();
			$node['curUserListStr'] = "";
			while ( $row = mssql_fetch_array ( $result ) ) {
				if($rowNum == 0){//默认第一个处理人处理该单据
					$nextNodeId = $row['cs_id'];
					$nextWfUid = $row['wf_uid'];
					$nextSsn = $row['ssn'];
					//获取下一节点名
					if($nextNodeId=="1"){
						$nextNodeName = "草稿";
					}else{
						$wf_xml = XmlUtil::getConfiguration ( $nextWfUid );
						$nextNode = XmlUtil::getElementById($nextNodeId, $wf_xml);
						$nextNodeName = $nextNode->getAttribute ( "name" );
					}
					
					
					//从页面获取action操作
					$actionId = DEFAULT_ACTION;
					$commandContext->clear();
					$configContext->clear();
					$commandContext->setSysVar('debug', DEBUG_PREDICTION);
					$config['etuid'] = $etuid;
					$config['wfuid'] = $nextWfUid;
					$config['actionid'] = $actionId;
					$config['ssn'] = $nextSsn;
					$responseBox = $this->workflow->doAction($config);
					
					$node['wfUid'] = $nextWfUid;
					$node['node_id'] = $nextNodeId;
					$node['node_name'] = $nextNodeName;
					$node['ssn'] = $row['ssn'];
					$node['curUser'] = iconvutf($row['name']);
					$node['actionId'] = $actionId;
					$node['responseBox'] = $responseBox;
					$node['status'] = $commandContext->getFlowStatus();
					
				}
				array_push($node['curUserList'], array('ssn'=>$row['ssn'],'name'=>iconvutf($row['name'])));
				$node['curUserListStr'] .= iconvutf($row['name']).",";
				$rowNum ++;
			}
			array_push($this->nodeList,$node);
			if($responseBox['status']=='success'){
				$this->draw_forward($etuid);
			}
		
		}
		
		return $this->nodeList;;
	}
}
