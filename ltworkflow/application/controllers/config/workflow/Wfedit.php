<?php
class Wfedit extends CI_Controller {
	private $wf_xml;
	private $treeArray;
	private $locX = 0;
	private $locY = 0;
	private $offsetX = 120;
	private $offsetY = 60;
	private $maxloop = 50;
	private $curloop = 0;
	function __construct() {
		parent::__construct();
		$this->wf_xml = new DOMDocument ( );
		$this->treeArray = Array();
	}
	/**
	 * 
	 * 树图
	 */
	function tree(){
		$wfuid = request ( "wfuid" );
		$data = array();
		$data['wfuid'] = $wfuid;
		$sql = "select  wf_uid, wf_name, wf_filename 
		from t_wf_workflow where wf_uid=".sqlFilter($wfuid,1);

		$query = $this->db->query($sql);
		if($query->num_rows() > 0){
			$row = $query->row_array();
			$data['base']  = $row;
			$dxml = $row['wf_filename'];
			$this->wf_xml->loadXML ($dxml);
			//$wf_xml = simplexml_load_string($dxml);
			//var_dump($wf_xml);
			$this->dom2Array();
			//var_dump($this->treeArray);
			$treeModel = array();
			$treeModel['class'] = "go.TreeModel";
			$treeModel['nodeDataArray'] = $this->treeArray;
			$data['treeModel'] = json_encode($treeModel);
			
		}
		$this->load->view ( 'config/workflow/treechart', $data);
	}
	
	/**
	 * 
	 * 将dom对象转换为数组
	 * @param $id
	 */
	private function dom2Array($key="1",$id="1"){
		$this->curloop++;
		if($this->curloop > $this->maxloop){
			return;
		}
		$wf_node = $this->getElementById($id);
		if(isNull($wf_node)){//如果没有后续节点则返回
			return ;
		}
		if($id == "1"){//起始节点
			$obj = array();
			$obj['key'] = $id;
			$obj['id'] = $id;
			$obj['name'] = "开始";
			$obj['status'] = "start";
			$this->tree_push($obj);
		}
		$wf_nodeaction = $wf_node->getElementsByTagName ( "action" )->item ( 0 );
		//echo simplexml_import_dom ( $wf_nodeaction )->saveXML ();
		$wf_results = $wf_nodeaction->getElementsByTagName ( 'result' );
		foreach ( $wf_results as $wf_result ) {
			$nextObj = $this->result2nextObj($wf_result,$key);
			if(array_key_exists("key", $nextObj) && $nextObj['id']!=$id){
				$this->tree_push($nextObj);
				if($nextObj['status'] != "end"){
					$this->dom2Array($nextObj['key'],$nextObj['id']);
				}
			}
			
		}
		//无条件结果集
		$unconditional_result = $wf_nodeaction->getElementsByTagName ( 'unconditional-result' )->item ( 0 );
		if(!isNull($unconditional_result)){
			$wf_result = $unconditional_result;
			$nextObj = $this->result2nextObj($wf_result,$key);
			if(array_key_exists("key", $nextObj) && $nextObj['id']!=$id){
				$this->tree_push($nextObj);
				if($nextObj['status'] != "end"){
					$this->dom2Array($nextObj['key'],$nextObj['id']);
				}
			}
		}	
		return ;
	}
	/**
	 * 
	 * 根据result获取下一节点对象
	 * @param unknown_type $wf_result
	 */
	private function result2nextObj($wf_result,$key){
		$nextObj = array();
		$nextObj['conditiontype'] = "";
		$nextObj['roletype'] = "";
		//条件判断类型
		$wf_conditions = $wf_result->getElementsByTagName ( 'condition' )->item ( 0 );
		if(!isNull($wf_conditions)){
			$nextObj['conditiontype'] = $wf_conditions->getAttribute ( 'type' );
		}
		//角色判断类型
		$wf_xmlrole = $wf_result->getElementsByTagName ( 'role' )->item ( 0 );
		if(!isNull($wf_xmlrole)){
			$nextObj['roletype'] = $wf_xmlrole->getAttribute ( 'type' );
		}
		$end_id = $wf_result->getAttribute ( "end" );//结束节点
		if (! isNull ( $end_id )) {
			$nextObj['key'] = $key."->".$end_id;
			$nextObj['id'] = $end_id;
			$nextObj['parent'] = $key;
			$nextObj['status'] = "end";
			$nextObj['name'] = "结束";
		}else{
			$next_id = $wf_result->getAttribute ( 'step' );//下一节点
			if (! isNull ( $next_id )) {
				$next_node = $this->getElementById($next_id);
				$next_node_name = $next_node->getAttribute ( "name" );//下一节点名称
				$nextObj['key'] =  $key."->".$next_id;
				$nextObj['id'] = $next_id;
				$nextObj['parent'] = $key;
				$nextObj['status'] = "flow";
				$nextObj['name'] = $next_node_name;
			}
		}
		return $nextObj;
	}
	/**
	 * 
	 * 将对象放到树中
	 * @param unknown_type $obj
	 */
	private function tree_push($obj){
		$exitFlag = false;
		if(array_key_exists("parent",$obj)){
			foreach ($this->treeArray as $row){
				if($row['key'] == $obj['key'] && $row['parent'] == $obj['parent']){
					$exitFlag = true;
				}
			}
		}
		if(!$exitFlag){
			array_push($this->treeArray, $obj);
		}
		
		
	}
	/**
	 * 
	 * 流程图
	 */
	function flow(){
		$wfuid = request ( "wfuid" );
		$data = array();
		$data['wfuid'] = $wfuid;
		$sql = "select  wf_uid, wf_name, wf_filename
		, wf_layout 
		from t_wf_workflow where wf_uid=".sqlFilter($wfuid,1);
		$query = $this->db->query($sql);
		if($query->num_rows() > 0){
			$row = $query->row_array();
			$data['base']  = $row;
			$dxml = $row['wf_filename'];
			if(!isNull($dxml)){
				$this->wf_xml->loadXML ($dxml);
			}
			
			$layoutNode = array();//节点
			$layoutLink = array();//连接线
			if(!isNull($row['wf_layout'])){
				$layout = json_decode($row['wf_layout']);
				$layoutNode = $layout->nodeDataArray;
				$layoutLink = $layout->linkDataArray;
			}
			$linksModel = array();
			$linksModel['class'] = "go.GraphLinksModel";
			$linksModel['linkFromPortIdProperty'] = "fromPort";
			$linksModel['linkToPortIdProperty'] = "toPort";
			$linksModel['nodeDataArray'] = $this->getNodeDataArray($layoutNode);
			foreach ($layoutNode as $node){
				if(array_key_exists("category", $node) && $node->category=="Comment"){
					array_push($linksModel['nodeDataArray'], $node);
				}
			}
			$linksModel['linkDataArray'] = $this->getlinksDataArray($layoutLink);
			$data['linksModel'] = json_encode($linksModel);
			
			
		
		}
		$this->load->view ( 'config/workflow/flowchart', $data);
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
			$dxml = $row['wf_filename'];
			$this->wf_xml->loadXML ($dxml);
			
			$layoutNode = array();
			$layoutLink = array();
			if(!isNull($row['wf_layout'])){
				$layout = json_decode($row['wf_layout']);
				$layoutNode = $layout->nodeDataArray;
				$layoutLink = $layout->linkDataArray;
			}
			$linksModel = array();
			$linksModel['class'] = "go.GraphLinksModel";
			$linksModel['linkFromPortIdProperty'] = "fromPort";
			$linksModel['linkToPortIdProperty'] = "toPort";
			$linksModel['nodeDataArray'] = $this->getNodeDataArray($layoutNode);
			foreach ($layoutNode as $node){
				if(array_key_exists("category", $node) && $node->category=="Comment"){
					array_push($linksModel['nodeDataArray'], $node);
				}
			}
			$linksModel['linkDataArray'] = $this->getlinksDataArray($layoutLink);
			$data['linksModel'] = json_encode($linksModel);
			
			
		
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
		$this->db->trans_start();
		//更新流程配置
		$sql = "UPDATE t_wf_workflow SET wf_layout = ".sqlFilter($jsonChart,3)." 
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
	 * 获取节点数据
	 * @param $id
	 */
	private function getNodeDataArray($layoutData){
		$nodeDataArray = array();
		//起始节点
		$start_node = $this->wf_xml->getElementsByTagName ( 'initial-actions' )->item ( 0 );
		$obj = array();
		$obj['key'] = $start_node->getAttribute ( "id" );
		$obj['id'] = $start_node->getAttribute ( "id" );
		$obj['text'] = "开始";
		$obj['category'] = "Start";
		$obj['loc'] = $this->locX." ".$this->locY;
		$this->locY += $this->offsetY;
		$nodeDataArray = $this->node_push($nodeDataArray, $layoutData, $obj);
		
		//流程节点
		foreach ( $this->wf_xml->getElementsByTagName ( 'step' ) as $node ) {
		    //
            $nodeType = $node->getAttribute ( "type" );//合并流程
            $actionType = "";//拆分流程
            foreach ( $node->getElementsByTagName ( "action" ) as $wf_nodeaction ) {
                if ($wf_nodeaction->getAttribute ( "type" ) == "split") {
                    $actionType = "split";
                }
            }

			//流程节点
			$obj = array();
			$obj['key'] = $node->getAttribute ( "id" );
			$obj['id'] = $node->getAttribute ( "id" );
			$obj['text'] = $node->getAttribute ( "name" );
			$obj['loc'] = $this->locX." ".$this->locY;
			if($nodeType == "join"){
                $obj['category'] = "join";
            }
            if($actionType == "split"){
                $obj['category'] = "split";
            }
			$this->locY += $this->offsetY;
			$nodeDataArray = $this->node_push($nodeDataArray, $layoutData, $obj);
		}
		//结束
		$end_nodes = $this->wf_xml->getElementsByTagName ( 'end-nodes' )->item ( 0 );
		foreach ( $end_nodes->getElementsByTagName ( 'node' ) as $node ) {
			//结束节点
			$obj = array();
			$obj['key'] = $node->getAttribute ( "id" );
			$obj['id'] = $node->getAttribute ( "id" );
			$obj['text'] = "结束";
			$obj['category'] = "End";
			$obj['loc'] = $this->locX." ".$this->locY;
			$this->locY += $this->offsetY;
			$nodeDataArray = $this->node_push($nodeDataArray, $layoutData, $obj);
		}
		return $nodeDataArray;
		
	}
	/**
	 * 
	 * 获取节点间连接数据
	 * @param unknown_type $id
	 */
	private function getlinksDataArray($layoutData,$id=1,$linksDataArray = array()) {
		$this->curloop++;
		if($this->curloop > $this->maxloop){
			return $linksDataArray;
		}
		$wf_node = $this->getElementById($id);
		if(isNull($wf_node)){//如果没有后续节点则返回
			return $linksDataArray;
		}
		$next_id = "";
		$wf_nodeaction = $wf_node->getElementsByTagName ( "action" )->item ( 0 );
		//echo simplexml_import_dom ( $wf_nodeaction )->saveXML ();
		$wf_results = $wf_nodeaction->getElementsByTagName ( 'result' );
		$this->locY += $this->offsetY;
		foreach ( $wf_results as $wf_result ) {
			//判断条件xml
			$wf_conditiontype = "";
			$wf_condition_value = "";
			if(!isNull($wf_result->getElementsByTagName ( 'condition' ))){
				$wf_condition = $wf_result->getElementsByTagName ( 'condition' )->item ( 0 );
				$wf_conditiontype = $wf_condition->getAttribute ( 'type' );
				if ($wf_conditiontype == "shell") {
					$wf_condition_value = $wf_condition->getAttribute ( 'value' );
				} else if ($wf_conditiontype == "beanshell") {
					$wf_condition_value = $wf_condition->getAttribute ( 'path' );
					$wf_condition_value .= "，type:".$wf_condition->getElementsByTagName ( 'type' )->item ( 0 )->nodeValue;
				} else if ($wf_conditiontype == "config") {
					$wf_condition_value = "";
				} 
			}
			
			//角色xml
			$wf_roletype = "";
			$wf_value = "";
			if(!isNull($wf_result->getElementsByTagName ( 'roles' ))){
				$wf_roles = $wf_result->getElementsByTagName ( 'roles' )->item ( 0 );
				$wf_xmlrole =$wf_roles->getElementsByTagName ( 'role' )->item ( 0 );
				if(!isNull($wf_xmlrole)){
					$wf_roletype = $wf_xmlrole->getAttribute ( 'type' );
					if ($wf_roletype == "0") {
						//取角色表
						$wf_value = $wf_xmlrole->getElementsByTagName ( 'uid' )->item ( 0 )->nodeValue;
					} else if ($wf_roletype == "1") {
						//行政角色  
						$wf_value = "path:".$wf_xmlrole->getAttribute ( 'path' );
						$wf_value .= "，type:".$wf_xmlrole->getElementsByTagName ( 'type' )->item ( 0 )->nodeValue;
					} else if ($wf_roletype == "2") {
						//从配置表取下一处理人
						$wf_value = "";
					} else if ($wf_roletype == "10") {
						//自定义角色。
						$wf_value = "path:".$wf_xmlrole->getAttribute ( 'path' );
						$wf_value .= "，type:".$wf_xmlrole->getElementsByTagName ( 'type' )->item ( 0 )->nodeValue;
					} else if ($wf_roletype == "11") {
						//直接添加人员工号 多个用逗号分隔
						$wf_value = $wf_xmlrole->getElementsByTagName ( 'usercode' )->item ( 0 )->nodeValue;
					}
				}
			}
			$end_id = $wf_result->getAttribute ( "end" );
			if (!isNull ( $end_id )) {//结束节点
				//连接到下一节点
				if($id != $end_id){
					$obj = array();
					$obj["from"] = $id;;
					$obj["to"] = $end_id;
					$obj["fromPort"] = "L";
					$obj["toPort"] = "L";
					$obj["condition_type"] = $wf_conditiontype;
					$obj["condition_value"] = $wf_condition_value;
					$obj["role_type"] = "";
					$obj["role_value"] = "";
					$linksDataArray = $this->link_push($linksDataArray,$layoutData, $obj);
				}
				
			}else{
				$next_id = $wf_result->getAttribute ( 'step' );//下一节点
				//连接到下一节点
				if($id != $next_id){
					$obj = array();
					$obj["from"] = $id;
					$obj["to"] = $next_id;
					$obj["fromPort"] = "R";
					$obj["toPort"] = "R";
					$obj["condition_type"] = $wf_conditiontype;
					$obj["condition_value"] = $wf_condition_value;
					$obj["role_type"] = $wf_roletype;
					$obj["role_value"] = $wf_value;
					$linksDataArray = $this->link_push($linksDataArray,$layoutData, $obj);
					$linksDataArray = $this->getlinksDataArray($layoutData,$next_id,$linksDataArray) ;
				}
			}
			
		}
		//无条件结果集
		$unconditional_result = $wf_nodeaction->getElementsByTagName ( 'unconditional-result' )->item ( 0 );
		if(!isNull($unconditional_result)){
			$wf_result = $unconditional_result;
			$obj = array();
			$end_id = $wf_result->getAttribute ( "end" );
			if (!isNull ( $end_id )) {//结束节点
				//连接到下一节点
				if($id != $end_id){
					$obj = array();
					$obj["from"] = $id;;
					$obj["to"] = $end_id;
					$obj["fromPort"] = "L";
					$obj["toPort"] = "L";
					$obj["condition_type"] = "true";
					$obj["condition_value"] = "true";
					$obj["role_type"] = "";
					$obj["role_value"] = "";
					$linksDataArray = $this->link_push($linksDataArray,$layoutData, $obj);
				}
			}else{
				//角色xml
				$wf_roletype = "";
				$wf_value = "";
				if(!isNull($wf_result->getElementsByTagName ( 'roles' ))){
					$wf_roles = $wf_result->getElementsByTagName ( 'roles' )->item ( 0 );
					$wf_xmlrole = $wf_roles->getElementsByTagName ( 'role' )->item ( 0 );
					if(!isNull($wf_xmlrole)){
						$wf_roletype = $wf_xmlrole->getAttribute ( 'type' );
						if ($wf_roletype == "0") {
							//取角色表
							$wf_value = $wf_xmlrole->getElementsByTagName ( 'uid' )->item ( 0 )->nodeValue;
						} else if ($wf_roletype == "1") {
							//行政角色  
							$wf_value = "path:".$wf_xmlrole->getAttribute ( 'path' );
							$wf_value .= "，type:".$wf_xmlrole->getElementsByTagName ( 'type' )->item ( 0 )->nodeValue;
						} else if ($wf_roletype == "2") {
							//从配置表取下一处理人
							$wf_value = "";
						} else if ($wf_roletype == "10") {
							//自定义角色。
							$wf_value = "path:".$wf_xmlrole->getAttribute ( 'path' );
							$wf_value .= "，type:".$wf_xmlrole->getElementsByTagName ( 'type' )->item ( 0 )->nodeValue;
						} else if ($wf_roletype == "11") {
							//直接添加人员工号 多个用逗号分隔
							$wf_value = $wf_xmlrole->getElementsByTagName ( 'usercode' )->item ( 0 )->nodeValue;
						}
					}
					
				}
				$next_id = $wf_result->getAttribute ( 'step' );//下一节点
				//接到下一节点
				if($id != $next_id){
					$obj = array();
					$obj["from"] = $id;
					$obj["to"] = $next_id;
					$obj["fromPort"] = "B";
					$obj["toPort"] = "T";
					$obj["condition_type"] = "true";
					$obj["condition_value"] = "true";
					$obj["role_type"] = $wf_roletype;
					$obj["role_value"] = $wf_value;
					$linksDataArray = $this->link_push($linksDataArray,$layoutData, $obj);
					$linksDataArray = $this->getlinksDataArray($layoutData,$next_id,$linksDataArray) ;
				}
			}
		}	
		
		return $linksDataArray;
	}
	/**
	 * 
	 * 将节点数据放到数组
	 * 1、使用编辑过位置信息的节点
	 * @param unknown_type $nodeDataArray
	 * @param unknown_type $obj
	 */
	private function node_push($nodeDataArray,$layoutData,$obj){
		foreach ($layoutData as $row){
			if($obj['key'] == $row->key){
				foreach ($row as $key=>$value){
					if($key != 'text'){
						$obj[$key] = $value;
					}
				}
				break;
			}
		}
		array_push($nodeDataArray, $obj);
		return $nodeDataArray;
	
	}
	/**
	 * 
	 * 将连接数据放到数组
	 * 1、已经存在的数据不添加
	 * 2、使用编辑过位置信息的节点
	 * @param unknown_type $obj
	 */
	private function link_push($linksDataArray,$layoutData,$obj){
		$exitFlag = false;
		foreach ($linksDataArray as $row){
			if($row['from'] == $obj['from'] && $row['to'] == $obj['to']){
				$exitFlag = true;
			}
		}
		foreach ($layoutData as $row){
			if($row->from == $obj['from'] && $row->to == $obj['to']){
				foreach ($row as $key=>$value){
					if(!in_array($key, array("condition_type","condition_value","role_type","role_value"))){
						$obj[$key] = $value;
					}
				}
				break;
			}
		}
		if(!$exitFlag){
			array_push($linksDataArray, $obj);
		}
		return $linksDataArray;
		
		
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
}