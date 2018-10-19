<?php

namespace app\admin\controller;
use app\admin\common\Base;
use think\Request;

class protocol extends Base
{
    /**
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function index()
    {
		$store_protocol_basic = db('store_protocol_basic')->order('id asc')->paginate(10,false,[
		     'type' => 'bootstrap',
		     'var_page' => 'page',
		]);
		$count = db('store_protocol_basic')->count();
      	$this->assign('store_protocol_basic',$store_protocol_basic);
		$this->assign('count',$count);
		return $this->fetch('protocol_list');
    }

    /**
     * @param $id
     * @return string
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function look($id)
    {
        $where= db('store_protocol_basic')->where('id',$id)->field('contract_id')->find();
		$data = db('store_protocol_basic')->where($where)->find();
		$this->view->assign('data',$data);
		
		$dataRental = db('store_protocol_rental')->where($where)->find();
		$this->view->assign('dataRental',$dataRental);
		
		$dataService = db('store_protocol_service')->where($where)->find();
		$this->view->assign('dataService',$dataService);
        return $this->view->fetch('protocol_look');
    }

    /**
     * @param $contract_id
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function valid($contract_id)
    {
        $contractCheck = db('store_protocol_basic')->where('contract_id',$contract_id)->find();
        if(!empty($contractCheck)){
            $result = "合同编号已存在!";
		    return $result;
        } else if(empty($contractCheck)){
            $result = "";
		    return $result;
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function create(Request $request)
    {
        $mca = $request->module().'/'.$request->controller().'/'.$request->action();
        if(in_array($mca,parent::certify())){
            return $this->fetch('protocol_add');
        } else {
            $this->error('无操作权限','index','',3);
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return string
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function edit(Request $request,$id)
    {
        $mca = $request->module().'/'.$request->controller().'/'.$request->action();
        if(in_array($mca,parent::certify())){
            $where= db('store_protocol_basic')->where('id',$id)->field('contract_id')->find();
            $data = db('store_protocol_basic')->where('contract_id',$where['contract_id'])->find();
            $this->view->assign('data',$data);
            //$dataRental若为空
            $dataRental = db('store_protocol_rental')->where('contract_id',$where['contract_id'])->find();
            if(is_null($dataRental)){
                $dataR = [
                    'contract_id'=>$where['contract_id'],
                    'renting_style'=>'按商铺营业额百分比',
                    'detail1'=>0,
                    'free'=>0,
                    'detail2'=>0,
                    'des2'=>0
                ];
                db('store_protocol_rental')->insert($dataR);
            }
            $this->view->assign('dataRental',$dataRental);
            ////$dataService若为空
            $dataService = db('store_protocol_service')->where('contract_id',$where['contract_id'])->find();
            if(is_null($dataService)){
                $dataS = [
                    'contract_id'=>$where['contract_id'],
                    'standard'=>0,
                    'service_rate'=>'0%',
                    'month_service'=>0,
                    'des3'=>0
                ];
                db('store_protocol_service')->insert($dataS);
            }
            $this->view->assign('dataService',$dataService);
            return $this->view->fetch('protocol_edit');
        } else {
            $this->error('无操作权限','index','',3);
        }
    }


    /**
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function search()
    {
        $contract_id = input('contract_id');
		$leasee = input('leasee');
		$where['contract_id'] = array('like','%'.$contract_id.'%');
	    $where['leasee'] = array('like','%'.$leasee.'%');
        $store_protocol_basic = db('store_protocol_basic')
						      ->where([
						      'contract_id'=>$where['contract_id'],
					          'leasee'=>$where['leasee'],
						       ])
	                          ->paginate(10,false,['query'=> request()->param()]);
	    $count = db('store_protocol_basic')
						      ->where([
						      'contract_id'=>$where['contract_id'],
					          'leasee'=>$where['leasee'],
						       ])
				             ->count();
        $this->view->assign('store_protocol_basic',$store_protocol_basic);
        $this->view->assign('count',$count);
        return $this->fetch('protocol_list');
    }

    /**
     * @param Request $request
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function save(Request $request)
    {   
		if($request->isPost()){
			 //true包含上传文件
			$data = $request->param(true);
            $where = db('store_protocol_basic')->where('contract_id',$data['contract_id'])->find();
			$where_contract = db('store_contract')->where('contract_id',$data['contract_id'])->find();
            if(empty($where)){
			if(!empty($where_contract)){
			$data['status'] = 0;
			$data1 = [
			    'contract_id'=>$data['contract_id'],
				'leaser'=>$data['leaser'],
				'leasee'=>$data['leasee'],
				'square'=>$data['square'],
				'retail_format'=>$data['retail_format'],
				'brand'=>$data['brand'],
				'deposit'=>$data['deposit'],
				'expiry'=>$data['expiry'],
				'startdate'=>$data['startdate'],
				'enddate'=>$data['enddate'],
				'status'=>$data['status'],
				'des1'=>$data['des1']				
			];
			$data2 = [
			    'contract_id'=>$data['contract_id'],
				'renting_style'=>$data['renting_style'],
				'detail1'=>$data['detail1'],
				'free'=>$data['free'],
				'detail2'=>$data['detail2'],
				'des2'=>$data['des2']
			];
			$data3 = [
			    'contract_id'=>$data['contract_id'],
				'standard'=>$data['standard'],
				'service_rate'=>$data['service_rate'],
				'month_service'=>$data['month_service'],
				'des3'=>$data['des3']
			];
			$res1 = db('store_protocol_basic')->insert($data1);		
			$res2 = db('store_protocol_rental')->insert($data2);
			$res3 = db('store_protocol_service')->insert($data3);
			if((is_null($res1)&&is_null($res2))&&is_null($res3)){
			$this->error('新增失败','index',3);
		}
			$this->success('新增成功','index',3);
		} else {
			$this->error('请先添加该合同的基础信息!','index',3);
		}
        } else {
            $this->error('数据库已存在该合同编号，请修改!','index',3);
        }
		} else {
			$this->error('请求类型错误!','index',3);
		}
    }

    /**
     * @throws \think\Exception
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function update()
    {
        		//获取提交过来的所有数据包括文件
        $data = $this->request->param(true);
		$where= db('store_protocol_basic')->where('id',$data['id'])->field('contract_id')->find();
		$whereStatus= db('store_protocol_basic')->where('id',$data['id'])->field('status')->find();
        if($whereStatus['status']==0){
		$res1 = db('store_protocol_basic')
		          ->where('contract_id',$where['contract_id'])
		          ->update([
				    'leaser'=>$data['leaser'],
				    'leasee'=>$data['leasee'],
				    'square'=>$data['square'],
				    'retail_format'=>$data['retail_format'],
				    'brand'=>$data['brand'],
				    'deposit'=>$data['deposit'],
				    'expiry'=>$data['expiry'],
				    'startdate'=>$data['startdate'],
				    'enddate'=>$data['enddate'],
				    'des1'=>$data['des1']		
		        ]);
		$res2 = db('store_protocol_rental')
		          ->where('contract_id',$where['contract_id'])
		          ->update([
				    'renting_style'=>$data['renting_style'],
				    'detail1'=>$data['detail1'],
			      	'free'=>$data['free'],
				    'detail2'=>$data['detail2'],
			        'des2'=>$data['des2']
		        ]);
        $res3 = db('store_protocol_service')
		          ->where('contract_id',$where['contract_id'])
		          ->update([
				    'standard'=>$data['standard'],
				    'service_rate'=>$data['service_rate'],
			     	'month_service'=>$data['month_service'],
				    'des3'=>$data['des3']
		        ]);					
		if((is_null($res1)&&is_null($res2))&&is_null($res3)){
			$this->error('更新失败','index',3);
			
		} else {
			$this->success('更新成功','index',3);
		}
    } else {
		$this->error('更新失败,只有未审核合同才可以修改!','index',3);
	}
	}
	
}