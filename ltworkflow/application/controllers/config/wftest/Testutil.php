<?php

class Testutil extends CI_Controller {
	private $id;
	private $etuid;
	private $simulateUtil;
	private $engine_version;
	private $wf_version = array();
	private $workflow;
	function __construct() {
		parent::__construct();
		
	}
	public function open(){
		$id = request('id');
		$base = array();
		if($id != ""){
			$sql = "select tc_uid,tc_name,tc_joinmark,tc_discript, tc_xmlcontent 
			from t_wf_testcase where tc_uid = ".sqlFilter($id,3);
			$query = $this->db->query($sql);
			if($query->num_rows()>0){
				$row = $query->row_array();
				foreach ($row as $key => $value) {
	                $base [$key] = $value;
	            }
			}
		}else{
			$id = uuid();
		}
		$data['id'] = $id;
		$data['base'] = $base;
		$this->load->view ( 'top/bootstrap_top');
		$this -> load -> view ( 'config/wftest/testcase', $data );
		$this->load->view ( 'top/foot' );
	
	}
	public function save(){
		$id = request('id');
		$execflag = request('execflag');
		$tc_joinmark = request('tc_joinmark');
		$tc_name = request('tc_name');
		$tc_discript = request('tc_discript');
		$tc_xmlcontent = $_POST['tc_xmlcontent'];
		$response = array();
		$sql = "select 1 from t_wf_testcase where tc_uid = ".sqlFilter($id,3);
		$query = $this->db->query($sql);
		if($query->num_rows() > 0){
			$sqlArray = array ();
			$sqlArray ['tc_uid'] =  $id; 
			$sqlArray ['tc_name'] = $tc_name; 
			$sqlArray ['tc_joinmark'] = $tc_joinmark; 
			$sqlArray ['tc_discript'] = $tc_discript; 
			$sqlArray ['tc_xmlcontent'] = $tc_xmlcontent; 
			$sqlArray ['tc_updateuser'] = $_SESSION['loginusersn']; 
			$sqlArray ['tc_updatetime'] = "getdate()"; 
			$update_sql =$this->simple_model->simple_update_string ( $sqlArray, 'sias', 't_wf_testcase', 'tc_uid' );
			$this->db->query ( $update_sql );
		}else{
			$sqlArray = array ();
			$sqlArray ['tc_uid'] = $id; 
			$sqlArray ['tc_name'] = $tc_name;
			$sqlArray ['tc_joinmark'] = $tc_joinmark;  
			$sqlArray ['tc_discript'] = $tc_discript; 
			$sqlArray ['tc_xmlcontent'] = $tc_xmlcontent; 
			$sqlArray ['tc_updateuser'] = $_SESSION['loginusersn']; 
			$sqlArray ['tc_updatetime'] = "getdate()"; 
			$insert_sql = $this->simple_model->simple_insert_string ( $sqlArray, 'sias', 't_wf_testcase' );
			$this->db->query (  $insert_sql );
		}
		$msg = "";
		if($execflag=="1"){
			$msg = $this->exec($id,$tc_xmlcontent);
			
		}
		if($msg != ""){
			$response['status'] = "error";
			$response['message'] = $msg;
		}else{
			$response['status'] = "success";
		}
		echo json_encode($response);
		
	}
	
	/**
	 * 
	 * 执行测试
	 * @param unknown_type $xmlStr
	 */
	private function exec($id,$xmlStr){
		set_time_limit ( 0 );
		$xml = simplexml_load_string($xmlStr);
		//$this->engine_version = $xml->engine_version;//工作流引擎版本
		$this->etuid = $xml->etuid;//实例id
		$this->id = $id;
		require_once FCPATH . 'ltworkflow/workflow.php';
		$this->workflow = new com\ltworkflow\workflow();
		$this->simulateUtil = SimulateUtil::getInstance();
		//设置缓存
	    $cache_enable = $xml->cache_config->enable;
	    $cache_exclude = $xml->cache_config->exclude;
	    if($cache_enable == "1"){
	    	$excludeList = explode(",", strtolower($cache_exclude));
	    	$cache = DBcommonCache::getInstance();
			$cache->setExcludeList($excludeList);
			$cache->set_cache_on(true);
	    }
		//设置测试用例数据
	    $tableArray = array();
	    foreach ($xml->table as $table){
	    	$tab = array();
		    $tab['tablename'] = $table->attributes()->name;//表名
		    $tab['keycol'] = $table->attributes()->keycol;//主键列
		    $tab['keyvalue'] = $table->attributes()->keyvalue;//主键值
		    $columnsList = array();//所有列值
		    foreach ($table->columns as $columns){
		    	array_push($columnsList, $columns);
		    }
		    $tab['colArray'] = $this->setColArray($columnsList);//循环生成测试用例数据
		    array_push($tableArray, $tab);
	    }
	     //删除之前测试的结果
	    $this->delResult($id);
	    //循环执行审批模拟
	    $this->runTableArray($tableArray);
	    return "";

	}
	/**
	 * 
	 * 循环执行审批模拟
	 * @param  $tableArray 表数组
	 * @param  $allColArray 合并所有条件
	 */
	private function runTableArray($tableArray,$allColArray=array()){
		
		$table = array_shift($tableArray);
		foreach ($table['colArray'] as $colArray){
			$allColArray = array_merge($allColArray,$colArray);//合并所有条件
			$this->setdata($table['tablename'], $table['keycol'], $table['keyvalue'], $colArray);
			if(empty($tableArray)){
				foreach ($allColArray as $row){
					if(@$row['authoruser'] == "1"){
						$authoruser = $row['value'];
						if(strlen($authoruser)== 12){//12 位id处理
							$sql = "SELECT SalarySN FROM idc..T_Employee WHERE CRMID = '$authoruser' ";
							$query = $this->db->query($sql);
							$authoruser = $query->row()->SalarySN;
						}else if (strlen($authoruser)== 36){//36 位id处理
							$sql = "SELECT SalarySN FROM idc..T_Employee WHERE EmpUID = '$authoruser'";
							$query = $this->db->query($sql);
							$authoruser = $query->row()->SalarySN;
						}
					}
				}
				if($authoruser == ""){
					die("起草人不能为空");
				}
				//模拟审批
		    	$this->simulateUtil->init($this->etuid);
    			$nodeList = $this->prediction($this->etuid, $authoruser, $this->simulateUtil,$this->wf_version);
		    	$this->saveResult($this->id,$authoruser,$allColArray,$nodeList);
			}else{
				$this->runTableArray($tableArray,$allColArray);
			}
		}
	}
	/**
	 * 
	 * 循环生成测试用例数据
	 * @param unknown_type $columnsList
	 * @param unknown_type $colArray
	 */
	private function setColArray($columnsList,$tableArray=array(),$colArray=array()){
		$columns = array_shift($columnsList);
	    $c_name = (string)$columns->attributes()->name;//列名
	    $c_descript =  (string)$columns->attributes()->descript;//列描述
	    $c_type =  (string)$columns->attributes()->type;//列类型
	    $c_authoruser =  (string)$columns->attributes()->authoruser;//是否为起草人列 1,0
	    $c_values =  explode(",", $columns->value);//列值
	    foreach ($c_values as $c_value){
	    	$c_array = array();
	    	$c_array['name'] = $c_name;
	    	$c_array['descript'] = $c_descript;
	    	$c_array['authoruser'] = $c_authoruser;
	    	$c_array['type'] = $c_type;
	    	$c_array['value'] = $c_value;
	    	$colArray[$c_name] = $c_array;
	    	if(empty($columnsList)){
	    		array_push($tableArray, $colArray);
	    	}else{
	    		$tableArray = $this->setColArray($columnsList,$tableArray,$colArray);
	    	}
	    }  
		return $tableArray;
	}
	/**
	 * 
	 * 设置测试用例数据
	 * @param $tablename 表名
	 * @param $keycol 主键
	 * @param $keyvalue 主键值
	 * @param $colArray 列
	 */
	private function setdata($tablename,$keycol,$keyvalue,$colArray){
		$colSql = "";
		foreach ($colArray as $key =>$columns ) {
			if($columns['type']=="string"){
				$value = "'".$columns['value']."'";
			}else{
				$value = $columns['value'];
			}
			$colSql .= $key."=".$value.",";
		}
		if($colSql != ""){
			$colSql = substr($colSql,0,strlen($colSql)-1);
			$sql = "update ".$tablename." set ".$colSql." where ".$keycol." = '".$keyvalue."'";
			DBCommon::exec(wf_iconvgbk($sql));
		} 
		
	}
	/**
	 * 
	 * 删除之前的测试结果
	 * @param unknown_type $id
	 */
	private function delResult($id){
		$sql = "delete from t_wf_testcase_result where tcr_pid=".sqlFilter($id,3);
		$this->db->query($sql);
	}
	/**
	 * 
	 * 保存测试结果
	 * @param $id
	 * @param $authoruser
	 * @param $colArray
	 * @param $nodeList
	 */
	private function saveResult($id,$authoruser,$colArray,$nodeList){
		$condtion = "";
		foreach ($colArray as $row){
			$condtion .= $row['descript']."(".$row['value']."),";
		}
		if($condtion !=""){
			$condtion = substr($condtion, 0,strlen($condtion)-1);
		}
		
		$approve = "";
		foreach ($nodeList as $node){
			if($node['node_id'] != "1"){
				$approve .= $node['name']."(".$node['node_id']."),";
			}
		}
		if($approve != ""){
			$approve = substr($approve, 0,strlen($approve)-1);
		}
		$sqlArray = array ();
		$sqlArray ['tcr_pid'] = $id; 
		$sqlArray ['tcr_authoruser'] =  $authoruser; 
		$sqlArray ['tcr_condtion'] = $condtion; 
		$sqlArray ['tcr_approvelist'] = $approve; 
		$sqlArray ['tcr_createuser'] = $_SESSION['loginusersn']; 
		$sqlArray ['tcr_createtime'] = "getdate()"; 
		$insert_sql = $this->simple_model->simple_insert_string ( $sqlArray, 'sias', 't_wf_testcase_result' );
		$this->db->query ( $insert_sql );
		
	}
	/**
	 * 
	 * 预测审批人
	 * @param $eruid 实例id
	 * @param $orguser 工号（获取该人员组织结构）
	 * @param $simulateUtil 模拟工具类
	 */
	private function prediction($etuid,$orguser,$simulateUtil,$wf_version){
		$commandContext = CommandContext::getInstance();
		$configContext = ConfigContext::getInstance();
		$nodeList = array();
		$loopNum = 20;//定义最大循环次数，防止死循环
		$status = 'success';//调用工作流状态，默认成功
		$curUserListStr = "";//审批人
		$result = $simulateUtil->get_t_wf_currentstep();
		$startOrg = false;//组织结构审批起始标志
		while($status == 'success' && count($result)> 0 && $loopNum > 0){
			
			$row = $result[0];
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
			$configContext->clearGlobalVar();//只清空变量
			$commandContext->setSysVar('debug', DEBUG_PREDICTION);
			$commandContext->setSysVar("wf_version", $wf_version);
			$config['etuid'] = $etuid;
			$config['wfuid'] = $nextWfUid;
			$config['actionid'] = $actionId;
			$config['ssn'] = $nextSsn;
			$config['orguser'] = $orguser;
			$responseBox = $this->workflow->doAction($config);
			//$responseBox = doAction($nextNodeId, $actionId, $nextWfUid, $etuid,'','',$orguser,$session);
			$status = $responseBox['status'];//调用工作流状态
			
			$node = array ();
			$node['salarysn'] = $row['cs_salarysn'];
			$node['name'] = $row['name'];
			$node['node_id'] = $nextNodeId;
			$node['node_name'] = $nextNodeName;
			array_push($nodeList,$node);
			
			//取下一审批点，继续循环
			$result = $simulateUtil->get_t_wf_currentstep();
			$loopNum -- ;
		}
		return $nodeList;
	}
	/**
	 * 
	 * 导出结果
	 */
	public function excel(){
		$id = request('id');
		$sql = "select u_name,tcr_approvelist,tcr_condtion 
				from t_wf_testcase_result 
				inner join  v_wf_user on tcr_authoruser = u_usercode
				where tcr_pid = ".sqlFilter($id,3)."
				order by tcr_condtion,u_name,tcr_approvelist ";
		$query = $this->db->query($sql);
		$result = array();
		$condtionTitle = array();
		foreach ($query->result_array() as $row){
			$condtionTitle = array();
			foreach ($row as $key=>$value){
				if($key == 'tcr_condtion'){
					$condtions = explode(",", $value);
					$i = 0;
					foreach ($condtions as $condtion){
						$index = strpos($condtion, "(");
						$title = substr($condtion, 0,$index);
						array_push($condtionTitle, $title);
						
						$cell = substr($condtion, $index+1,strlen($condtion)-$index-2);
						if($i==0){
							$row[$key] = $cell;
						}else{
							$row[$key.$i] = $cell;
						}
						$i ++;
					}
				}
			}
			
			array_push($result, $row);
		}
		
		$title =  array(
					iconvgbk("起草人"),
					iconvgbk("审批人"));
		$title = array_merge($title,$condtionTitle);	
		excel_helper::print_excel ( "test.xls", "sheet",$title, $result);
	}
	/**
	 * 
	 * 导出差异
	 */
	public function compare_excel(){
		$id = request('id');
		$sql = "select a.tcr_approvelist approvelist1,b.tcr_approvelist approvelist2, a.tcr_condtion
				from t_wf_testcase_result a 
				inner join t_wf_testcase a1 on a.tcr_pid = a1.tc_uid
				inner join t_wf_testcase_result b
				inner join t_wf_testcase b1 on b.tcr_pid = b1.tc_uid
				on a.tcr_condtion = b.tcr_condtion and a1.tc_joinmark = b1.tc_joinmark
				where a1.tc_uid = ".sqlFilter($id,3)."
				and a.tcr_approvelist <> b.tcr_approvelist
				order by  a.tcr_condtion,a.tcr_approvelist,b.tcr_approvelist";
		$query = $this->db->query($sql);
		$result = array();
		$condtionTitle = array();
		foreach ($query->result_array() as $row){
			$condtionTitle = array();
			foreach ($row as $key=>$value){
				if($key == 'tcr_condtion'){
					$condtions = explode(",", $value);
					$i = 0;
					foreach ($condtions as $condtion){
						$index = strpos($condtion, "(");
						$title = substr($condtion, 0,$index);
						array_push($condtionTitle, $title);
						
						$cell = substr($condtion, $index+1,strlen($condtion)-$index-2);
						if($i==0){
							$row[$key] = $cell;
						}else{
							$row[$key.$i] = $cell;
						}
						$i ++;
					}
				}
			}
			
			array_push($result, $row);
		}
		//表头
		$sql = "select a.tc_name name1,b.tc_name name2
				from t_wf_testcase a 
				inner join t_wf_testcase b on a.tc_joinmark = b.tc_joinmark
				where a.tc_uid = ".sqlFilter($id,3)." and b.tc_uid <> ".sqlFilter($id,3);
		$query = $this->db->query($sql);
		if($query->num_rows()>0){
			$row = $query->row_array();
			$title =  array($row['name1'],$row['name2']);
		}else{
			$title =  array(iconvgbk("测试用例"),iconvgbk("审批人"));
		}
		$title = array_merge($title,$condtionTitle);	
		excel_helper::print_excel ( "compare.xls", "sheet",$title, $result);
	}
}
