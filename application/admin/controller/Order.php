<?php

namespace app\admin\controller;
use app\admin\common\Base;
use think\Request;
//use app\admin\model\Order as OrderModel;

class order extends Base
{
    /**
     *
     *
     * @return \think\Response
     * @throws \think\exception\DbException
     */
    public function index()
    {
		$store_contract = db('store_contract')->paginate(10,false,[
		     'type' => 'bootstrap',
		     'var_page' => 'page',
		]);
		$count = db('store_contract')->count();
      	$this->assign('store_contract',$store_contract);
		$this->assign('count',$count);
		return $this->fetch('order_list');
    }

    /**
     *
     * @param Request $request
     * @return mixed
     */
    public function create(Request $request)
    {
        $mca = $request->module().'/'.$request->controller().'/'.$request->action();
        if(in_array($mca,parent::certify())){
            return $this->fetch('order_add');
        } else {
            $this->error('无操作权限','index','',3);
        }
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
        $contractCheck = db('store_contract')->where('contract_id',$contract_id)->find();
        if(!empty($contractCheck)){
            $result = "合同编号已存在!";
		    return $result;
        } else if(empty($contractCheck)){
            $result = "";
		    return $result;
        }
    }

    /**
     *
     *
     * @param  \think\Request $request
     * @return void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function save(Request $request)
    {
		 if($request->isPost()){
			 //true包含上传文件
			$data = $request->param(true);
            $where = db('store_contract')->where('contract_id',$data['contract_id'])->find();
            if(empty($where)){
			//获取上传的文件对象
			$file = $this->request->file('upload');
			//判断$file是否为空
			if(empty($file)){
				$this->error($file->getError());
			}
			
			$info = $file->move(ROOT_PATH.'public'.DS.'uploads');
			if(is_null($info)){
				$this->error($file->getError());
			} 
            $data['status'] = 0;
			$data['upload'] = $info->getSaveName();
			$res = db('store_contract')->insert($data);
			
			if(is_null($res)){
			$this->error('新增失败','index',3);
		}
			$this->success('新增成功','index',3);
		
        } else {
            $this->error('数据库已存在该合同编号，请修改!','index',3);
        }
	
		} else {
			$this->error('请求类型错误','index',3);
			
		}
    }

    /**
     *
     *
     * @return \think\Response
     * @throws \think\exception\DbException
     */
	public function search()
    {		
	    $status = input('status');
        $payment_method = input('payment_method');
        $retail_format = input('retail_format');
        $company_name = input('company_name');
        $where['status'] = array('like','%'.$status.'%');
	 	$where['payment_method'] = array('like','%'.$payment_method.'%');
	    $where['retail_format'] = array('like','%'.$retail_format.'%');
		$where['company_name'] = array('like','%'.$company_name.'%');
	    //paginate的第三个参数：
        $store_contract = db('store_contract')
						->where([
						'status'=>$where['status'],
						'payment_method'=>$where['payment_method'],
						'retail_format'=>$where['retail_format'],
						'company_name'=>$where['company_name']
						])
		                ->paginate(10,false,['query'=> request()->param()]);
	    $count = db('store_contract')
						->where([
						'status'=>$where['status'],
						'payment_method'=>$where['payment_method'],
						'retail_format'=>$where['retail_format'],
						'company_name'=>$where['company_name']
						])
		                ->count();
        $this->view->assign('store_contract',$store_contract);
        $this->view->assign('count',$count);
        return $this->fetch('order_list');
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
            $data = db('store_contract')->where('id',$id)->find();
            $this->view->assign('data',$data);
            return $this->view->fetch('order_edit');
        } else {
            $this->error('无操作权限','index','',3);
        }
    }

    /**
     *
     *
     * @return void
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function update()
    {
        		//获取提交过来的所有数据包括文件
        $data = $this->request->param(true);
		$file = $this->request->file('upload');
		if(empty($file)){
				$this->error($file->getError());
			}
			
			$info = $file->move(ROOT_PATH.'public'.DS.'uploads');
			if(is_null($info)){
				$this->error($file->getError());
			} 
		$res = db('store_contract')
		          ->where('id',$data['id'])
		          ->update([
		            'contract_id'=>$data['contract_id'],
		            'type'=>$data['type'],
		            'name'=>$data['name'],
		            'company_name'=>$data['company_name'],
		            'code'=>$data['code'],
		            'belonger'=>$data['belonger'],
		            'brand'=>$data['brand'],
		            'retail_format'=>$data['retail_format'],
		            'berth_number'=>$data['berth_number'],
		            'payment_method'=>$data['payment_method'],
		            'startdate'=>$data['startdate'],
		            'enddate'=>$data['enddate'],
					'upload'=>$info->getSaveName(),
		            'description'=>$data['description']
		            ]);
		if(is_null($res)){
			$this->error('更新失败','index',3);
			
		}
			$this->success('更新成功','index',3);
    }

    /**
     *
     *
     * @param  int $id
     * @return void
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function delete($id)
    {
        db('store_contract')->where('id',$id)->delete();
    }
}