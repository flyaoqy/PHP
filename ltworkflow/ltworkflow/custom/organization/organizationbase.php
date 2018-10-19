<?php
namespace com\ltworkflow;
/**
 * 
 * 通用规则，无自定义脚本时调用
 *
 */
class organizationBase extends AbstractOrgRule{
	/*
	 * 定义按部门id判断审批规则
	 * 	  
	 */
	function getRuleByDeptId() {
		$deptRule = array ();
		return $deptRule;
	}
}