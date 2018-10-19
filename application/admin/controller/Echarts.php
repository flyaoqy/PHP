<?php

namespace app\admin\controller;
use app\admin\common\Base;

class echarts extends Base
{
    /**
     * 显示会员列表
     *
     * @return \think\Response
     */
    public function linechart()
    {  
		$arr1= db('service_receive')->column('received_price');
		$arr2 = db('service_receive')->column('real_date');
		$arr1 = json_encode($arr1);
		$arr2 = json_encode($arr2);
		$this->view->assign('arr1',$arr1);
		$this->view->assign('arr2',$arr2);
		return $this->fetch('echarts1');
		
    }
	 public function columnchart()
    {
        return $this->fetch('echarts2');
		
    }
	 public function mapchart()
    {
        return $this->fetch('echarts3');
	}
	 public function piechart()
    {
        return $this->fetch('echarts4');
	}
	 public function radarchart()
    {
        return $this->fetch('echarts5');
	}
	 public function klinechart()
    {
        return $this->fetch('echarts6');
	}
	 public function thermodynamicchart()
    {
        return $this->fetch('echarts7');
	}
	 public function meterchart()
    {
        return $this->fetch('echarts8');
	}

}