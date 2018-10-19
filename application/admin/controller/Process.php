<?php

namespace app\admin\controller;
use app\admin\common\Base;
use think\Session;

class Process extends Base
{
    /**
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function index()
    {
		$store_contract = db('store_contract')->where('status',0)->paginate(10,false,[
		     'type' => 'bootstrap',
		     'var_page' => 'page',
		]);
		$count = db('store_contract')->where('status',0)->count();
      	$this->assign('store_contract',$store_contract);
		$this->assign('count',$count);
		return $this->fetch('process_list');
    }

    /**
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function analyze()
    {

		$store_contract = db('store_contract')->where('status=1 or status=2')->paginate(10,false,[
		     'type' => 'bootstrap',
		     'var_page' => 'page',
		]);
		$count = db('store_contract')->where('status=1 or status=2')->count();
      	$this->assign('store_contract',$store_contract);
		$this->assign('count',$count);
		return $this->fetch('process_analysis');
    }

    /**
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\Exception
     */
    public function read($id)
    {
        $where= db('store_contract')->where('id',$id)->field('contract_id')->find();
		$data = db('store_protocol_basic')->where($where)->find();
		$this->view->assign('data',$data);
		$dataRental = db('store_protocol_rental')->where($where)->find();
		$this->view->assign('dataRental',$dataRental);
		$dataService = db('store_protocol_service')->where($where)->find();
		$this->view->assign('dataService',$dataService);
        return $this->fetch('protocol/protocol_read');
		
    }

    /**
     * @param $id
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function analysis($id)
    {   //数据解析
	    
	    $username = Session::get('user_id');
		$role = db('user')->where('username',$username)->column('role');
	    if($role[0]=="超级管理员"){
	    $where= db('store_contract')->where('id',$id)->field('contract_id')->find();
		$whereStatus= db('store_contract')->where('id',$id)->field('status')->find();
	    $dataRental = db('store_protocol_rental')->where($where)->find();
		$string = $dataRental['detail1'];
		$stringNotags = strip_tags($string);
		$stringNo = preg_split("/(;|(\\|))/",$stringNotags);
		$array = array_filter($stringNo);
		
		if(!empty($array)){
        $key = ['start_date','end_date','receivable_price'];
		$arrayNew = array_chunk($array,3,false);
		
		if(count($arrayNew[0])==3){
        foreach($arrayNew as $k=>$v) {
            $new_array[$k] = array_combine($key,$v);
        }
            if (!empty($new_array)) {
                foreach($new_array as $k=>$v){
                    $new_array[$k]['received_price']=0;
                    $new_array[$k]['difference']=$new_array[$k]['receivable_price'];
                    $new_array[$k]['derate']=0;
                    $new_array[$k]['status']=0;
                   $new_array[$k]['contract_id']=$where['contract_id'];
                 }
            }
		} else {
			$this->error('租金数据格式错误,请检查!','process/analyze');
		}
		if($whereStatus['status']==1){
            if (!empty($new_array)) {
                $res = db('account_receivable')->insertAll($new_array);
            }
	    db('store_contract')->where('contract_id',$where['contract_id'])->update(['status' => '2']);	
        db('store_protocol_basic')->where('contract_id',$where['contract_id'])->update(['status' => '2']);
		} else {
			$this->error('解析失败,只有已审批的合同数据才可以解析','process/analyze');
		}
            if (!empty($res)) {
                if($res){
                    $this->success('解析成功');
                } else {
                    $this->error('解析失败','process/analyze');
                }
            }
		} else {
			$this->error('租金数据为空,请检查!','process/analyze');
		}
		} else {
			$this->error('仅超级管理员有此权限!','process/analyze');
		}
    }

    /**
     * @param $contract_id
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function check($contract_id)
    {   
	    $username = Session::get('user_id');
		$role = db('user')->where('username',$username)->column('role');
	    if($role[0]=="财务人员"){
	        $check1 = db('store_contract',[],false)->where('contract_id',$contract_id)->update(['status' => '1']);
            $check2 = db('store_protocol_basic',[],false)->where('contract_id',$contract_id)->update(['status' => '1']);
		    $check = $check1+$check2;
			print_r($check);
		} 
    }

    /**
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function search()
    {
      $company_name = input('company_name');
	  $where['company_name'] = array('like','%'.$company_name.'%');
	  //注意paginate的第三个参数
      $store_contract = db('store_contract')
	                       ->where($where)
	                       ->where('status',0)
	                       ->paginate(10,false,['query'=> request()->param()]);
	  $count = db('store_contract')
	              ->where($where)
				  ->where('status',0)
				  ->count();
      $this->view->assign('store_contract',$store_contract);
      $this->view->assign('count',$count);
      return $this->fetch('process_list');
    }
}