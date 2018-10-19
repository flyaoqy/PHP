<?php

namespace app\admin\controller;
use app\admin\common\Base;
use think\Request;

class Store extends Base
{
    /**
     * 显示会员列表
     *
     * @return \think\Response
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function index()
    {
		$store_info = db('store_info')
		                ->paginate(10,false,[
		               'type' => 'bootstrap',
		               'var_page' => 'page',
		]);
		$count = db('store_info')->count();
		$this->assign('store_info',$store_info);
		$this->assign('count',$count);
		return $this->fetch('store_list');
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function create(Request $request)
    {
        $mca = $request->module().'/'.$request->controller().'/'.$request->action();
        if(in_array($mca,parent::certify())){
            return $this->fetch('store_add');
        } else {
            $this->error('无操作权限','index','',3);
        }
    }

    /**
     * @param $berth_code
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function valid($berth_code)
    {
        $storeCode= db('store_info')->where('berth_code',$berth_code)->find();
        if(!empty($storeCode)){
            $result = "商铺代码已存在!";
		    return $result;
        } else if(empty($storeCode)){
            $result = "";
		    return $result;
        }
    }
	
    public function select($name)
    {    
	    
		if($name=="禹洲中央广场"){
		    $result = '[{"name":"floor","value":"-1F"},{"name":"floor","value":"1F"},{"name":"floor","value":"2F"},{"name":"floor","value":"3F"},{"name":"floor","value":"4F"}]';
            return $result;

		} else if($name=="禹洲风情街"){
			$result = '[{"name":"floor","value":"1F"},{"name":"floor","value":"2F"},{"name":"floor","value":"3F"},{"name":"floor","value":"4F"},{"name":"floor","value":"5F"},{"name":"floor","value":"6F"}]';
			return $result;
		}

    }

    /**
     * 保存新建的资源
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
			$where = db('store_info')->where('berth_code',$data['berth_code'])->find();
			if(empty($where)){
			$data = [
			    'berth_code'=>$data['berth_code'],
				'berth_name'=>$data['berth_name'],
				'building_number'=>$data['building_number'],
				'floor'=>$data['floor'],
				'floor_area'=>$data['floor_area'],
				'utilization_area'=>$data['utilization_area'],
				'billing_area'=>$data['billing_area'],
				'reference_rent'=>$data['reference_rent'],
                'contract_date'=>$data['contract_date'],
				'description'=>$data['description'],
				'status'=>0,
				'operate_time'=>date('Y-m-d H:i:s',time())
			];

			$res = db('store_info')->insert($data);		

			if(is_null($res)){
			$this->error('新增失败','index',3);
		}
			$this->success('新增成功','index',3);
        } else {
            $this->error('数据库已存在该合同编号，请修改!','index',3);
        }	
		} else {
			$this->error('请求类型错误!','index',3);
		}
		
    }

    /**
     * 显示指定的资源
     *
     * @return \think\Response
     * @throws \think\exception\DbException
     */
     public function search()
    {
        $status = input('status');
        $berth_name = input('berth_name');
        $where['status'] = array('like','%'.$status.'%');
		$where['berth_name'] = array('like','%'.$berth_name.'%');
        $store_info = db('store_info')
						->where([
						'status'=>$where['status'],
						'berth_name'=>$where['berth_name']
						])
		                ->paginate(10,false,['query'=> request()->param()]);
	    $count = db('store_info')
						->where([
						'status'=>$where['status'],
						'berth_name'=>$where['berth_name']
						])
		                ->count();
        $this->view->assign('store_info',$store_info);
        $this->view->assign('count',$count);
        return $this->fetch('store_list');
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param Request $request
     * @param  int $id
     * @return \think\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function edit(Request $request,$id)
    {
        $mca = $request->module().'/'.$request->controller().'/'.$request->action();
        if(in_array($mca,parent::certify())){
            $data = db('store_info')->where('id',$id)->find();
            $this->view->assign('data',$data);
            return $this->fetch('store_edit');
        } else {
            $this->error('无操作权限','index','',3);
        }
    }

    /**
     * 保存更新的资源
     *
     * @return void
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function update()
    {  
		$data = $this->request->param(true);
		$res = db('store_info')
		          ->where('id',$data['id'])
		          ->update([
				    'berth_code'=>$data['berth_code'],
				    'berth_name'=>$data['berth_name'],
                    'building_number'=>$data['building_number'],
					'floor'=>$data['floor'],
                    'floor_area'=>$data['floor_area'],
					'utilization_area'=>$data['utilization_area'],
					'billing_area'=>$data['billing_area'],
                    'reference_rent'=>$data['reference_rent'],
					'status'=>$data['status'],
					'contract_date'=>$data['contract_date'],
                    'description'=>$data['description']
		        ]);
				
		if(is_null($res)){
			$this->error('更新失败','index',3);
			
		} else {
			$this->success('更新成功','index',3);
		}
    }

    /**
     * 删除指定资源
     *
     * @param Request $request
     * @param  int $id
     * @return void
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function delete(Request $request,$id)
    {
        $mca = $request->module().'/'.$request->controller().'/'.$request->action();
        if(in_array($mca,parent::certify())){
            db('store_info')->where('id',$id)->delete();
        } else {
            $this->error('无操作权限','index','',3);
        }
    }
	
	public function stop($id)
    {
        
    }
	public function start($id)
    {
        
    }
}