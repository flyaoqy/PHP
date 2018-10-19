<?php
class Wf_flow_edit extends CI_Controller {
	private $wf_xml;
	private $locX = 0;
	private $locY = 0;
	private $offsetX = 120;
	private $offsetY = 60;
	private $maxloop = 50;
	private $curloop = 0;
	function __construct() {
		parent::__construct();
		$this->wf_xml = new DOMDocument ( );
	}
	/**
	 * 
	 * 流程图
	 */
	function flow(){
		$wfuid = request ( "wfuid" );
		$data = array();
		$data['wfuid'] = $wfuid;
		$data['linksModel'] = json_encode(array());
		
		$sql = "select  wf_uid, wf_name, wf_filename, wf_layout from t_wf_workflow where wf_uid=".sqlFilter($wfuid,1);
		$query = $this->db->query($sql);
		if($query->num_rows() > 0){
			$row = $query->row_array();
			$wf_filename = simplexml_load_string($row['wf_filename']);
			$data['base']  = $row;
			$data['linksModel'] = $row['wf_layout'];
			$data['xmlModel'] =json_encode($wf_filename);
			
		}
		$this->load->view('top/bootstrap_top');
		$this->load->view ( 'config/workflow/flow_edit', $data);
		$this->load->view('top/foot');
	}
	/**
	 * 
	 * 历史流程图
	 */
	function history_flow(){
		$id = request ( "id" );
		$data = array();
		$sql = "select  wf_uid, wf_name, wf_filename
		,  wf_layout 
		from t_wf_workflow_xmllog where id=".sqlFilter($id,2);
		$query = $this->db->query($sql);
		if($query->num_rows() > 0){
			$row = $query->row_array();
			$data['base']  = $row;
			$data['linksModel'] = $row['wf_layout'];
		}
		$this->load->view ( 'config/workflow/flowchart', $data);
	}
	/**
	 * 
	 * 保存流程图
	 */
	function flow_chart_save(){
		$wfuid = request ( "wfuid" );
		$jsonChart = request("mySavedModel");
		
		$objChart = json_decode($jsonChart,true);
		$this->node2xmlObj($objChart['nodeDataArray']);//审批节点转为xml
		$this->addNodeLink($objChart['linkDataArray']);//xml节点添加审批分支
		
		
		$this->db->trans_start();
		//xml配置字符串
		$wf_filename = simplexml_import_dom ( $this->wf_xml )->saveXML ();
		//更新流程配置
		$sql = "UPDATE t_wf_workflow SET wf_filename = '$wf_filename', wf_layout = ".sqlFilter($jsonChart,3)." 
			WHERE wf_uid = ".sqlFilter($wfuid,1);
		$this->db->query($sql);
		
		//查询最近的历史版本
		$sql = "SELECT wf_layout,wf_version 
				FROM t_wf_workflow_xmllog 
				WHERE wf_uid = ".sqlFilter($wfuid,1)."
				ORDER BY logtime DESC 
				limit 1 ";
		$query = $this->db->query($sql);
		if($query->num_rows() > 0){//
			$row = $query->row_array();
			$preLayout = trim($row['wf_layout']);
			$preVersion = $row['wf_version'];
			if($jsonChart != $preLayout){//如果XML内容不相同，添加历史版本
				$newVersion = $preVersion + 0.0001 ;//小版本号加 0.0001
				$sql = "INSERT INTO t_wf_workflow_xmllog
        				( wf_uid ,wf_name ,wf_filename ,wf_layout ,wf_version ,logtime)
						SELECT wf_uid ,wf_name ,wf_filename ,wf_layout,$newVersion,now() 
						FROM t_wf_workflow WHERE wf_uid =".sqlFilter($wfuid,1);
				$this->db->query($sql);
			}
		}else{
			//第一版
			$sql = "INSERT INTO t_wf_workflow_xmllog
        				( wf_uid ,wf_name ,wf_filename ,wf_layout ,wf_version ,logtime)
						SELECT wf_uid ,wf_name ,wf_filename ,wf_layout,1,now() 
						FROM t_wf_workflow WHERE wf_uid =".sqlFilter($wfuid,1);
				$this->db->query($sql);
		}
		
		$this->db->trans_complete();
		$response['status'] = "success";
		echo json_encode($response);
	}
	/**
	 * 
	 * 审批节点转为xml
	 */
	private function node2xmlObj($nodeDataArray){
		
		if($nodeDataArray){
			$root = $this->wf_xml->createElement("workflow");  
			$initial_actions = $this->wf_xml->createElement("initial-actions");  
			$steps = $this->wf_xml->createElement("steps");   
			$end_nodes = $this->wf_xml->createElement("end-nodes");   
			
			//添加子节点
			foreach ($nodeDataArray as $nodeData){
				
				if(key_exists('category', $nodeData) && $nodeData['category']=='Start'){//initial-actions
					//添加属性id
					$id = $this->wf_xml->createAttribute('id');
					$newsid = $this->wf_xml->createTextNode($nodeData['key']);
					$id->appendChild($newsid);
					$initial_actions->appendChild($id);
					
					//操作action
					$actions = $this->wf_xml->createElement("actions");  
					$action = $this->wf_xml->createElement("action");  
					//添加属性id
					$id = $this->wf_xml->createAttribute('id');
					$newsid = $this->wf_xml->createTextNode('1');
					$id->appendChild($newsid);
					$action->appendChild($id);
					//添加属性name
					$name = $this->wf_xml->createAttribute('name');
					$newname = $this->wf_xml->createTextNode('送审');
					$name->appendChild($newname);
					$action->appendChild($name);
					//添加results
					$results = $this->wf_xml->createElement("results");  
					$action->appendChild($results);
					
					//添加子节点action
					$actions->appendChild($action); 
					
					

					//添加子节点actions
					$initial_actions->appendChild($actions); 	
				}elseif(key_exists('category', $nodeData) && $nodeData['category']=='End'){//end-nodes
					$node = $this->wf_xml->createElement("node");  
					//添加属性id
					$id = $this->wf_xml->createAttribute('id');
					$newsid = $this->wf_xml->createTextNode($nodeData['key']);
					$id->appendChild($newsid);
					$node->appendChild($id);
					//添加属性name
					$name = $this->wf_xml->createAttribute('name');
					$newname = $this->wf_xml->createTextNode('结束');
					$name->appendChild($newname);
					$node->appendChild($name);
                    //添加属性type=end
                    $type = $this->wf_xml->createAttribute('type');
                    $newtype = $this->wf_xml->createTextNode('end');
                    $type->appendChild($newtype);
                    $node->appendChild($type);
					$end_nodes->appendChild($node); 	
				
				}else{//setp
					$step = $this->wf_xml->createElement("step");  	
					//操作step
					//添加属性id
					$id = $this->wf_xml->createAttribute('id');
					$newsid = $this->wf_xml->createTextNode($nodeData['key']);
					$id->appendChild($newsid);
					$step->appendChild($id);
					//添加属性name
					$name = $this->wf_xml->createAttribute('name');
					$newname = $this->wf_xml->createTextNode($nodeData['text']);
					$name->appendChild($newname);
					$step->appendChild($name);
					
					//操作action
					$actions = $this->wf_xml->createElement("actions");  
					$action = $this->wf_xml->createElement("action");  
					//添加属性id
					$id = $this->wf_xml->createAttribute('id');
					$newsid = $this->wf_xml->createTextNode('1');
					$id->appendChild($newsid);
					$action->appendChild($id);
					//添加属性name
					$name = $this->wf_xml->createAttribute('name');
					$newname = $this->wf_xml->createTextNode('通过');
					$name->appendChild($newname);
					$action->appendChild($name);
					//添加results
					$results = $this->wf_xml->createElement("results");  
					$action->appendChild($results);
				
					//添加子节点action
					$actions->appendChild($action); 
					
					//添加子节点actions
					$step->appendChild($actions); 
					$steps->appendChild($step); 
				
				}
				
			}
			$root->appendChild($initial_actions); 
			$root->appendChild($steps); 
			$root->appendChild($end_nodes); 
			$this->wf_xml->appendChild($root); 
			//echo  simplexml_import_dom ( $doc )->saveXML ();
		}
		return true;
	}
	/**
	 * 
	 * xml节点添加审批分支
	 * @param $linkDataArray 连接线
	 */
	private function addNodeLink($linkDataArray){
		//$doc = $this->wf_xml;
		$results = null ;
		foreach ($linkDataArray as $linkData){
			$node = $this->getElementById($linkData['from']);
			//查询results 节点 并添加分支 result
			if($node){
				$results = $node->getElementsByTagName ( 'results' ) ->item ( 0 );
			}
			if($results){
				//添加子节点result
				$result = $this->wf_xml->createElement("result"); 
				//添加属性step
				$step = $this->wf_xml->createAttribute('step');
				$newstep = $this->wf_xml->createTextNode($linkData['to']);
				$step->appendChild($newstep);
				$result->appendChild($step);
				
				//添加子节点roles
				$roles = $this->wf_xml->createElement("roles");  
				$role = $this->wf_xml->createElement("role"); 
				//添加role type 
				$roleType = $this->wf_xml->createAttribute('type');
				$newroleType = $this->wf_xml->createTextNode('2');
				$roleType->appendChild($newroleType);
				$role->appendChild($roleType);
				$roles->appendChild($role);
				$result->appendChild($roles);
				
				//添加子节点condition
				$condition = $this->wf_xml->createElement("condition");  
				$conditionType = $this->wf_xml->createAttribute('type');
				$newconditionType = $this->wf_xml->createTextNode('config');
				$conditionType->appendChild($newconditionType);
				$condition->appendChild($conditionType);
				$result->appendChild($condition);
				
				//添加子节点result
				$results->appendChild($result); 
			}
		}

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
		foreach ( $this->wf_xml->getElementsByTagName ( 'node' ) as $node ) {
			//判断是否id
			if ($node->getAttribute ( "id" ) == $wf_id) {
				return $node;
			}
		}
		return null;
	}
}