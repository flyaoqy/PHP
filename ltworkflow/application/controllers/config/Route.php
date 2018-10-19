<?php
require_once ROOTPATH . 'ltworkflow/workflow.php';

class Route extends CI_Controller {
    //流程xml配置
    //private $wflist = array();
    //组织结构流程id(取消)
    //private $org_wfid = "";

    /*
     * 流程信息
     * author_sn：起草人工号
     * route_step：调整环节（array）
     * main_wfid：主流程ID
     * wflist：流程XML各个环节信息（主\子流程）
     */
    private $wfinfo;

    public function __construct() {
        parent::__construct();
        //变量持久化
        if (isset($_SESSION['wfinfo'])) {
            $this->wfinfo = $_SESSION['wfinfo'];
        }
    }

    //打开
    public function open() {
        $data = array();
        $this->load->view('top/bootstrap_top');
        $this->load->view('config/route', $data);
        $this->load->view('top/foot');
    }

    //查看流程信息、获取调整列表
    public function query() {
        //每次查询全局变量
        $this->wfinfo = array();

        $rtnarray = array();
        $id = request('etuid');
        $user = request('usercode');

        //设置起草人
        if (isNull($user)) {
            $sql = "select u_usercode,u_name from v_wf_user
                where u_usercode=(
                  select et_createuser from t_wf_entry
                  where et_uid='$id'
                )";
        } else {
            $sql = "select u_usercode,u_name from v_wf_user where u_usercode='$user'";
        }
        $result = com\ltworkflow\DBCommon::query($sql);
        if ($row = com\ltworkflow\DBCommon::fetch_array($result)) {
            $this->wfinfo['author_sn'] = $row['u_usercode'];
        }

        //查看当前流程
        $wflist = array();
        //子流程
        $sql = "SELECT b.wf_name,'2' as et_state,b.wf_uid FROM t_wf_flowstack a
                INNER JOIN t_wf_workflow b
                ON a.wf_uid=b.wf_uid
                WHERE et_uid='$id'";
        $result = com\ltworkflow\DBCommon::query($sql);
        while ($row = com\ltworkflow\DBCommon::fetch_array($result)) {
            $row['wf_name'] = com\ltworkflow\wf_iconvutf($row['wf_name']);
            $wflist[] = $row;
        }
        //主流程
        $sql = "select a.wf_name,b.et_state,a.wf_uid,b.et_uid from t_wf_workflow a
                inner join t_wf_entry b
                on a.wf_uid=b.wf_uid
                where b.et_uid='$id'";
        $result = com\ltworkflow\DBCommon::query($sql);
        if ($row = com\ltworkflow\DBCommon::fetch_array($result)) {
            $row['wf_name'] = com\ltworkflow\wf_iconvutf($row['wf_name']);
            $wflist[] = $row;
            //设置主流程ID
            $this->wfinfo['main_wfid'] = strtoupper($row['wf_uid']);
            //主流程开始节点id
            $xml = com\ltworkflow\XmlUtil::getConfiguration($row['wf_uid']);
            $this->wfinfo['initId'] = com\ltworkflow\XmlUtil::getInitialId($xml);
            //主流程结束节点id
            $this->wfinfo['endId'] = com\ltworkflow\XmlUtil::getEndId($xml);
             
        }
        $rtnarray['wflist'] = $wflist;

        //获取流程xml信息
        $this->init_wflist($this->wfinfo['main_wfid']);
        //查看当前处理人
        $sql = "select b.u_usercode,b.u_name,a.cs_status,a.cs_nodename,c.wf_name from t_wf_currentstep a
            inner join v_wf_user b
            on a.cs_salarysn=b.u_usercode
            INNER JOIN t_wf_workflow c
            ON a.wf_uid=c.wf_uid
            where et_uid='$id'
            order by cs_endTime";
        $result = com\ltworkflow\DBCommon::query($sql);
        $wfcurrent = array();
        while ($row = com\ltworkflow\DBCommon::fetch_array($result)) {
            $row['u_name'] = com\ltworkflow\wf_iconvutf($row['u_name']);
            $row['cs_nodename'] = com\ltworkflow\wf_iconvutf($row['cs_nodename']);
            $row['wf_name'] = com\ltworkflow\wf_iconvutf($row['wf_name']);
            switch ($row['cs_status']) {
                case 'Draft':
                    $row['status'] = '起草';
                    break;
                case 'Reject':
                    $row['status'] = '驳回';
                    break;
                case 'Dealing':
                    $row['status'] = '流程中';
                    break;
                case 'Underway':
                    $row['status'] = '已会签';
                    break;
                case 'Routing':
                    $row['status'] = '会签';
                    break;
            };
            array_push($wfcurrent, $row);
        }
        $rtnarray['wfcurrent'] = $wfcurrent;
        //查看已审批人
        $sql = "select b.u_usercode,b.u_name,a.wf_status,a.cs_id,a.wf_actionid,a.wf_uid from t_wf_log a
                inner join v_wf_user b
                on a.wflg_salarysn=b.u_usercode
                where et_uid='$id'
                order by a.wflg_date";
        $result = com\ltworkflow\DBCommon::query($sql);
        $wflog = array();
        while ($row = com\ltworkflow\DBCommon::fetch_array($result)) {
            $row['u_name'] = com\ltworkflow\wf_iconvutf($row['u_name']);
            switch ($row['wf_status']) {
                case 'Submit':
                    $row['status'] = '送审';
                    break;
                case 'Reject':
                    $row['status'] = '驳回';
                    break;
                case 'Recycle':
                    $row['status'] = '收回';
                    break;
                case 'Dealing':
                    $row['status'] = '通过';
                    break;
                case 'Routing':
                    $row['status'] = '会签';
                    break;
                default:
                    $row['status'] = '通过';
                    break;
            };
            $wfuid=strtoupper($row['wf_uid']);
            $xmlconfig = $this->wfinfo['wflist'][$wfuid];
            $row['wf_name'] = $xmlconfig['wf_name'];
            if (isset($xmlconfig['step'][$row['cs_id']])) {
                $row['wf_stepname'] = $xmlconfig['step'][$row['cs_id']];
            } else {
                $row['wf_stepname'] = "";
            }
            array_push($wflog, $row);

        }
        $rtnarray['wflog'] = $wflog;

        //环节列表
        $rtnarray['steplist'] = $this->getstep();
        //处理人列表
        $rtnarray['usrlist'] = $this->getuser($wflog);

        echo json_encode($rtnarray);

        //持久化、session赋值
        $_SESSION['wfinfo'] = $this->wfinfo;
    }

    //获取审批环节列表
    private function getstep() {
        $steparray = array();
        $keylist = ",";
        foreach ($this->wfinfo['wflist'] as $wfrow) {
            foreach ($wfrow['step'] as $key => $value) {
                if ($key != '1001') {//非组织结构
                    $keyword = $wfrow['wf_uid'] . "#" . $key . ",";
                    if (!stristr($keylist, $keyword)) {
                        $row = array();
                        $row['wfname'] = $wfrow['wf_name'];
                        $row['wfid'] = $wfrow['wf_uid'];
                        $row['stpeid'] = $key;
                        $row['stpename'] = $value;
                        $steparray[] = $row;
                        $keylist .= $keyword;
                    }
                }
            }
        };
        return $steparray;
    }

    //获取审批人列表
    private function getuser($list) {
        $userarray = array();
        $keylist = ",";
        foreach ($list as $row) {
            if ($row['cs_id'] != '1') {
                //审批环节
                if ($row['wf_status'] != 'Routing') {
                    //避免流程反复时重复插入
                    $key = $row['wf_uid'] . "#" . $row['cs_id'] . ",";
                    if (!stristr($keylist, $key)) {
                        $userarray[] = $row;
                        $keylist .= $key;
                    }
                }
            }
        }
        //起草(插入数组头)
        $start_row['u_usercode'] = $this->wfinfo['author_sn'];
        $start_row['u_name'] = '起草';
        $start_row['wf_actionid'] = '0';
        $start_row['cs_id'] = $this->wfinfo['initId'];
        $start_row['wf_uid'] = $this->wfinfo['main_wfid'];
        $start_row['wf_stepname'] = '起草';
        array_unshift($userarray, $start_row);

        //审批完成（插入数组尾）
        $end_row['u_usercode'] = '';
        $end_row['u_name'] = '审批完成';
        $end_row['wf_actionid'] = '';
        $end_row['cs_id'] = $this->wfinfo['endId'];
        $end_row['wf_uid'] = '';
        $end_row['wf_stepname'] = '结束';
        array_push($userarray, $end_row);
        return $userarray;
    }

    //读取流程xml
    private function init_wflist($wfid) {
        if (isNull($wfid)) {
            return;
        }else{
            $wfid=strtoupper($wfid);
        }
        //判断已经取过流程信息
        if (isset($this->wfinfo['wflist'][$wfid])) {
            return;
        }
        $wf = array();
        $sql = "select wf_name,wf_uid from t_wf_workflow
                where wf_uid='$wfid'";
        $result = com\ltworkflow\DBCommon::query($sql);
        if ($row = com\ltworkflow\DBCommon::fetch_array($result)) {
            $row['wf_name'] = com\ltworkflow\wf_iconvutf($row['wf_name']);

            $wf = $row;
        }
        $wf['step'] = array();
        $xml = com\ltworkflow\XmlUtil::getConfiguration($wfid);
        $steps = $xml->getElementsByTagName("step");
        foreach ($steps as $step) {
            $step_id = $step->getAttribute("id");
            $step_name = $step->getAttribute("name");
            $wf['step'][$step_id] = $step_name;

            //取当前环节是否有子流程
            $sub_flows = $step->getElementsByTagName("sub-flow");
            foreach ($sub_flows as $flow) {
                $sub_wfid = $flow->getAttribute("uid");
                $this->init_wflist($sub_wfid);
                $this->wfinfo['wflist'][$sub_wfid]['p_stepid'] = $step_id;
                $this->wfinfo['wflist'][$sub_wfid]['p_wfid'] = $wfid;
            }
        }
        $this->wfinfo['wflist'][$wfid] = $wf;
    }

    //流程处理
    function workflowroute() {
        $result = array('status' => 'success');
        $errormsg = "";

        //初始化流程调整信息
        $etuid = request('etuid');
        $this->wfinfo['route_step']['etuid'] = $etuid;
        $this->wfinfo['route_step']['stepid'] = request("stepid");
        $this->wfinfo['route_step']['wfid'] = request("wfid");
        $this->wfinfo['route_step']['user'] = request("user");
        $this->wfinfo['route_step']['nodename'] = request("nodename");
        //默认子流程为空
        $this->wfinfo['route_step']['p_stepid'] = "";
        $this->wfinfo['route_step']['p_wfid'] = "";

        //校验输入参数
        if (isNull($this->wfinfo['route_step']['etuid']) || isNull($this->wfinfo['route_step']['stepid'])) {
            $errormsg .= '输入参数错误！';
        } else {
            $type = request('type');
            //事务处理
            com\ltworkflow\DBCommon::begin_tran();
            switch ($type) {
                case "byuser":
                    //检查调整环节是否为子流程
                    $wfid = strtoupper($this->wfinfo['route_step']['wfid']);
                    if ($wfid != $this->wfinfo['main_wfid']) {
                        $row = $this->wfinfo['wflist'][$wfid];
                        $this->wfinfo['route_step']['p_stepid'] = $row['p_stepid'];
                        $this->wfinfo['route_step']['p_wfid'] = $row['p_wfid'];
                    }
                    $errormsg .= $this->modify_currentstep();
                    $errormsg .= $this->modify_entry();
                    $errormsg .= $this->modify_flowstack();
                    $errormsg .= $this->modify_interface();
                    break;
                case "bystep":
                	//设置任务所需变量
                	$commandContext = com\ltworkflow\CommandContext::getInstance();
                    $commandContext->setWfuid($this->wfinfo['route_step']['wfid']);
                    $commandContext->setEtuid($this->wfinfo['route_step']['etuid']);
                    $commandContext->setNextStepid($this->wfinfo['route_step']['stepid']);
                	com\ltworkflow\XmlUtil::setGlobal ($this->wfinfo['main_wfid']);//设置主流程全局变量
                	$configContext = com\ltworkflow\ConfigContext::getInstance();
		            $initMainNode = $configContext->getInitElement($this->wfinfo['main_wfid']);
					if (!empty($initMainNode)) {
						//初始化配置信息。
						$init_cls = com\ltworkflow\class_load ($initMainNode, "init",  $this->wfinfo['route_step']['etuid'] );
						$init_cls->init_entry ();
					}
	                	
                    //检查调整环节是否为子流程
                    $wfid = strtoupper($this->wfinfo['route_step']['wfid']);
                    if ($wfid != $this->wfinfo['main_wfid']) {
                    	com\ltworkflow\XmlUtil::setGlobal ($wfid);//设置子流程全局变量
	                    $initSubNode = $configContext->getInitElement($wfid);
						if (!empty($initSubNode)) {
							//初始化配置信息。
							$init_sub = com\ltworkflow\class_load ( $initSubNode, "init",  $this->wfinfo['route_step']['etuid'] );
							$init_sub->init_entry ();
						}
                        $row = $this->wfinfo['wflist'][$wfid];
                        $this->wfinfo['route_step']['p_stepid'] = $row['p_stepid'];
                        $this->wfinfo['route_step']['p_wfid'] = $row['p_wfid'];
                    }

                    //读取配置文件取审批人
                    $xml = com\ltworkflow\XmlUtil::getConfiguration($wfid);
                    $xpath = new DOMXpath($xml);
                    $results = $xpath->query("//result[@step='" . $this->wfinfo['route_step']['stepid'] . "']|//unconditional-result[@step='" . $this->wfinfo['route_step']['stepid'] . "']");
                    if ($results->length > 0) {
                        $xml_result = $results->item(0);
                        if ($xml_result->getAttribute("auto")) {
                            $errormsg .= "请选择子流程配置具体环节！";
                        } else {
                            $role = $xml_result->getElementsByTagName('roles')->item(0);
                            //设置工作流必须的系统变量
                            com\ltworkflow\__addGlobalVar('@@createuser__', $this->wfinfo['author_sn']);
                            com\ltworkflow\__addGlobalVar('@@orgcreateuser__', $this->wfinfo['author_sn']);
                            //取审批人
                            $users = com\ltworkflow\XmlUtil::getRoles($role, $this->wfinfo['route_step']['etuid']);
                            if (!is_array($users) || count($users) == 0) {
                                $errormsg .= "取当前环节审批人出错！";
                            } else {
                                $this->wfinfo['route_step']['user'] = implode(",", $users);
                                $errormsg .= $this->modify_currentstep();
                                $errormsg .= $this->modify_entry();
                                $errormsg .= $this->modify_flowstack();
                                $errormsg .= $this->modify_interface();
                            }
                        }

                    } else {
                        $errormsg .= "获取配置文件出错！";
                    }
					break;
            };
            com\ltworkflow\DBCommon::end_tran();
        }
        if ($errormsg != "") {
            $result['status'] = "error";
            $result['msg'] = $errormsg;
        }
        echo json_encode($result);
    }

    //修改当前环节表
    private function modify_currentstep() {
        $return = "";
        $etuid = $this->wfinfo['route_step']['etuid'];
        $wfid = $this->wfinfo['route_step']['wfid'];
        $stepid = $this->wfinfo['route_step']['stepid'];
        $user = $this->wfinfo['route_step']['user'];
        $nodename = $this->wfinfo['route_step']['nodename'];
        try {
            $wf_sql = "delete from t_wf_currentstep where et_uid ='$etuid'";
            com\ltworkflow\DBCommon::exec(com\ltworkflow\wf_iconvgbk($wf_sql));
            $users = explode(",", $user);
            foreach ($users as $row) {
                //结束时不用插入数据
                if ($stepid != $this->wfinfo['endId']) {
                    $sql_array['uid'] = uuid();
                    $sql_array['et_uid'] = $etuid;
                    $sql_array['cs_salarysn'] = $row;
                    $sql_array['cs_updateby'] = $row;
                    $sql_array['cs_id'] = $stepid;
                    if ($stepid == $this->wfinfo['initId']) {
                        $sql_array['cs_status'] = "Draft";
                        $sql_array['steplock'] = "unlocked";
                        $sql_array['cs_nodename'] = "起草";
                    } else if ($stepid == '1001') {
                        $deptid = $this->wfinfo['route_step']['deptid'];
                        $sql_array['cs_status'] = "Dealing";
                        $sql_array['steplock'] = "locked";
                        $sql_array['cs_nodename'] = "部门领导审批";
                        $sql_array['cs_parentid'] = $deptid;
                    } else {
                        $sql_array['cs_status'] = "Dealing";
                        $sql_array['steplock'] = "locked";
                        $sql_array['cs_nodename'] = $nodename;
                    }
                    $sql_array['cs_endTime'] = date('Y-m-d H:i:s');
                    $sql_array['wf_uid'] = "$wfid";
                    $insert_sql = $this->simple_model->simple_insert_string ( $sql_array, '', 't_wf_currentstep' );
                    com\ltworkflow\DBCommon::exec(com\ltworkflow\wf_iconvgbk($insert_sql));
                }
            }
        } catch (Exception $e) {
            $return = '更新t_wf_currentstep表出错';
        }
        return $return;
    }

    //修改子流程表
    private function modify_flowstack() {
        $return = "";
        $etuid = $this->wfinfo['route_step']['etuid'];
        $wfid = $this->wfinfo['route_step']['wfid'];
        $stepid = $this->wfinfo['route_step']['stepid'];
        $p_stepid = $this->wfinfo['route_step']['p_stepid'];
        $p_wfid = $this->wfinfo['route_step']['p_wfid'];

        try {
            $wf_sql = "delete from t_wf_flowstack where et_uid='$etuid'";
            com\ltworkflow\DBCommon::exec(com\ltworkflow\wf_iconvgbk($wf_sql));
            //流程中
            if ($stepid != $this->wfinfo['initId'] && $stepid != $this->wfinfo['endId']) {
                if ($this->wfinfo['main_wfid'] != $wfid) {
                    $sql_array['fs_uid'] = uuid();
                    $sql_array['fs_puid'] = $p_wfid;
                    $sql_array['et_uid'] = $etuid;
                    $sql_array['wf_uid'] = $wfid;
                    $sql_array['fs_pcsid'] = $p_stepid;
                    $sql_array['fs_createdate'] = date('Y-m-d H:i:s');
                    $insert_sql =  $this->simple_model->simple_insert_string ( $sql_array, '', 't_wf_flowstack' );
                   
                    com\ltworkflow\DBCommon::exec(com\ltworkflow\wf_iconvgbk($insert_sql));
                }
            }
        } catch (Exception $e) {
            $return = '更新t_wf_flowstack表出错';
        }
        return $return;
    }

    //实例表操作
    private function modify_entry() {
        $return = "";
        $etuid = $this->wfinfo['route_step']['etuid'];
        $stepid = $this->wfinfo['route_step']['stepid'];

        try {
            if ($stepid == $this->wfinfo['endId']) {
                $wf_sql = "update t_wf_entry set et_state='5' where et_uid='$etuid'";
            } else if ($stepid == $this->wfinfo['initId']) {
                $wf_sql = "update t_wf_entry set et_state='1' where et_uid='$etuid'";
            } else {
                $wf_sql = "update t_wf_entry set et_state='2' where et_uid='$etuid'";
            }
            com\ltworkflow\DBCommon::exec(com\ltworkflow\wf_iconvgbk($wf_sql));
        } catch (Exception $e) {
            $return = '更新t_wf_entry表出错';
        }
        return $return;
    }

    //同步接口表
    private function modify_interface() {
        $return = "";
        $etuid = $this->wfinfo['route_step']['etuid'];
        $stepid = $this->wfinfo['route_step']['stepid'];

        try {
            if ($stepid == '500') {//审批完成
                
            } else if ($stepid == '1') {//草稿

            } else {//流程中
               
            }
            //com\ltworkflow\DBCommon::exec(com\ltworkflow\wf_iconvgbk($sql));
        } catch (Exception $e) {
            $return = '更新接口表出错';
        }
        return $return;
    }
}
