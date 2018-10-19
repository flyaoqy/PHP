<?php

namespace app\admin\controller;
use app\admin\common\Base;
use think\Request;

class report extends Base
{

    public function index()
    {
		$start_date = date('Y-m-d');
		$where['start_date'] = array('<=',$start_date);
	    $where['end_date'] = array('>=',$start_date);
		//租金计算
		$ys_zj = db('account_receivable')
		            ->where([
					    'start_date'=>$where['start_date'],
						'end_date'=>$where['end_date'],
					])
					->sum('receivable_price');
		$ss_zj = db('account_receivable')
		            ->where([
					    'start_date'=>$where['start_date'],
						'end_date'=>$where['end_date'],
					])
		            ->sum('received_price');
		$qf_zj = db('account_receivable')
		            ->where([
					    'start_date'=>$where['start_date'],
						'end_date'=>$where['end_date'],
					])
		            ->sum('difference');
		if($ys_zj !== 0){
            $sjl_zj =  sprintf("%01.2f",($ss_zj/$ys_zj)*100).'%';
        } else{
		    $sjl_zj = 0;
		}

		//物业费计算
		$ys_wyf = db('store_protocol_service')
					->sum('month_service');
		$ss_wyf = db('service_receive')
		            ->where([
					    'start_date'=>$where['start_date'],
						'end_date'=>$where['end_date'],
					])
					->sum('received_price');
		$qf_wyf = db('service_receive')
		            ->where([
					    'start_date'=>$where['start_date'],
						'end_date'=>$where['end_date'],
					])
		            ->sum('difference');
		if($ys_wyf!==0){
            $sjl_wyf = sprintf("%01.2f",($ss_wyf/$ys_wyf)*100).'%';
        } else {
            $sjl_wyf = 0;
        }
		//转换为数据，并添加指定的下标
		$data[0][0] = $ys_zj;
		$data[0][1] = $ss_zj;
		$data[0][2] = $qf_zj;
		$data[0][3] = $sjl_zj;
		$data[0][4] = $ys_wyf;
		$data[0][5] = $ss_wyf;
		$data[0][6] = $qf_wyf;
		$data[0][7] = $sjl_wyf;
		$key = ['ys_zj','ss_zj','qf_zj','sjl_zj','ys_wyf','ss_wyf','qf_wyf','sjl_wyf'];
		foreach($data as $k=>$v) {
            $data[$k] = array_combine($key,$v);
        }

		$this->assign('data',$data);
		return $this->fetch('report_list');
    }
	/**
     * 
     *
     */
    public function create()
    {
        //新增商户合同
		return $this->fetch('order_add');
    }

    /**
     *
     *
     * @param  \think\Request $request
     * @return void
     */
    public function save(Request $request)
    {
		
    }

    /**
     *
     *
     * @param  int $id
     * @return void
     */
    public function read($id)
    {
        //
    }

    /**
     *
     *
     * @return void
     */
	public function search()
    {		
	    
	}



}