<?php
namespace com\ltworkflow;

class order_rule extends AbstractOrgRule {
    /*
     * 定义按部门id判断审批规则
     * 此方法覆盖父类同名方法
     */
    function getRuleByDeptId() {
        $deptRule = array();
		//$deptRule ['d000058'] = 'LV3toLV2'; // 支付/资金托管  云金融&支付  支付产品
        //$deptRule ['d000004'] = 'LV2toLV2'; // 云金融&支付  数据部
        return $deptRule;
    }

    function LV3toLV2($org) {
        $skipary = array();
        $skipLevel = $this->skipByLevel($org, '3', '2');
        $skipary = $skipLevel;
        return $skipary;
    }
    function LV2toLV2($org) {
        $skipary = array();
        $skipLevel = $this->skipByLevel($org, '2', '2');
        $skipary = $skipLevel;
        return $skipary;
    }

}
